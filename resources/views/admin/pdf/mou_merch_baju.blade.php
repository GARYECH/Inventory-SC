<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        MoU Merchandise Baju -
        {{ $order->mou_number ?? $order->order_number }}
    </title>


    <style>

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.5;
            margin: 0;
            padding: 0 15px;
        }


        .kop-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }


        .kop-table td {
            vertical-align: middle;
        }


        .kop-text {
            text-align: right;
            line-height: 1.2;
        }


        .kop-text h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
        }


        .kop-text p {
            margin: 0;
            font-size: 10pt;
        }


        .surat-title {
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.3;
        }


        .surat-title h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }


        .surat-title p {
            margin: 0;
            font-size: 11pt;
        }


        .identitas-table {
            width: 100%;
            margin-bottom: 15px;
            margin-left: 10px;
        }


        .identitas-table td {
            vertical-align: top;
            padding: 3px 0;
        }


        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }


        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-size: 10pt;
        }


        .items-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }


        .items-table td.left {
            text-align: left;
        }


        .items-table td.right {
            text-align: right;
        }


        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }


        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }


        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10pt;
            vertical-align: top;
        }


        .detail-table th {
            width: 30%;
            text-align: left;
            background-color: #f9f9f9;
        }


        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 11pt;
        }


        .isi-pasal {
            text-align: justify;
            margin-bottom: 10px;
        }


        ol {
            margin-top: 5px;
            margin-bottom: 5px;
            padding-left: 20px;
            text-align: justify;
        }


        li {
            margin-bottom: 4px;
        }


        .signature-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
            page-break-inside: avoid;
        }


        .signature-table td {
            width: 50%;
            vertical-align: bottom;
            height: 120px;
        }


        .page-break {
            page-break-after: always;
        }


        .small {
            font-size: 9pt;
        }

    </style>

</head>


<body>


@php

    /*
    |--------------------------------------------------------------------------
    | SETTING
    |--------------------------------------------------------------------------
    */

    $logoDb =
        \App\Models\Setting::where(
            'key',
            'logo_sc'
        )->value('value');


    $ttdDb =
        \App\Models\Setting::where(
            'key',
            'ttd_bendahara'
        )->value('value');


    $logoPath =
        $logoDb
            ? storage_path(
                'app/public/' . $logoDb
            )
            : null;


    $ttdPath =
        $ttdDb
            ? storage_path(
                'app/public/' . $ttdDb
            )
            : null;


    \Carbon\Carbon::setLocale('id');


    $hari =
        \Carbon\Carbon::parse(
            $order->created_at
        )->translatedFormat('l');


    $tanggal =
        \Carbon\Carbon::parse(
            $order->created_at
        )->translatedFormat('d F Y');


    /*
    |--------------------------------------------------------------------------
    | TOTAL QUANTITY
    |--------------------------------------------------------------------------
    */

    $totalQuantity = 0;


    foreach ($order->orderItems as $detail) {

        $totalQuantity +=
            (int) $detail->quantity;

    }

@endphp



<!-- ============================================================= -->
<!-- KOP -->
<!-- ============================================================= -->

<table class="kop-table">

    <tr>

        <td width="25%">

            @if(
                $logoPath &&
                file_exists($logoPath)
            )

                <img
                    src="{{ $logoPath }}"
                    style="max-height: 65px;"
                >

            @endif

        </td>


        <td width="75%" class="kop-text">

            <h2>
                UNIVERSITAS CIPUTRA SURABAYA
            </h2>

            <h2>
                STUDENT COUNCIL
            </h2>

            <p>
                SURAT PERJANJIAN KERJA SAMA
            </p>

            <p>
                MERCHANDISE STUDENT COUNCIL 2026/2027
            </p>

            <p>
                Citraland CBD Boulevard, Surabaya, 60219
            </p>

            <p>
                Telepon: (031)7451699; Fax: (031)7451698 |
                Email: studentcouncil@ciputra.ac.id
            </p>

        </td>

    </tr>

</table>



<!-- ============================================================= -->
<!-- JUDUL -->
<!-- ============================================================= -->

