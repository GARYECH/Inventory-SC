INVENTORY SC — BERITA ACARA EDITOR FIX
=====================================

Isi ZIP ini adalah patch/overlay untuk repository Laravel yang sudah ada,
bukan salinan penuh repository. Ekstrak seluruh isi ZIP langsung ke root
folder Inventory-SC (folder yang berisi file `artisan`). Struktur folder
`app/`, `database/`, `resources/`, dan `routes/` akan digabung ke project.

PERUBAHAN
---------
1. PDF Berita Acara sekarang menampilkan nama Ketua Acara dari field
   `orders.ketua_acara` dan nama Bendahara Proker dari `orders.treasurer_name`.
2. Bagian tanda tangan menyediakan slot untuk tanda tangan Bendahara Proker
   dan Ketua Acara. Tanda tangan Bendahara Student Council di bagian
   "Mengetahui" tetap memakai pengaturan lama `ttd_bendahara`.
3. Kalimat jumlah denda tidak lagi dibuat bold.
4. Halaman Lampiran memiliki slot Bukti Transfer Denda. Gambar PNG/JPG akan
   ditampilkan langsung di halaman PDF; jika file berupa PDF, halaman lampiran
   menampilkan tautan untuk membuka file PDF tersebut.
5. Ada Berita Acara Editor untuk mengubah teks langsung di dokumen dengan
   toolbar (font, ukuran, bold, italic, underline, alignment, daftar, tabel,
   link, dan pemisah halaman).
6. Editor memiliki upload tanda tangan Ketua Acara, Bendahara Proker, dan
   Bukti Transfer Denda. Bukti transfer terpisah dari bukti pembayaran order.

CARA PASANG (macOS / zsh)
-------------------------
1. Ekstrak ZIP ke root project Inventory-SC, misalnya:

   cd ~/path/ke/Inventory-SC
   unzip -o ~/Downloads/Inventory_SC_Berita_Acara_Editor_Fix.zip -d .

2. Terapkan integrasi ke tiga file project yang sudah ada:

   python3 apply_ba_editor_patch.py

3. Jalankan migrasi untuk menambahkan kolom editor dan file lampiran:

   php artisan migrate

4. Pastikan symbolic link storage sudah ada. Jalankan command ini hanya jika
   folder `public/storage` belum ada:

   php artisan storage:link

5. Bersihkan cache Laravel:

   php artisan optimize:clear

6. Jalankan project seperti biasa:

   php artisan serve

   Jika project memakai Vite untuk CSS/JS, jalankan di terminal kedua:

   npm run dev

CARA PAKAI
----------
1. Login menggunakan akun Admin.
2. Buka Admin > Orders.
3. Pada order yang ingin dibuat Berita Acara-nya, klik "Edit Berita Acara".
4. Edit isi dokumen langsung pada preview kertas.
5. Upload tanda tangan Bendahara Proker dan Ketua Acara (PNG/JPG).
6. Upload bukti transfer denda (PNG/JPG atau PDF).
7. Klik "Simpan Berita Acara".
8. Klik "Preview / Download PDF" untuk memeriksa hasil akhir.

CATATAN
-------
- Pastikan kolom BA (nomor, tanggal, rincian kerusakan, dan total denda) telah
  diisi pada halaman Orders sebelum PDF digunakan.
- Nama Ketua Acara dan Bendahara Proker diambil dari data order yang diisi
  pada saat checkout. Tidak perlu memasukkan nama itu ulang di editor.
- Tanda tangan Bendahara Student Council tetap dikelola dari Settings dengan
  key `ttd_bendahara`, sehingga tidak perlu upload ulang pada editor BA.
- File bukti transfer denda menggunakan kolom tersendiri dan tidak menimpa
  `payment_receipt` untuk pembayaran transaksi utama.
- Sebelum memasang, disarankan backup atau commit perubahan lokal project.
