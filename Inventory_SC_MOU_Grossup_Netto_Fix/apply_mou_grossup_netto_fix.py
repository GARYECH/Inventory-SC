#!/usr/bin/env python3
"""Safely patch the five Inventory-SC MoU PDF Blade templates.

Default mode is dry-run. Pass --apply to write the changes.
Only MoU PDF Blade templates are edited; cart, Order.php, invoice, receipt,
CSS, margins, and page-break rules are intentionally left unchanged.
"""
from __future__ import annotations

import argparse
import difflib
import re
import shutil
import sys
from datetime import datetime
from pathlib import Path

FILES = {
    "resources/views/admin/pdf/mou_internal.blade.php": "grandTotal",
    "resources/views/admin/pdf/mou_HT.blade.php": "htTotal",
    "resources/views/admin/pdf/mou_merch_baju.blade.php": "grandTotal",
    "resources/views/admin/pdf/mou_vendor.blade.php": "grandTotal",
    "resources/views/admin/pdf/mou_merch_idcard.blade.php": "grandTotal",
}

# Expected number of display-only amounts to gross-up in each MOU template.
EXPECTED_MONEY_REPLACEMENTS = {
    "resources/views/admin/pdf/mou_internal.blade.php": 2,
    "resources/views/admin/pdf/mou_HT.blade.php": 7,
    "resources/views/admin/pdf/mou_merch_baju.blade.php": 2,
    "resources/views/admin/pdf/mou_vendor.blade.php": 2,
    "resources/views/admin/pdf/mou_merch_idcard.blade.php": 2,
}
EXPECTED_UNIT_FORMULAS = {
    "resources/views/admin/pdf/mou_internal.blade.php": 1,
    "resources/views/admin/pdf/mou_HT.blade.php": 2,
    "resources/views/admin/pdf/mou_merch_baju.blade.php": 0,
    "resources/views/admin/pdf/mou_vendor.blade.php": 1,
    "resources/views/admin/pdf/mou_merch_idcard.blade.php": 0,
}

MONEY_ARG_RE = re.compile(
    r"(\$money\(\s*)(\$unitPrice|\$subtotal|\$htSubtotal|\$htFee|"
    r"\$transportFee|\$row\['unit_price'\]|\$row\['subtotal_price'\])"
    r"(\s*\))"
)
UNIT_FORMULA_RE = re.compile(r"(number_format\(\s*)\$unitPrice(\s*,)")


def fail(message: str) -> None:
    raise ValueError(message)


def patch_one(path: str, original: str, base_var: str) -> str:
    text = original

    # Add a scoped display-only gross-up helper after the existing MOU tax
    # allocation. The helper deliberately returns normal prices when tax is
    # not applicable to this order/MOU.
    tax_line = re.compile(
        rf"(?m)^(?P<indent>[ \t]*)\$mouTaxAmount = "
        rf"\$order->getMouTaxAmount\(\(int\) \${base_var}\);[ \t]*$"
    )
    matches = list(tax_line.finditer(text))
    if len(matches) != 1:
        fail(f"{path}: expected one tax-allocation line for ${base_var}, found {len(matches)}")
    indent = matches[0].group("indent")
    helper = (
        f"{indent}$mouGrossUp = function ($value) use ($mouTaxAmount) {{\n"
        f"{indent}    $amount = (int) $value;\n"
        f"{indent}    if ($amount <= 0 || $mouTaxAmount <= 0) {{\n"
        f"{indent}        return $amount;\n"
        f"{indent}    }}\n"
        f"{indent}    return (int) round(\n"
        f"{indent}        $amount / (1 - \\App\\Models\\Order::TAX_RATE)\n"
        f"{indent}    );\n"
        f"{indent}}};"
    )
    text = tax_line.sub(lambda m: m.group(0) + "\n\n" + helper, text, count=1)

    # The final amount written in the legal paragraph and the bottom row is
    # the agreed NET price, not the grossed-up display amount.
    total_assignment = re.compile(
        rf"(?m)^(?P<indent>[ \t]*)\$mouGrandTotal = "
        rf"\(int\) \${base_var} \+ \$mouTaxAmount;[ \t]*$"
    )
    total_matches = list(total_assignment.finditer(text))
    if len(total_matches) != 1:
        fail(f"{path}: expected one gross total assignment for ${base_var}, found {len(total_matches)}")
    text = total_assignment.sub(
        lambda m: f"{m.group('indent')}$mouGrandTotal = (int) ${base_var};",
        text,
        count=1,
    )

    # Gross-up visible unit prices, item subtotals, and HT fee summaries only.
    # Source data and all underlying cart/order calculations remain unchanged.
    text, money_count = MONEY_ARG_RE.subn(
        lambda m: m.group(1) + "$mouGrossUp(" + m.group(2) + ")" + m.group(3),
        text,
    )
    expected_money = EXPECTED_MONEY_REPLACEMENTS[path]
    if money_count != expected_money:
        fail(f"{path}: expected {expected_money} display-price expressions to gross-up, found {money_count}")

    text, formula_count = UNIT_FORMULA_RE.subn(
        lambda m: m.group(1) + "$mouGrossUp($unitPrice)" + m.group(2),
        text,
    )
    expected_formulas = EXPECTED_UNIT_FORMULAS[path]
    if formula_count != expected_formulas:
        fail(f"{path}: expected {expected_formulas} unit-price formula(s), found {formula_count}")

    # Change only the PPh row: show the withholding as a deduction.
    tax_row_pattern = re.compile(r"@if\(\$mouTaxAmount > 0\)(.*?)@endif", re.S)
    tax_rows = list(tax_row_pattern.finditer(text))
    if len(tax_rows) != 1:
        fail(f"{path}: expected one MOU tax row, found {len(tax_rows)}")
    row = tax_rows[0].group(0)
    if row.count("PPh 23 2% (Gross-up)") != 1:
        fail(f"{path}: original tax label not found exactly once")
    row = row.replace("PPh 23 2% (Gross-up)", "PPh 23 2% (Potongan)", 1)
    tax_value = re.compile(r"\{\{\s*\$money\(\$mouTaxAmount\)\s*\}\}")
    row, tax_value_count = tax_value.subn("− {{ $money($mouTaxAmount) }}", row, count=1)
    if tax_value_count != 1:
        fail(f"{path}: expected one tax amount in PPh row, found {tax_value_count}")
    text = text[:tax_rows[0].start()] + row + text[tax_rows[0].end():]

    # Relabel the first Total row following the PPh row. This is a display label
    # only; the template's spacing, table structure, and page breaks are kept.
    after_tax = tax_rows[0].end() + (len(row) - len(tax_rows[0].group(0)))
    tail = text[after_tax:]
    row_matches = list(re.finditer(r"<tr\b.*?</tr>", tail, re.S))
    target = None
    for match in row_matches:
        if re.search(r"<strong>\s*Total\s*</strong>", match.group(0)):
            target = match
            break
    if target is None:
        fail(f"{path}: could not find the Total row after the tax row")
    total_row = target.group(0)
    total_row_new, label_count = re.subn(
        r"<strong>\s*Total\s*</strong>",
        "<strong>Total Netto</strong>",
        total_row,
        count=1,
    )
    if label_count != 1:
        fail(f"{path}: could not safely relabel the Total row")
    text = text[:after_tax + target.start()] + total_row_new + text[after_tax + target.end():]

    return text