<div class="surat-title">

    <h3>
        SURAT PERJANJIAN KERJA SAMA
    </h3>

    <p>
        MERCHANDISE BAJU
    </p>

    <p>
        Nomor:
        {{ $order->mou_number ?? '......../SC/UC/EXT/KS/........' }}
    </p>

</div>



<!-- ============================================================= -->
<!-- PEMBUKA -->
<!-- ============================================================= -->

<div
    class="isi-pasal"
    style="text-indent: 30px;"
>

    Pada hari ini,
    <strong>{{ $hari }}</strong>,
    tanggal
    <strong>{{ $tanggal }}</strong>,
    kami yang bertanda tangan di bawah ini:

</div>



<!-- ============================================================= -->
<!-- PIHAK PERTAMA -->
<!-- ============================================================= -->

<table class="identitas-table">

    <tr>

        <td width="3%">
            1.
        </td>

        <td width="15%">
            Nama
        </td>

        <td width="2%">
            :
        </td>

        <td width="80%">
            <strong>
                Gregory Edgard Christian
            </strong>
        </td>

    </tr>


    <tr>

        <td></td>

        <td>
            Jabatan
        </td>

        <td>
            :
        </td>

        <td>
            Bendahara
        </td>

    </tr>


    <tr>

        <td></td>

        <td>
            Instansi
        </td>

        <td>
            :
        </td>

        <td>
            Student Council Universitas Ciputra Surabaya
        </td>

    </tr>


    <tr>

        <td></td>

        <td>
            Alamat
        </td>

        <td>
            :
        </td>

        <td>
            Citraland CBD Boulevard, Kelurahan Made,
            Kec. Sambikerep, Surabaya, 60219
        </td>

    </tr>


    <tr>

        <td></td>

        <td colspan="3" style="padding-top:5px; text-align:justify;">

            Yang selanjutnya dalam Surat Perjanjian Kerja Sama ini
            bertindak untuk dan atas nama Student Council Universitas
            Ciputra Surabaya, yang selanjutnya disebut sebagai
            <strong>PIHAK PERTAMA</strong>.

        </td>

    </tr>

</table>



<!-- ============================================================= -->
<!-- PIHAK KEDUA -->
<!-- ============================================================= -->

<table class="identitas-table">

    <tr>

        <td width="3%">
            2.
        </td>

        <td width="15%">
            Nama
        </td>

        <td width="2%">
            :
        </td>

        <td width="80%">
            <strong>
                {{ $order->user->name }}
            </strong>
        </td>

    </tr>


    <tr>

        <td></td>

        <td>
            Jabatan
        </td>

        <td>
            :
        </td>

        <td>
            {{ $order->position ?? 'Perwakilan ' . $order->proker_name }}
        </td>

    </tr>


    <tr>

        <td></td>

        <td>
            Organisasi
        </td>

        <td>
            :
        </td>

        <td>
            {{ $order->organization }}
        </td>

    </tr>


    <tr>

        <td></td>

        <td>
            No. Telepon
        </td>

        <td>
            :
        </td>

        <td>
            {{ $order->phone_number ?? '-' }}
        </td>

    </tr>


    <tr>

        <td></td>

        <td colspan="3" style="padding-top:5px; text-align:justify;">

            Yang selanjutnya dalam Surat Perjanjian Kerja Sama ini
            bertindak untuk dan atas nama pribadi dan/atau kepanitiaan
            terkait, yang selanjutnya disebut sebagai
            <strong>PIHAK KEDUA</strong>.

        </td>

    </tr>

</table>



<!-- ============================================================= -->
<!-- PENJELASAN -->
<!-- ============================================================= -->

<div
    class="isi-pasal"
    style="text-indent:30px;"
>

    <strong>PIHAK PERTAMA</strong> dan
    <strong>PIHAK KEDUA</strong> yang selanjutnya secara sendiri-sendiri
    disebut <strong>PIHAK</strong> dan secara bersama-sama disebut
    <strong>PARA PIHAK</strong>, terlebih dahulu menerangkan bahwa
    <strong>PIHAK KEDUA</strong> bermaksud melakukan pembelian
    merchandise berupa baju melalui
    <strong>PIHAK PERTAMA</strong> sesuai dengan rincian dan ketentuan
    yang telah disepakati.

</div>



<!-- ============================================================= -->
<!-- PASAL I -->
<!-- ============================================================= -->

