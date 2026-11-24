#!/usr/bin/env python3
"""Apply the small integrations needed by the Berita Acara editor overlay."""
from pathlib import Path
import sys

ROOT = Path.cwd()

required = [
    ROOT / "artisan",
    ROOT / "routes/web.php",
    ROOT / "app/Http/Controllers/User/DocumentController.php",
    ROOT / "resources/views/admin/orders/index.blade.php",
]
missing = [str(path.relative_to(ROOT)) for path in required if not path.exists()]
if missing:
    print("ERROR: Jalankan script ini dari root project Laravel. File tidak ditemukan:")
    print("\n".join(f" - {item}" for item in missing))
    sys.exit(1)

routes_path = ROOT / "routes/web.php"
doc_path = ROOT / "app/Http/Controllers/User/DocumentController.php"
orders_path = ROOT / "resources/views/admin/orders/index.blade.php"

routes = routes_path.read_text(encoding="utf-8")
doc_controller = doc_path.read_text(encoding="utf-8")
orders_view = orders_path.read_text(encoding="utf-8")

# 1) Register the additive BA editor route file.
route_anchor = "require_once __DIR__ . '/mou_editor.php';"
route_line = "require_once __DIR__ . '/ba_editor.php';"
if route_line not in routes:
    if route_anchor not in routes:
        print("ERROR: Baris require routes/mou_editor.php tidak ditemukan. Tidak ada file yang diubah.")
        sys.exit(1)
    routes = routes.replace(route_anchor, route_anchor + "\n" + route_line, 1)

# 2) Make the existing generated-BA route render the saved editor HTML, if present.
function_start = doc_controller.find("    public function downloadBeritaAcara(")
if function_start < 0:
    print("ERROR: Method downloadBeritaAcara tidak ditemukan. Tidak ada file yang diubah.")
    sys.exit(1)
function_end = doc_controller.find("\n\n\n    /*", function_start)
if function_end < 0:
    print("ERROR: Batas method downloadBeritaAcara tidak ditemukan. Tidak ada file yang diubah.")
    sys.exit(1)

replacement = '''    public function downloadBeritaAcara(
        Order $order
    ) {
        $this->authorizeOrder($order);

        $order->load([
            'orderItems.item.category',
            'orderItems.sizeBreakdowns',
        ]);

        $rentalDays = $this->getRentalDays(
            $order->start_date,
            $order->end_date
        );

        $html = \\App\\Support\\BaDocumentHtml::forOrder(
            $order,
            $rentalDays
        );

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Berita_Acara_' . $order->order_number . '.pdf'
        );
    }'''
doc_controller = doc_controller[:function_start] + replacement + doc_controller[function_end:]

# 3) Add a direct editor link beside the existing generated BA link.
orders_anchor = "                                        <!-- BERITA ACARA -->"
editor_link = '''                                        <a
                                            href="{{ route('admin.ba-editor.edit', $order->id) }}"
                                            class="mb-2 flex w-full items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-indigo-700 transition hover:bg-indigo-100"
                                        >
                                            Edit Berita Acara
                                        </a>

'''
if "route('admin.ba-editor.edit'" not in orders_view:
    if orders_anchor not in orders_view:
        print("ERROR: Bagian BERITA ACARA pada halaman Orders tidak ditemukan. Tidak ada file yang diubah.")
        sys.exit(1)
    orders_view = orders_view.replace(orders_anchor, editor_link + orders_anchor, 1)

# Write only after all expected anchors were verified.
routes_path.write_text(routes, encoding="utf-8")
doc_path.write_text(doc_controller, encoding="utf-8")
orders_path.write_text(orders_view, encoding="utf-8")

print("Integrasi Berita Acara Editor berhasil diterapkan.")
print("File yang diperbarui:")
print(" - routes/web.php")
print(" - app/Http/Controllers/User/DocumentController.php")
print(" - resources/views/admin/orders/index.blade.php")
print("File baru dari ZIP tetap berada pada folder app/, routes/, resources/, dan database/.")