def main() -> int:
    parser = argparse.ArgumentParser(description="Patch MOU gross-up display and net totals safely.")
    parser.add_argument("--apply", action="store_true", help="write the patch after validation; default is dry-run")
    args = parser.parse_args()

    root = Path.cwd()
    if not (root / "artisan").is_file():
        print("ERROR: jalankan dari root project Laravel (folder yang berisi file artisan).", file=sys.stderr)
        return 2

    original_by_path: dict[str, str] = {}
    patched_by_path: dict[str, str] = {}
    try:
        for relative_path, base_var in FILES.items():
            file_path = root / relative_path
            if not file_path.is_file():
                fail(f"File tidak ditemukan: {relative_path}")
            original = file_path.read_text(encoding="utf-8")
            original_by_path[relative_path] = original
            patched_by_path[relative_path] = patch_one(relative_path, original, base_var)

        # Ensure the changes did not touch page-break/CSS definitions.
        for relative_path, before in original_by_path.items():
            after = patched_by_path[relative_path]
            for marker in ("@page", "page-break-after", ".page {"):
                if before.count(marker) != after.count(marker):
                    fail(f"{relative_path}: marker CSS/page-break {marker!r} changed unexpectedly")

    except (OSError, UnicodeError, ValueError) as exc:
        print(f"VALIDATION FAILED: {exc}", file=sys.stderr)
        print("Tidak ada file project yang diubah.", file=sys.stderr)
        return 1

    print("VALIDATION PASSED. Changes are limited to these five MOU PDF templates:")
    print("- Gross-up display values for unit prices, item subtotals, and HT fee totals.")
    print("- PPh row becomes a deduction; final Total Netto and legal amount use the original base value.")
    print("- No changes to cart, Order.php, invoice, kwitansi, CSS, or page breaks.")

    for relative_path in FILES:
        diff = difflib.unified_diff(
            original_by_path[relative_path].splitlines(keepends=True),
            patched_by_path[relative_path].splitlines(keepends=True),
            fromfile=relative_path,
            tofile=relative_path + " (proposed)",
        )
        sys.stdout.writelines(diff)

    if not args.apply:
        print("\nDRY RUN ONLY: no files changed. Add --apply after reviewing the diff.")
        return 0

    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    backup_root = Path("/tmp") / f"Inventory_SC_MOU_Grossup_Netto_Backup_{timestamp}"
    try:
        backup_root.mkdir(parents=True, exist_ok=False)
        for relative_path, original in original_by_path.items():
            backup_path = backup_root / relative_path
            backup_path.parent.mkdir(parents=True, exist_ok=True)
            backup_path.write_text(original, encoding="utf-8")
        for relative_path, updated in patched_by_path.items():
            (root / relative_path).write_text(updated, encoding="utf-8")
    except OSError as exc:
        print(f"WRITE FAILED: {exc}", file=sys.stderr)
        print(f"Backup (jika sudah dibuat): {backup_root}", file=sys.stderr)
        return 1

    print(f"\nPATCH APPLIED. Backup asli disimpan di: {backup_root}")
    print("Langkah berikutnya: jalankan git diff --check lalu periksa git diff sebelum membuat PDF uji.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
