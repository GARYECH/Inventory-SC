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

    @php
        $baDate =
            $order->ba_date
                ? \Carbon\Carbon::parse(
                    $order->ba_date
                )->locale('id')
                : null;

        $baDueDate =
            $baDate
                ? $baDate->copy()->addDays(7)
                : null;
    @endphp

    <!-- ================= KOP SURAT ================= -->

    <table class="kop-table">
        <tr>
            <td width="30%" style="vertical-align: middle;">
                @if($logoPath && file_exists($logoPath))
                    <img
                        src="{{ $logoPath }}"
                        alt="Logo Student Council"
                        style="max-height: 70px;"
                    >
                @endif
            </td>

            <td
                width="70%"
                style="text-align: right; line-height: 1.2;"
            >
                <span style="font-weight: bold; font-size: 14px;">
                    UNIVERSITAS CIPUTRA SURABAYA
                </span><br>

                <span style="font-weight: bold; font-size: 14px;">
                    STUDENT COUNCIL
                </span><br>

                Citraland CBD Boulevard, Surabaya, 60219<br>
                Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>
                Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <!-- ================= NOMOR + TANGGAL ================= -->

    <table class="meta-table">
        <tr>
            <td width="15%">No. Surat</td>
            <td width="3%">:</td>
            <td width="82%">{{ $order->ba_number }}</td>
        </tr>

        <tr>
            <td>Hari/Tanggal</td>
            <td>:</td>
            <td>
                {{
                    $baDate
                        ? $baDate->translatedFormat('l, d F Y')
                        : ($order->ba_date ?? '-')
                }}
            </td>
        </tr>
    </table>

    <!-- ================= JUDUL ================= -->

    <div
        style="
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        "
    >
        BERITA ACARA (INVENTARIS RUSAK / HILANG)
    </div>

    <!-- ================= ISI ================= -->

    <p style="text-align: justify; text-indent: 40px;">
        Pada hari
        <strong>
            {{
                $baDate
                    ? $baDate->translatedFormat('l, d F Y')
                    : ($order->ba_date ?? '-')
            }}
        </strong>, telah dilakukan serah terima dan pemeriksaan
        inventaris <strong>Student Council Universitas Ciputra Surabaya</strong>
        yang sebelumnya dipinjam dan digunakan oleh
        <strong>{{ $order->proker_name }}</strong>.
        Berdasarkan hasil pemeriksaan setelah pelaksanaan kegiatan selesai,
        ditemukan adanya kerusakan dan/atau kehilangan pada inventaris yang
        dipinjam. Adapun rincian kerusakan inventaris tersebut sebagai berikut:
    </p>

    <div style="margin-left: 40px; margin-bottom: 15px;">
        - {!! nl2br(e($order->ba_description)) !!}
    </div>

    <p style="text-align: justify;">
        Kerusakan tersebut akan dipertanggungjawabkan dengan membayarkan
        denda sesuai rincian di atas, sehingga total denda yang harus
        dibayarkan berjumlah
        <strong>
            Rp {{ number_format($order->ba_total_fine, 0, ',', '.') }},-
        </strong>.
    </p>

    <p style="margin-top: 30px;">
        Yang bertandatangan dibawah ini:
    </p>

    <!-- ================= PIHAK PERTAMA ================= -->

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td colspan="3" style="font-weight: bold;">
                PIHAK PERTAMA
            </td>
        </tr>

        <tr>
            <td width="15%">Nama</td>
            <td width="3%">:</td>
            <td width="82%">{{ $order->treasurer_name }}</td>
        </tr>

        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Bendahara {{ $order->proker_name }}</td>
        </tr>
    </table>

    <!-- ================= PIHAK KEDUA ================= -->

    <table style="width: 100%; margin-bottom: 25px;">
        <tr>
            <td colspan="3" style="font-weight: bold;">
                PIHAK KEDUA
            </td>
        </tr>

        <tr>
            <td width="15%">Nama</td>
            <td width="3%">:</td>
            <td width="82%">Gregory Edgard Christian</td>
        </tr>

        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Bendahara Student Council</td>
        </tr>
    </table>

    <!-- ================= PERNYATAAN + PEMBAYARAN ================= -->

    <p style="text-align: justify;">
        Dengan ini menyatakan <strong>PIHAK PERTAMA</strong> akan membayarkan
        denda kepada <strong>PIHAK KEDUA</strong> paling lambat pada hari
        <strong>
            {{
                $baDueDate
                    ? $baDueDate->translatedFormat('l, d F Y')
                    : '-'
            }}
        </strong>.
        Demikian berita acara ini dibuat dengan sebenar-benarnya dan digunakan
        sebagaimana mestinya. Metode pembayaran akan dilakukan melalui transfer
        bank dengan rincian:
    </p>

    <table style="width: 75%; margin-left: 40px;">
        <tr>
            <td width="30%">- Nama Bank</td>
            <td width="5%">:</td>
            <td width="65%">Bank Central Asia (BCA)</td>
        </tr>

        <tr>
            <td>- Nama Rekening</td>
            <td>:</td>
            <td>Chalistha Dea Yuwanda</td>
        </tr>

        <tr>
            <td>- Nomor Rekening</td>
            <td>:</td>
            <td>8620797163</td>
        </tr>
    </table>

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
                <span style="font-weight: bold; text-decoration: underline;">{{ $order->treasurer_name }}</span><br>
                Bendahara {{ $order->proker_name }}
            </td>
            <td>
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
