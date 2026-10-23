INVENTORY-SC — FIX TAMPILAN GROSS-UP DAN TOTAL NETTO MOU

Tujuan:
- Cart, app/Models/Order.php, invoice, dan kwitansi tidak diubah.
- Khusus lima template MoU PDF: internal, HT, merchandise baju, vendor, dan merchandise ID card.
- Nilai satuan/subtotal ditampilkan gross-up = nilai normal / (1 - tarif pajak).
- Baris PPh 23 ditampilkan sebagai potongan.
- Total netto dan angka terbilang pada MoU tetap menggunakan nilai normal yang disepakati.
- CSS, margin A4, dan page-break tidak diubah.

CARA PAKAI:
1. Extract ZIP ke root project, folder yang sama dengan file artisan.
2. Jalankan dry-run:
   python3 Inventory_SC_MOU_Grossup_Netto_Fix/apply_mou_grossup_netto_fix.py
3. Periksa diff. Kalau VALIDATION PASSED dan perubahan sesuai, terapkan:
   python3 Inventory_SC_MOU_Grossup_Netto_Fix/apply_mou_grossup_netto_fix.py --apply
4. Periksa:
   git diff --check
   git diff
5. Buat ulang PDF MoU untuk uji visual sebelum deploy.

Script akan menolak patch jika file/pola tidak cocok, dan dry-run default tidak mengubah file.
Saat --apply, salinan asli kelima file disimpan di /tmp/Inventory_SC_MOU_Grossup_Netto_Backup_<timestamp>/.

CATATAN:
Ini adalah patch installer (bukan file Blade pengganti utuh). File Blade lokal dipertahankan dan diedit hanya dengan perubahan yang tervalidasi agar tata letak serta perubahan lokal lain tidak hilang.
PPh 23 hanya boleh dipakai untuk transaksi yang memang memenuhi ketentuan perpajakan.
