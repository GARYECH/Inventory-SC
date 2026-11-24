<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $order->ba_number }}</title>
    <style>
        @page { margin: 20mm 18mm 18mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 14px; color: #000; line-height: 1.6; margin: 0; padding: 0; }
        .kop-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 28px; }
        .meta-table { width: 100%; margin-bottom: 30px; }
        .meta-table td { vertical-align: top; padding: 2px 0; }
        .signature-table { width: 100%; margin-top: 48px; text-align: center; border-collapse: collapse; }
        .signature-table td { width: 50%; vertical-align: bottom; height: 130px; padding: 0 12px; }
        .signature-image-slot { height: 68px; display: block; text-align: center; }
        .signature-image-slot img { max-width: 180px; max-height: 64px; object-fit: contain; }
        .page-break { page-break-after: always; }
        .proof-slot { min-height: 150px; margin-top: 24px; text-align: center; }
        .proof-slot img { max-width: 100%; max-height: 520px; object-fit: contain; }
        a { color: #1d4ed8; text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $logoDb = \App\Models\Setting::where('key', 'logo_sc')->value('value');
        $ttdDb = \App\Models\Setting::where('key', 'ttd_bendahara')->value('value');
        $logoPath = $logoDb ? storage_path('app/public/' . $logoDb) : null;
        $ttdPath = $ttdDb ? storage_path('app/public/' . $ttdDb) : null;

        $baDate = $order->ba_date
            ? \Carbon\Carbon::parse($order->ba_date)->locale('id')
            : null;
        $baDueDate = $baDate ? $baDate->copy()->addDays(7) : null;
    @endphp

    <!-- HALAMAN 1: ISI BERITA ACARA -->
    <table class="kop-table">
        <tr>
            <td width="30%" style="vertical-align: middle;">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo Student Council" style="max-height: 70px;">
                @endif
            </td>
            <td width="70%" style="text-align: right; line-height: 1.2;">
                <span style="font-weight: bold; font-size: 14px;">UNIVERSITAS CIPUTRA SURABAYA</span><br>
                <span style="font-weight: bold; font-size: 14px;">STUDENT COUNCIL</span><br>
                Citraland CBD Boulevard, Surabaya, 60219<br>
                Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>
                Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr><td width="15%">No. Surat</td><td width="3%">:</td><td width="82%">{{ $order->ba_number }}</td></tr>
        <tr>
            <td>Hari/Tanggal</td><td>:</td>
            <td>{{ $baDate ? $baDate->translatedFormat('l, d F Y') : ($order->ba_date ?? '-') }}</td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 30px;">
        BERITA ACARA (INVENTARIS RUSAK / HILANG)
    </div>

    <p style="text-align: justify; text-indent: 40px;">
        Pada hari {{ $baDate ? $baDate->translatedFormat('l, d F Y') : ($order->ba_date ?? '-') }},
        telah dilakukan serah terima dan pemeriksaan inventaris
        <strong>Student Council Universitas Ciputra Surabaya</strong> yang sebelumnya dipinjam dan digunakan oleh
        <strong>{{ $order->proker_name }}</strong>. Berdasarkan hasil pemeriksaan setelah pelaksanaan kegiatan selesai,
        ditemukan adanya kerusakan dan/atau kehilangan pada inventaris yang dipinjam. Adapun rincian kerusakan inventaris
        tersebut sebagai berikut:
    </p>

    <div style="margin-left: 40px; margin-bottom: 15px;">
        - {!! nl2br(e($order->ba_description)) !!}
    </div>

    <p style="text-align: justify; font-weight: normal;">
        Kerusakan tersebut akan dipertanggungjawabkan dengan membayarkan denda sesuai rincian di atas, sehingga total denda
        yang harus dibayarkan berjumlah Rp {{ number_format($order->ba_total_fine, 0, ',', '.') }},-.
    </p>

    <p style="margin-top: 30px;">Yang bertandatangan di bawah ini:</p>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr><td colspan="3" style="font-weight: bold;">PIHAK PERTAMA</td></tr>
        <tr><td width="15%">Nama</td><td width="3%">:</td><td width="82%">{{ $order->treasurer_name }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>Bendahara {{ $order->proker_name }}</td></tr>
    </table>

    <table style="width: 100%; margin-bottom: 25px;">
        <tr><td colspan="3" style="font-weight: bold;">PIHAK KEDUA</td></tr>
        <tr><td width="15%">Nama</td><td width="3%">:</td><td width="82%">Gregory Edgard Christian</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>Bendahara Student Council</td></tr>
    </table>

    <p style="text-align: justify;">
        Dengan ini menyatakan <strong>PIHAK PERTAMA</strong> akan membayarkan denda kepada
        <strong>PIHAK KEDUA</strong> paling lambat pada hari
        <strong>{{ $baDueDate ? $baDueDate->translatedFormat('l, d F Y') : '-' }}</strong>.
        Demikian berita acara ini dibuat dengan sebenar-benarnya dan digunakan sebagaimana mestinya. Metode pembayaran
        akan dilakukan melalui transfer bank dengan rincian:
    </p>

    <table style="width: 75%; margin-left: 40px;">
        <tr><td width="30%">- Nama Bank</td><td width="5%">:</td><td width="65%">Bank Central Asia (BCA)</td></tr>
        <tr><td>- Nama Rekening</td><td>:</td><td>Chalistha Dea Yuwanda</td></tr>
        <tr><td>- Nomor Rekening</td><td>:</td><td>8620797163</td></tr>
    </table>

    <div class="page-break"></div>

    <!-- HALAMAN 2: TANDA TANGAN PENGURUS ACARA DAN BENDAHARA SC -->
    <table class="kop-table">
        <tr>
            <td width="30%">
                @if($logoPath && file_exists($logoPath)) <img src="{{ $logoPath }}" style="max-height: 70px;" alt="Logo Student Council"> @endif
            </td>
            <td width="70%" style="text-align: right; line-height: 1.2;">
                <span style="font-weight: bold; font-size: 14px;">UNIVERSITAS CIPUTRA SURABAYA</span><br>
                <span style="font-weight: bold; font-size: 14px;">STUDENT COUNCIL</span><br>
                Citraland CBD Boulevard, Surabaya, 60219<br>Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30px;">Hormat Kami,</div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-image-slot" data-ba-slot="event-treasurer"><span style="display:block;height:64px;"></span></div>
                <span style="font-weight: bold; text-decoration: underline;">{{ $order->treasurer_name }}</span><br>
                Bendahara {{ $order->proker_name }}
            </td>
            <td>
                <div class="signature-image-slot" data-ba-slot="event-chair"><span style="display:block;height:64px;"></span></div>
                <span style="font-weight: bold; text-decoration: underline;">{{ $order->ketua_acara ?: '(Nama Ketua Acara)' }}</span><br>
                Ketua Acara {{ $order->proker_name }}
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 50px;">
        Mengetahui,<br>
        @if($ttdPath && file_exists($ttdPath))
            <img src="{{ $ttdPath }}" style="max-height: 90px; margin-top: 10px; margin-bottom: 5px;" alt="Tanda tangan Bendahara Student Council"><br>
        @else
            <br><br><br><br>
        @endif
        <span style="font-weight: bold; text-decoration: underline;">Gregory Edgard Christian</span><br>
        Bendahara Student Council
    </div>

    <div class="page-break"></div>

    <!-- HALAMAN 3: LAMPIRAN BUKTI TRANSFER DENDA -->
    <table class="kop-table">
        <tr>
            <td width="30%">
                @if($logoPath && file_exists($logoPath)) <img src="{{ $logoPath }}" style="max-height: 70px;" alt="Logo Student Council"> @endif
            </td>
            <td width="70%" style="text-align: right; line-height: 1.2;">
                <span style="font-weight: bold; font-size: 14px;">UNIVERSITAS CIPUTRA SURABAYA</span><br>
                <span style="font-weight: bold; font-size: 14px;">STUDENT COUNCIL</span><br>
                Citraland CBD Boulevard, Surabaya, 60219<br>Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; font-size: 16px; margin-top: 30px; text-decoration: underline;">
        LAMPIRAN BUKTI TRANSFER DENDA
    </div>
    <div class="proof-slot" data-ba-slot="payment-proof">
        <p style="color:#777;font-style:italic;text-align:center;">Bukti transfer belum diunggah.</p>
    </div>
</body>
</html>
