#!/usr/bin/env python3
"""Safely update only Inventory-SC invoice and kwitansi PDF Blade views.

Default mode is dry-run. Use --apply to write changes after reviewing the diff.
The cart, Order model, MoU templates, CSS, and page-break rules are not edited.
"""
from __future__ import annotations

import argparse
import difflib
import re
import shutil
import sys
from datetime import datetime
from pathlib import Path

FILES = [
    "resources/views/admin/pdf/invoice.blade.php",
    "resources/views/admin/pdf/kwitansi.blade.php",
]


def fail(message: str) -> None:
    raise ValueError(message)


def replace_exact(text: str, old: str, new: str, expected: int, description: str) -> str:
    actual = text.count(old)
    if actual != expected:
        fail(f"{description}: expected {expected} exact match(es), found {actual}")
    return text.replace(old, new)


def replace_regex(text: str, pattern: str, replacement: str, expected: int, description: str) -> str:
    updated, count = re.subn(pattern, replacement, text, flags=re.S)
    if count != expected:
        fail(f"{description}: expected {expected} match(es), found {count}")
    return updated


def preserve_style_and_breaks(before: str, after: str, path: str) -> None:
    def styles(value: str) -> list[str]:
        return re.findall(r"<style\b[^>]*>.*?</style>", value, flags=re.S | re.I)

    if styles(before) != styles(after):
        fail(f"{path}: CSS/style block changed unexpectedly")
    for marker in ("@page", "page-break-after", "page-break-before", "page-break-inside"):
        if before.count(marker) != after.count(marker):
            fail(f"{path}: pagination marker {marker!r} changed unexpectedly")


def patch_invoice(text: str) -> str:
    # Calculate a display-only gross-up, and show a net amount in the final total.
    old_total = """    $total =
        (int) $order->grand_total_price;"""
    new_total = """    $grossTotal = (int) $order->grand_total_price;
    $hasWithholdingTax = (int) $order->tax_amount > 0;

    $total = $hasWithholdingTax
        ? (int) $order->total_price
        : $grossTotal;

    $grossUpDisplay = function ($value) use ($hasWithholdingTax) {
        $amount = (int) $value;

        if (!$hasWithholdingTax || $amount <= 0) {
            return $amount;
        }

        return (int) round(
            $amount / (1 - \\App\\Models\\Order::TAX_RATE)
        );
    };"""
    text = replace_exact(text, old_total, new_total, 1, "Invoice total/display helper")

    # Display item prices and item subtotals grossed-up; underlying amounts stay unchanged.
    text = replace_regex(
        text,
        r"(number_format\(\s*)\$displayUnitPrice(\s*,)",
        r"\1$grossUpDisplay($displayUnitPrice)\2",
        2,
        "Invoice unit price and rental formula",
    )
    text = replace_regex(
        text,
        r"(number_format\(\s*)\$subtotal(\s*,)",
        r"\1$grossUpDisplay($subtotal)\2",
        1,
        "Invoice item subtotal",
    )
    text = replace_regex(
        text,
        r"(number_format\(\s*)\$itemSubtotal(\s*,)",
        r"\1$grossUpDisplay($itemSubtotal)\2",
        1,
        "Invoice summary subtotal",
    )
    text = replace_regex(
        text,
        r"(number_format\(\s*)\$htFee(\s*,)",
        r"\1$grossUpDisplay($htFee)\2",
        1,
        "Invoice HT fee",
    )
    text = replace_regex(
        text,
        r"(number_format\(\s*)\$transportFee(\s*,)",
        r"\1$grossUpDisplay($transportFee)\2",
        1,
        "Invoice transport fee",
    )

    old_label = "Tax (PPh 23 {{ (int) (100 * \\App\\Models\\Order::TAX_RATE) }}% - Gross-up)"
    new_label = "PPh 23 {{ (int) (100 * \\App\\Models\\Order::TAX_RATE) }}% (Potongan)"
    text = replace_exact(text, old_label, new_label, 1, "Invoice PPh label")

    # Label only the final net figure; tax amount is shown as an ordinary positive amount
    # because the row label itself describes it as withholding.
    total_marker = "<!-- TOTAL -->"
    if text.count(total_marker) != 1:
        fail("Invoice: expected exactly one TOTAL section marker")
    section_start = text.index(total_marker)
    tbody_end = text.find("</tbody>", section_start)
    if tbody_end < 0:
        fail("Invoice: could not locate end of totals table")
    section = text[section_start:tbody_end]
    section = replace_regex(
        section,
        r"(?m)^(?P<indent>[ \t]*)Total[ \t]*$",
        r"\g<indent>Total Netto",
        1,
        "Invoice final total label",
    )
    text = text[:section_start] + section + text[tbody_end:]
    return text


