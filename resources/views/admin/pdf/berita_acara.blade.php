<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $order->ba_number }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 14px; color: #000; line-height: 1.6; margin: 0; padding: 10px 30px; }
        .kop-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 30px; }
        .meta-table { width: 100%; margin-bottom: 30px; }
        .meta-table td { vertical-align: top; padding: 2px 0; }
        .signature-table { width: 100%; margin-top: 50px; text-align: center; }
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
    @endphp

    <!-- ================= HALAMAN 1: ISI BERITA ACARA ================= -->
    <table class="kop-table">
        <tr>
            <td width="30%">
                @if($logoPath && file_exists($logoPath)) <img src="{{ $logoPath }}" style="max-height: 70px;"> @endif
            </td>
            <td width="70%" style="text-align: right; line-height: 1.2;">
                <span style="font-weight: bold; font-size: 14px;">UNIVERSITAS CIPUTRA SURABAYA</span><br>
                <span style="font-weight: bold; font-size: 14px;">STUDENT COUNCIL</span><br>
                Citraland CBD Boulevard, Surabaya, 60219<br>Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr><td width="15%">No. Surat</td><td width="3%">:</td><td width="82%">{{ $order->ba_number }}</td></tr>
        <tr><td>Hari/Tanggal</td><td>:</td><td>{{ $order->ba_date }}</td></tr>
    </table>

    <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 30px;">
        BERITA ACARA (INVENTARIS RUSAK / HILANG)
    </div>

    <p style="text-align: justify; text-indent: 40px;">
        Pada hari {{ $order->ba_date }}, dengan ini kami sampaikan bahwa terdapat inventaris Student Council yang dipinjam oleh program kerja {{ $order->proker_name }} dilaporkan mengalami kerusakan/kehilangan. Setelah acara selesai dan dilakukan serah terima, ditemukan rincian sebagai berikut:
    </p>
    
    <div style="margin-left: 40px; margin-bottom: 15px;">
        {!! nl2br(e($order->ba_description)) !!}
    </div>

    <p style="text-align: justify;">
        Kerusakan/kehilangan ini akan dipertanggung jawabkan dengan membayarkan denda sesuai rincian di atas, sehingga total denda yang harus dibayarkan berjumlah <strong>Rp {{ number_format($order->ba_total_fine, 0, ',', '.') }},-</strong>.
    </p>

    <p style="margin-top: 30px;">Yang bertandatangan dibawah ini:</p>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr><td colspan="3" style="font-weight: bold;">PIHAK PERTAMA</td></tr>
        <tr><td width="15%">Nama</td><td width="3%">:</td><td width="82%">{{ $order->treasurer_name }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>Bendahara {{ $order->proker_name }}</td></tr>
    </table>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr><td colspan="3" style="font-weight: bold;">PIHAK KEDUA</td></tr>
        <tr><td width="15%">Nama</td><td width="3%">:</td><td width="82%">Gregory Edgard Christian</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>Bendahara Student Council</td></tr>
    </table>

    <p style="text-align: justify;">
        Dengan ini menyatakan <strong>PIHAK PERTAMA</strong> akan membayarkan denda kepada <strong>PIHAK KEDUA</strong> paling lambat pada tanggal <strong>{{ $order->ba_due_date }}</strong>. Demikian berita acara ini dibuat dengan sebenar-benarnya dan digunakan sebagaimana mestinya.
    </p>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- ================= HALAMAN 2: TANDA TANGAN ================= -->
    <table class="kop-table">
        <tr>
            <td width="30%">
                @if($logoPath && file_exists($logoPath)) <img src="{{ $logoPath }}" style="max-height: 70px;"> @endif
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
                <!-- Ruang kosong untuk ttd mahasiswa -->
                <span style="font-weight: bold; text-decoration: underline;">{{ $order->treasurer_name }}</span><br>
                Bendahara {{ $order->proker_name }}
            </td>
            <td>
                <!-- Ruang kosong untuk ketua proker mahasiswa -->
                <span style="font-weight: bold; text-decoration: underline;">(...........................................)</span><br>
                Ketua Acara {{ $order->proker_name }}
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 50px;">
        Mengetahui,<br>
        @if($ttdPath && file_exists($ttdPath))
            <img src="{{ $ttdPath }}" style="max-height: 90px; margin-top: 10px; margin-bottom: 5px;"><br>
        @else
            <br><br><br><br>
        @endif
        <span style="font-weight: bold; text-decoration: underline;">Gregory Edgard Christian</span><br>
        Bendahara Student Council
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- ================= HALAMAN 3: LAMPIRAN (KOSONG UNTUK DIISI MHS) ================= -->
    <table class="kop-table">
        <tr>
            <td width="30%">
                @if($logoPath && file_exists($logoPath)) <img src="{{ $logoPath }}" style="max-height: 70px;"> @endif
            </td>
            <td width="70%" style="text-align: right; line-height: 1.2;">
                <span style="font-weight: bold; font-size: 14px;">UNIVERSITAS CIPUTRA SURABAYA</span><br>
                <span style="font-weight: bold; font-size: 14px;">STUDENT COUNCIL</span><br>
                Citraland CBD Boulevard, Surabaya, 60219<br>Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; font-size: 16px; margin-top: 30px;">
        LAMPIRAN
    </div>
    <div style="text-align: center; margin-top: 200px; color: #ccc; font-style: italic;">
        (Silakan gabungkan PDF bukti transfer denda dan foto barang yang rusak di halaman ini)
    </div>

</body>
</html>