<div class="pasal-title">
    PASAL I
    <br>
    RINCIAN MERCHANDISE
</div>


<div class="isi-pasal">

    <strong>PARA PIHAK</strong> sepakat atas rincian merchandise
    yang menjadi objek transaksi sebagai berikut:

</div>



<table class="items-table">

    <thead>

        <tr>

            <th width="5%">
                No.
            </th>

            <th width="35%">
                Nama Barang
            </th>

            <th width="15%">
                Ukuran
            </th>

            <th width="10%">
                Jumlah
            </th>

            <th width="17%">
                Harga Satuan
            </th>

            <th width="18%">
                Subtotal
            </th>

        </tr>

    </thead>


    <tbody>

        @foreach(
            $order->orderItems as $index => $detail
        )

            @php

                $quantity =
                    (int) $detail->quantity;


                $basePrice =
                    $detail->price !== null
                        ? (int) $detail->price
                        : (int) $detail->item->price;


                $sizeExtra =
                    (int) (
                        $detail->size_additional_price
                        ?? 0
                    );


                $unitPrice =
                    $basePrice +
                    $sizeExtra;


                $subtotal =
                    $unitPrice *
                    $quantity;

            @endphp


            <tr>

                <td>
                    {{ $index + 1 }}
                </td>


                <td class="left">

                    {{ $detail->item->name }}

                </td>


                <td>

                    {{ $detail->size ?? '-' }}

                </td>


                <td>

                    {{ $quantity }}

                </td>


                <td class="right">

                    Rp
                    {{ number_format(
                        $unitPrice,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>


                <td class="right">

                    Rp
                    {{ number_format(
                        $subtotal,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

            </tr>

        @endforeach


        <tr class="total-row">

            <td colspan="3" class="right">
                Total
            </td>

            <td>
                {{ $totalQuantity }}
            </td>

            <td></td>

            <td class="right">

                Rp
                {{ number_format(
                    $order->total_price,
                    0,
                    ',',
                    '.'
                ) }}

            </td>

        </tr>

    </tbody>

</table>



<!-- ============================================================= -->
<!-- PASAL II -->
<!-- ============================================================= -->

<div class="pasal-title">
    PASAL II
    <br>
    SPESIFIKASI DAN DESAIN
</div>


<div class="isi-pasal">

    <ol>

        <li>

            Merchandise yang dipesan oleh
            <strong>PIHAK KEDUA</strong> berupa
            baju sesuai dengan ukuran dan jumlah sebagaimana
            tercantum dalam Pasal I.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong> wajib memberikan
            file desain dengan kualitas yang memadai kepada
            <strong>PIHAK PERTAMA</strong> untuk diproses
            sesuai dengan kebutuhan produksi.

        </li>


        <li>

            Desain yang telah diberikan oleh
            <strong>PIHAK KEDUA</strong> akan digunakan sebagai
            dasar produksi setelah mendapatkan persetujuan
            dari pihak yang berkepentingan.

        </li>


        <li>

            Perubahan desain setelah proses produksi berjalan
            dapat dikenakan biaya tambahan dan/atau penyesuaian
            waktu produksi sesuai dengan kondisi produksi.

        </li>

    </ol>

</div>



<!-- ============================================================= -->
<!-- DESIGN DETAILS -->
<!-- ============================================================= -->

@php

    $designLinks = [];


    foreach ($order->orderItems as $detail) {

        if (
            !empty(
                $detail->design_link
            )
        ) {

            $designLinks[] =
                $detail->design_link;

        }

    }

@endphp


@if(count($designLinks))

    <table class="detail-table">

        <tr>

            <th>
                Link Desain
            </th>

            <td>

                @foreach(
                    array_unique($designLinks)
                    as $link
                )

                    {{ $link }}

                    @if(!$loop->last)
                        <br>
                    @endif

                @endforeach

            </td>

        </tr>

    </table>

@endif



<!-- ============================================================= -->
<!-- PASAL III -->
<!-- ============================================================= -->

<div class="pasal-title">
    PASAL III
    <br>
    HARGA DAN PEMBAYARAN
</div>


<div class="isi-pasal">

    <ol>

        <li>

            Total nilai transaksi yang disepakati oleh
            <strong>PARA PIHAK</strong> adalah sebesar

            <strong>
                Rp
                {{ number_format(
                    $order->total_price,
                    0,
                    ',',
                    '.'
                ) }},-
            </strong>

            sebagaimana rincian pada Pasal I.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong> wajib melakukan
            pembayaran sesuai dengan ketentuan pembayaran
            yang telah ditetapkan oleh
            <strong>PIHAK PERTAMA</strong>.

        </li>


        <li>

            Bukti pembayaran wajib disampaikan kepada
            <strong>PIHAK PERTAMA</strong> melalui sistem
            Inventory Student Council untuk diverifikasi.

        </li>

    </ol>

</div>



<!-- ============================================================= -->
<!-- PASAL IV -->
<!-- ============================================================= -->

<div class="pasal-title">
    PASAL IV
    <br>
    PRODUKSI DAN PENGAMBILAN
</div>


<div class="isi-pasal">

    <ol>

        <li>

            Produksi merchandise dilakukan setelah seluruh
            administrasi dan pembayaran yang dipersyaratkan
            telah diselesaikan.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong> akan menginformasikan
            kepada <strong>PIHAK KEDUA</strong> apabila
            merchandise telah selesai diproses.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong> bertanggung jawab
            untuk melakukan pengambilan merchandise sesuai
            dengan waktu yang telah ditentukan.

        </li>


        <li>

            Apabila terdapat ketidaksesuaian jumlah atau
            kondisi barang saat diterima, laporan disampaikan
            kepada <strong>PIHAK PERTAMA</strong> sesuai
            dengan ketentuan yang berlaku.

        </li>

    </ol>

</div>



<!-- ============================================================= -->
<!-- PASAL V -->
<!-- ============================================================= -->

<div class="pasal-title">
    PASAL V
    <br>
    HAK DAN KEWAJIBAN PARA PIHAK
</div>


<div class="isi-pasal">

    <strong>Hak dan Kewajiban PIHAK PERTAMA:</strong>

    <ol>

        <li>
            Menyediakan merchandise sesuai dengan rincian
            yang telah disepakati.
        </li>

        <li>
            Memberikan informasi kepada
            <strong>PIHAK KEDUA</strong> mengenai proses
            produksi apabila diperlukan.
        </li>

        <li>
            Menerima pembayaran sesuai dengan nilai transaksi.
        </li>

    </ol>


    <strong>Hak dan Kewajiban PIHAK KEDUA:</strong>

    <ol>

        <li>
            Memberikan data, ukuran, jumlah, dan desain
            yang benar kepada <strong>PIHAK PERTAMA</strong>.
        </li>

        <li>
            Melakukan pembayaran sesuai dengan nilai transaksi
            yang telah disepakati.
        </li>

        <li>
            Melakukan pemeriksaan terhadap merchandise setelah
            menerima barang.
        </li>

        <li>
            Menyampaikan laporan mengenai barang yang tidak
            sesuai berdasarkan ketentuan yang berlaku.
        </li>

    </ol>

</div>



<!-- ============================================================= -->
<!-- PASAL VI -->
<!-- ============================================================= -->

<div class="pasal-title">
    PASAL VI
    <br>
    PENUTUP
</div>


<div
    class="isi-pasal"
    style="text-indent:30px;"
>

    Demikian Surat Perjanjian Kerja Sama ini dibuat dengan
    sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
    <strong>PARA PIHAK</strong> menyatakan telah membaca,
    memahami, dan menyetujui seluruh isi perjanjian ini.

</div>



<!-- ============================================================= -->
<!-- SIGNATURE -->
<!-- ============================================================= -->

<table class="signature-table">

    <tr>

        <td>

            Mengetahui,

            <br>

            <strong>
                PIHAK PERTAMA
            </strong>


            <br><br><br><br><br>


            <strong>
                Gregory Edgard Christian
            </strong>


            <br>

            Bendahara

        </td>



        <td>

            Menyetujui,

            <br>

            <strong>
                PIHAK KEDUA
            </strong>


            <br><br><br><br><br>


            <strong>
                {{ $order->user->name }}
            </strong>


            <br>

            {{ $order->position ?? 'Perwakilan' }}

        </td>

    </tr>

</table>



</body>

</html>