def patch_kwitansi(text: str) -> str:
    old_words = "$amountInWords = numberToWords($order->grand_total_price) . ' Rupiah';"
    new_words = """$grossTotal = (int) $order->grand_total_price;
        $netAmount = (int) $order->tax_amount > 0
            ? (int) $order->total_price
            : $grossTotal;
        $amountInWords = numberToWords($netAmount) . ' Rupiah';"""
    text = replace_exact(text, old_words, new_words, 1, "Kwitansi amount in words/net amount")

    # The main receipt Total and Amount Paid must refer to net money actually paid.
    grand_expr = "number_format($order->grand_total_price, 0, ',', '.')"
    text = replace_exact(text, grand_expr, "number_format($netAmount, 0, ',', '.')", 2, "Kwitansi net amount displays")

    # The detail table first shows the gross-up value, then the withholding, then net paid.
    subtotal_label = '<td style="width: 40%; padding-left: 10px;">Subtotal</td>'
    text = replace_exact(
        text,
        subtotal_label,
        '<td style="width: 40%; padding-left: 10px;">Total Bruto</td>',
        1,
        "Kwitansi gross total label",
    )
    base_expr = "number_format($order->total_price, 0, ',', '.')"
    text = replace_exact(text, base_expr, "number_format($grossTotal, 0, ',', '.')", 1, "Kwitansi gross total value")

    old_tax_label = "PPh 21 ({{ (int) (100 * \\App\\Models\\Order::TAX_RATE) }}%) - Gross-up"
    new_tax_label = "PPh 23 ({{ (int) (100 * \\App\\Models\\Order::TAX_RATE) }}%) - Potongan"
    text = replace_exact(text, old_tax_label, new_tax_label, 1, "Kwitansi PPh label")

    # Clarify that the receipt's primary total is the net amount received.
    text = replace_exact(
        text,
        '<td style="padding-left: 10px;">Total</td>',
        '<td style="padding-left: 10px;">Total Netto</td>',
        1,
        "Kwitansi main total label",
    )
    return text


def main() -> int:
    parser = argparse.ArgumentParser(description="Fix invoice and kwitansi gross-up/net display safely.")
    parser.add_argument("--apply", action="store_true", help="write changes after validation; default is dry-run")
    args = parser.parse_args()
    root = Path.cwd()
    if not (root / "artisan").is_file():
        print("ERROR: Jalankan dari root project Laravel (folder yang berisi file artisan).", file=sys.stderr)
        return 2

    originals: dict[str, str] = {}
    patched: dict[str, str] = {}
    try:
        for relative in FILES:
            path = root / relative
            if not path.is_file():
                fail(f"File tidak ditemukan: {relative}")
            before = path.read_text(encoding="utf-8")
            after = patch_invoice(before) if relative.endswith("invoice.blade.php") else patch_kwitansi(before)
            preserve_style_and_breaks(before, after, relative)
            originals[relative] = before
            patched[relative] = after
    except (OSError, UnicodeError, ValueError) as exc:
        print(f"VALIDATION FAILED: {exc}", file=sys.stderr)
        print("Tidak ada file project yang diubah.", file=sys.stderr)
        return 1

    print("VALIDATION PASSED. Only invoice and kwitansi Blade templates will change:")
    print("- Invoice line values and subtotal/fees display gross-up values when tax applies.")
    print("- PPh 23 is shown as a positive withholding row (no minus sign).")
    print("- Final invoice total and kwitansi paid amount remain net (base order value).")
    print("- Cart, Order.php, MoU templates, CSS, and page breaks are untouched.\n")
    for relative in FILES:
        sys.stdout.writelines(difflib.unified_diff(
            originals[relative].splitlines(keepends=True),
            patched[relative].splitlines(keepends=True),
            fromfile=relative,
            tofile=relative + " (proposed)",
        ))

    if not args.apply:
        print("\nDRY RUN ONLY: no files changed. Add --apply only after reviewing the diff.")
        return 0

    backup_root = Path("/tmp") / (
        "Inventory_SC_Invoice_Kwitansi_Backup_" + datetime.now().strftime("%Y%m%d_%H%M%S")
    )
    try:
        backup_root.mkdir(parents=True, exist_ok=False)
        for relative, content in originals.items():
            backup_path = backup_root / relative
            backup_path.parent.mkdir(parents=True, exist_ok=True)
            backup_path.write_text(content, encoding="utf-8")
        for relative, content in patched.items():
            (root / relative).write_text(content, encoding="utf-8")
    except OSError as exc:
        print(f"WRITE FAILED: {exc}", file=sys.stderr)
        print(f"Backup (jika sudah dibuat): {backup_root}", file=sys.stderr)
        return 1

    print(f"\nPATCH APPLIED. Backup asli disimpan di: {backup_root}")
    print("Langkah berikutnya: jalankan git diff --check dan periksa git diff sebelum membuat PDF uji.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
