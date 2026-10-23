FIX INVOICE DAN KWITANSI INVENTORY-SC

Fokus:
- Hanya resources/views/admin/pdf/invoice.blade.php dan kwitansi.blade.php.
- Jika tax_amount > 0, harga tampilan invoice di-gross-up dengan rumus nilai / (1 - TAX_RATE).
- Baris PPh 23 menjadi potongan, nominal ditampilkan positif tanpa tanda minus.
- Total netto dan amount paid = order.total_price (nilai normal yang disepakati).
- Kwitansi menampilkan Total Bruto, PPh 23 (Potongan), dan Amount Paid netto.
- Tidak mengubah cart, app/Models/Order.php, MoU, CSS, atau page-break.

Cara pakai:
1. Extract ZIP ke folder Downloads.
2. Buka Terminal dan masuk ke root project, yaitu folder yang memiliki file artisan.
3. Dry-run dulu:
   python3 Inventory_SC_Invoice_Kwitansi_Netto_Fix/apply_invoice_kwitansi_netto_fix.py
4. Baca preview diff. Jika VALIDATION PASSED dan perubahan sesuai, baru jalankan:
   python3 Inventory_SC_Invoice_Kwitansi_Netto_Fix/apply_invoice_kwitansi_netto_fix.py --apply
5. Cek:
   git diff --check
   git diff
6. Buat PDF uji invoice dan kwitansi.

Script membuat backup ke /tmp sebelum menerapkan perubahan. Default tanpa --apply hanya dry-run.
