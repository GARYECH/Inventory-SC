<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perjanjian Kerjasama - {{ $order->mou_number ?? $order->order_number }}</title>
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 11pt; 
            color: #000; 
            line-height: 1.5; 
            margin: 0; 
            padding: 0 15px;
        }
        
        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-table td { vertical-align: middle; }
        .kop-text { text-align: right; line-height: 1.2; }
        .kop-text h2 { margin: 0; font-size: 12pt; font-weight: bold; }
        .kop-text p { margin: 0; font-size: 10pt; }

        /* JUDUL SURAT */
        .surat-title { text-align: center; margin-bottom: 25px; line-height: 1.3; }
        .surat-title h3 { margin: 0; font-size: 12pt; font-weight: bold; text-decoration: underline; }
        .surat-title p { margin: 0; font-size: 11pt; }

        /* TABEL IDENTITAS */
        .identitas-table { width: 100%; margin-bottom: 15px; margin-left: 10px; }
        .identitas-table td { vertical-align: top; padding: 3px 0; }

        /* TABEL BARANG (PASAL 1) */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 15px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px 8px; text-align: center; font-size: 10pt; }
        .items-table th { background-color: #f9f9f9; font-weight: bold; }
        .items-table td.left { text-align: left; }
        .items-table td.right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }

        /* PASAL-PASAL */
        .pasal-title { text-align: center; font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 11pt; }
        .isi-pasal { text-align: justify; margin-bottom: 10px; text-indent: 0; }
        ol { margin-top: 5px; margin-bottom: 5px; padding-left: 20px; text-align: justify;}
        li { margin-bottom: 4px; }

        /* TANDA TANGAN */
        .signature-table { width: 100%; margin-top: 50px; text-align: center; page-break-inside: avoid; }
        .signature-table td { width: 50%; vertical-align: bottom; height: 120px; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    @php
        $logoDb = \App\Models\Setting::where('key', 'logo_sc')->value('value');
        $ttdDb = \App\Models\Setting::where('key', 'ttd_bendahara')->value('value');
        $logoPath = $logoDb ? storage_path('app/public/' . $logoDb) : null;
        $ttdPath = $ttdDb ? storage_path('app/public/' . $ttdDb) : null;

        \Carbon\Carbon::setLocale('id');
        $hari = \Carbon\Carbon::parse($order->created_at)->translatedFormat('l');
        $tanggal = \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y');
    @endphp

    <table class="kop-table">
        <tr>
            <td width="25%">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" style="max-height: 65px;">
                @endif
            </td>
            <td width="75%" class="kop-text">
                <h2>UNIVERSITAS CIPUTRA SURABAYA</h2>
                <h2>STUDENT COUNCIL</h2>
                <p>SURAT PERJANJIAN KERJASAMA</p>
                <p>ATRIBUT & INVENTARIS STUDENT COUNCIL 2026/2027</p>
                <p>Citraland CBD Boulevard, Surabaya, 60219</p>
                <p>Telepon: (031)7451699; Fax: (031)7451698 | Email: studentcouncil@ciputra.ac.id</p>
            </td>
        </tr>
    </table>

    <div class="surat-title">
        <h3>SURAT PERJANJIAN KERJA SAMA</h3>
        <p>Nomor: {{ $order->mou_number ?? '......../SC/UC/EXT/KS/........' }}</p>
    </div>

    <div class="isi-pasal" style="text-indent: 30px;">
        Pada hari ini, <strong>{{ $hari }}</strong>, tanggal <strong>{{ $tanggal }}</strong>, kami yang bertanda tangan di bawah ini:
    </div>

    <table class="identitas-table">
        <tr><td width="3%">1.</td><td width="15%">Nama</td><td width="2%">:</td><td width="80%"><strong>Gregory Edgard Christian</strong></td></tr>
        <tr><td></td><td>Jabatan</td><td>:</td><td>Bendahara</td></tr>
        <tr><td></td><td>Instansi</td><td>:</td><td>Student Council Universitas Ciputra Surabaya</td></tr>
        <tr><td></td><td>Alamat</td><td>:</td><td>Citraland CBD Boulevard, Kelurahan Made, Kec. Sambikerep, Surabaya, 60219</td></tr>
        <tr><td></td><td colspan="3" style="padding-top:5px; text-align: justify;">Yang selanjutnya dalam Surat Perjanjian Kerjasama ini bertindak untuk dan atas nama Student Council Universitas Ciputra Surabaya, yang selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</td></tr>
    </table>

    <table class="identitas-table">
        <tr><td width="3%">2.</td><td width="15%">Nama</td><td width="2%">:</td><td width="80%"><strong>{{ $order->user->name }}</strong></td></tr>
        <tr><td></td><td>Jabatan</td><td>:</td><td>Perwakilan {{ $order->proker_name }}</td></tr>
        <tr><td></td><td>Instansi</td><td>:</td><td>{{ $order->department ?? 'Universitas Ciputra Surabaya' }}</td></tr>
        <tr><td></td><td>No. Telpon</td><td>:</td><td>{{ $order->phone_number ?? '-' }}</td></tr>
        <tr><td></td><td colspan="3" style="padding-top:5px; text-align: justify;">Yang selanjutnya dalam Surat Perjanjian Kerjasama ini bertindak untuk dan atas nama pribadi dan/atau kepanitiaan terkait, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</td></tr>
    </table>

    <div class="isi-pasal" style="text-indent: 30px;">
        <strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> yang selanjutnya secara sendiri-sendiri disebut <strong>PIHAK</strong> dan secara bersama-sama disebut <strong>PARA PIHAK</strong>, terlebih dahulu menerangkan hal-hal sebagai berikut:
        <ol>
            <li>bahwa <strong>PIHAK PERTAMA</strong> adalah pengelola penuh inventaris dan atribut resmi Student Council Universitas Ciputra Surabaya;</li>
            <li>bahwa <strong>PIHAK KEDUA</strong> adalah pihak yang bermaksud melakukan pengambilan, peminjaman, atau pembelian barang inventaris sesuai dengan prosedur yang berlaku.</li>
        </ol>
        Berdasarkan hal-hal tersebut di atas, <strong>PARA PIHAK</strong> sepakat untuk mengikatkan diri dalam Surat Perjanjian Kerjasama yang diatur dengan ketentuan sebagai berikut.
    </div>

    <div class="pasal-title">PASAL I<br>RINCIAN PRODUK DAN TRANSAKSI</div>
    <div class="isi-pasal">
        <strong>PARA PIHAK</strong> telah menyetujui rincian barang, jumlah, dan nilai transaksi yang menjadi objek dari perjanjian ini dengan detail sebagai berikut:
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="45%">Keterangan Barang</th>
                <th width="10%">Jml</th>
                <th width="10%">Satuan</th>
                <th width="15%">Harga Satuan</th>
                <th width="15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">{{ $detail->item->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>Unit</td>
                <td class="right">Rp {{ number_format($detail->item->price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="right">Total Nilai Transaksi</td>
                <td class="right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="pasal-title">PASAL II<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</div>
    <div class="isi-pasal">
        <strong>Hak PIHAK PERTAMA:</strong>
        <ol>
            <li>Menerima pembayaran secara penuh dari <strong>PIHAK KEDUA</strong> sesuai dengan nominal yang tertera pada Pasal I.</li>
            <li>Menolak untuk menyerahkan barang apabila <strong>PIHAK KEDUA</strong> belum menyelesaikan tahapan administrasi, verifikasi dokumen, maupun pelunasan pembayaran.</li>
            <li>Mengenakan sanksi denda apabila barang yang dipinjamkan mengalami kerusakan atau hilang saat berada dalam tanggung jawab <strong>PIHAK KEDUA</strong>.</li>
        </ol>
        <strong>Kewajiban PIHAK PERTAMA:</strong>
        <ol>
            <li>Menyerahkan barang dalam kondisi baik kepada <strong>PIHAK KEDUA</strong> sesuai dengan rincian setelah seluruh proses verifikasi selesai dilakukan.</li>
            <li>Memberikan pelayanan administrasi berupa penerbitan tagihan (Invoice) dan bukti pembayaran resmi (Kwitansi).</li>
        </ol>
    </div>

    <div class="page-break"></div>

    <table class="kop-table">
        <tr>
            <td width="25%">@if($logoPath && file_exists($logoPath)) <img src="{{ $logoPath }}" style="max-height: 65px;"> @endif</td>
            <td width="75%" class="kop-text">
                <h2>UNIVERSITAS CIPUTRA SURABAYA</h2>
                <h2>STUDENT COUNCIL</h2>
                <p>Citraland CBD Boulevard, Surabaya, 60219</p>
                <p>Jawa Timur – Indonesia</p>
            </td>
        </tr>
    </table>

    <div class="pasal-title">PASAL III<br>HAK DAN KEWAJIBAN PIHAK KEDUA</div>
    <div class="isi-pasal">
        <strong>Hak PIHAK KEDUA:</strong>
        <ol>
            <li>Menerima barang yang telah dipesan atau dipinjam dalam kondisi yang sesuai dengan kesepakatan setelah pelunasan disetujui oleh <strong>PIHAK PERTAMA</strong>.</li>
            <li>Mendapatkan dokumen resmi berupa Invoice dan Kwitansi Lunas.</li>
        </ol>
        <strong>Kewajiban PIHAK KEDUA:</strong>
        <ol>
            <li>Membayar lunas seluruh nilai transaksi tepat waktu sebelum pengambilan barang.</li>
            <li>Mengunggah (upload) seluruh dokumen pendukung, seperti MoU yang telah ditandatangani, Bukti Transfer, dan Kwitansi, ke dalam portal Inventory Student Council.</li>
            <li>Menjaga kondisi barang (khusus untuk peminjaman) dan wajib mengunggah bukti dokumentasi barang melalui Google Drive saat proses serah terima kembali (Return).</li>
            <li>Tunduk dan patuh secara penuh pada Standar Operasional Prosedur (SOP) Student Council yang dipublikasikan secara resmi pada website peminjaman.</li>
        </ol>
    </div>

    <div class="pasal-title">PASAL IV<br>BIAYA DAN METODE PEMBAYARAN</div>
    <div class="isi-pasal">
        <ol>
            <li><strong>PIHAK KEDUA</strong> bersedia membayarkan nilai tagihan secara penuh (100%) sejumlah <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }},-</strong> kepada <strong>PIHAK PERTAMA</strong>.</li>
            <li>Pembayaran wajib dilakukan melalui metode transfer bank, yang ditujukan kepada rekening resmi kepanitiaan dengan rincian sebagai berikut:
                <div style="margin-top: 5px; margin-bottom: 5px; padding-left: 20px;">
                    Bank Tujuan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>Bank Central Asia (BCA)</strong><br>
                    No Rekening &nbsp;&nbsp;&nbsp;&nbsp;: <strong>8620797163</strong><br>
                    Atas Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>Chalistha Dea Yuwanda</strong>
                </div>
            </li>
            <li>Bukti transfer yang sah wajib diunggah pada sistem untuk diverifikasi lebih lanjut.</li>
        </ol>
    </div>

    <div class="pasal-title">PASAL V<br>SANKSI DAN DENDA KERUSAKAN</div>
    <div class="isi-pasal">
        Apabila terjadi kehilangan, cacat, atau kerusakan pada inventaris selama masa peminjaman, <strong>PIHAK PERTAMA</strong> akan menerbitkan Berita Acara Kerusakan. <strong>PIHAK KEDUA</strong> wajib membayarkan denda ganti rugi sesuai rincian pada Berita Acara selambat-lambatnya 7 (tujuh) hari kerja.
    </div>

    <div class="pasal-title">PASAL VI<br>PENYELESAIAN PERSELISIHAN</div>
    <div class="isi-pasal">
        Segala bentuk perselisihan yang timbul dari pelaksanaan Surat Perjanjian Kerjasama ini akan diselesaikan oleh <strong>PARA PIHAK</strong> dengan cara musyawarah dan mufakat untuk mencapai jalan keluar yang terbaik.
    </div>

    <div class="pasal-title">PASAL VII<br>PENUTUP</div>
    <div class="isi-pasal">
        Demikian Surat Perjanjian Kerjasama ini dibuat dan ditandatangani oleh <strong>PARA PIHAK</strong> secara sadar, tanpa tekanan dari pihak manapun, serta mempunyai kekuatan hukum yang sama bagi masing-masing pihak.
    </div>

    <div style="text-align: right; margin-top: 30px; margin-right: 50px;">
        Surabaya, {{ $tanggal }}
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <strong>PIHAK PERTAMA</strong><br>
                @if($ttdPath && file_exists($ttdPath))
                    <img src="{{ $ttdPath }}" style="max-height: 90px; margin-top: 10px; margin-bottom: 5px;"><br>
                @else
                    <br><br><br><br><br>
                @endif
                <span style="font-weight: bold; text-decoration: underline;">Gregory Edgard Christian</span><br>
                Bendahara Student Council<br>
                Universitas Ciputra Surabaya
            </td>
            <td>
                <strong>PIHAK KEDUA</strong><br>
                <br><br><br><br><br><br>
                <span style="font-weight: bold; text-decoration: underline;">{{ $order->user->name }}</span><br>
                Perwakilan {{ $order->proker_name }}
            </td>
        </tr>
    </table>

</body>
</html>