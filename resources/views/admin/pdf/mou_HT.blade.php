<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Surat Perjanjian Kerja Sama -
        {{ $order->mou_number ?? $order->order_number }}
    </title>


    <style>

        /*
        |--------------------------------------------------------------------------
        | PAGE / A4
        |--------------------------------------------------------------------------
        |
        | FIX:
        | Margin dari rule @page tidak diterapkan oleh Dompdf (konten jadi
        | mepet/terpotong di tepi kertas). Jadi margin @page di-nol-kan dan
        | margin halaman dibuat lewat padding pada .page (satu .page = satu
        | halaman A4).
        |
        | Area konten = 210mm - 16mm - 16mm = 178mm lebar.
        | Jangan beri width pada .page, biarkan auto.
        |
        */

        @page {
            size: A4 portrait;
            margin: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | GLOBAL
        |--------------------------------------------------------------------------
        */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            color: #000000;
            background: #ffffff;
            font-family: "Times New Roman", Times, serif;
            font-size: 11.5pt;
            line-height: 1.5;
        }

        p {
            margin: 0 0 4mm 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        td,
        th {
            overflow-wrap: break-word;
            word-wrap: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | MANUAL PAGE (1 .page = 1 halaman A4)
        |--------------------------------------------------------------------------
        */

        .page {
            margin: 0;
            padding: 11mm 16mm 12mm 16mm;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header {
            margin: 0 0 5mm 0;
            padding: 0 0 4mm 0;
            border-bottom: 6px solid #000000;
        }

        .header-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 0;
            border: none;
            vertical-align: middle;
        }

        .header-logo {
            width: 35%;
            padding-left: 2mm !important;
            text-align: left;
        }

        .header-logo img {
            max-width: 60mm;
            max-height: 27mm;
        }

        .header-text {
            width: 65%;
            text-align: right;
            line-height: 1.1;
        }

        .header-main {
            font-size: 12.5pt;
            font-weight: bold;
        }

        .header-sub {
            font-size: 11.5pt;
            font-weight: bold;
        }

        .header-address {
            margin-top: 4mm;
            font-size: 9.5pt;
            line-height: 1.25;
        }


        /*
        |--------------------------------------------------------------------------
        | BASIC TEXT
        |--------------------------------------------------------------------------
        */

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        .underline {
            text-decoration: underline;
        }

        .paragraph {
            text-align: justify;
            line-height: 1.55;
        }


        /*
        |--------------------------------------------------------------------------
        | DYNAMIC DATA
        |--------------------------------------------------------------------------
        |
        | Data belum ada = MERAH
        | Data sudah ada = HITAM
        |
        */

        .dynamic.empty {
            color: #ff0000;
        }

        .dynamic.filled {
            color: #000000;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .title {
            margin: 2mm 0 5mm 0;
            text-align: center;
        }

        .title-main {
            font-size: 15pt;
            font-weight: bold;
            text-decoration: underline;
            line-height: 1.2;
        }

        .title-number {
            margin-top: 1.5mm;
            font-size: 11.5pt;
        }


        /*
        |--------------------------------------------------------------------------
        | PARTY TABLE
        |--------------------------------------------------------------------------
        */

        .party-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .party-table td {
            padding: 0;
            border: none;
            vertical-align: top;
            line-height: 1.5;
        }

        .party-number {
            width: 6%;
        }

        .party-label {
            width: 16%;
        }

        .party-colon {
            width: 4%;
            text-align: center;
        }

        .party-value {
            width: 74%;
        }

        .party-description {
            margin-bottom: 4mm;
            text-align: justify;
            line-height: 1.55;
        }


        /*
        |--------------------------------------------------------------------------
        | ARTICLE TITLE
        |--------------------------------------------------------------------------
        */

        .article-title {
            margin: 1mm 0 6mm 0;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.18;
        }

        .article-title .line {
            display: block;
        }

        .sub-heading {
            margin-bottom: 2.5mm;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | LEGAL LIST
        |--------------------------------------------------------------------------
        */

        .legal-list {
            margin: 0;
            padding-left: 10mm;
        }

        .legal-list li {
            margin-bottom: 2.5mm;
            padding-left: 1.5mm;
            text-align: justify;
            line-height: 1.52;
        }

        .legal-list li:last-child {
            margin-bottom: 0;
        }

        .legal-list.alpha {
            padding-left: 13mm;
        }

        .legal-list.alpha li {
            margin-bottom: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | SCHEDULE
        |--------------------------------------------------------------------------
        */

        .schedule-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin: 1mm 0 4mm 0;
        }

        .schedule-table td {
            padding: 0;
            border: none;
            vertical-align: top;
            line-height: 1.55;
        }

        .schedule-label {
            width: 28%;
            padding-left: 7mm !important;
        }

        .schedule-colon {
            width: 4%;
            text-align: center;
        }

        .schedule-value {
            width: 68%;
        }

        .sub-number {
            margin: 2mm 0;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | HT ITEMS TABLE
        |--------------------------------------------------------------------------
        */

        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000000;
            padding: 2mm 1.2mm;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .items-table th {
            background-color: #d9e5f7;
            text-align: center;
            font-size: 8.8pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .items-table td {
            font-size: 9.2pt;
            line-height: 1.35;
        }

        .col-no {
            width: 6%;
            text-align: center;
        }

        .col-description {
            width: 31%;
            text-align: left;
        }

        .col-quantity {
            width: 9%;
            text-align: center;
        }

        .col-unit {
            width: 8%;
            text-align: center;
        }

        .col-price {
            width: 17%;
        }

        .col-days {
            width: 10%;
            text-align: center;
        }

        .col-subtotal {
            width: 19%;
        }

        .item-name {
            font-style: italic;
            line-height: 1.35;
        }

        .money-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .money-table td {
            border: none !important;
            padding: 0 !important;
            font-size: 9pt;
            line-height: 1.2;
        }

        .money-prefix {
            width: 19%;
            text-align: left;
        }

        .money-value {
            width: 81%;
            text-align: right;
            white-space: nowrap;
        }

        .price-note {
            margin-top: 1mm;
            text-align: right;
            font-size: 8pt;
            color: #555555;
        }

        .formula {
            margin-top: 1mm;
            text-align: right;
            font-size: 7.5pt;
            color: #555555;
            line-height: 1.2;
        }

        .free-label {
            text-align: center;
            color: #a00000;
            font-weight: bold;
            font-size: 9pt;
            line-height: 1.2;
        }

        .free-note {
            margin-top: 1mm;
            text-align: center;
            font-size: 7.5pt;
            color: #666666;
            line-height: 1.2;
        }

        .total-row td {
            background-color: #fff2cc;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | SIGNATURE
        |--------------------------------------------------------------------------
        */

        .signature-date {
            margin-top: 14mm;
            margin-right: 6mm;
            text-align: right;
        }

        .signature-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 6mm;
        }

        .signature-table td {
            width: 50%;
            padding: 0 4mm;
            border: none;
            vertical-align: top;
            text-align: center;
        }

        .signature-title {
            margin-bottom: 14mm;
        }

        .signature-image {
            height: 23mm;
            text-align: center;
        }

        .signature-image img {
            max-width: 43mm;
            max-height: 19mm;
            margin: 0 auto;
        }

        .signature-space {
            height: 23mm;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            line-height: 1.3;
        }

        .signature-role {
            line-height: 1.35;
        }


        /*
        |--------------------------------------------------------------------------
        | SPACING
        |--------------------------------------------------------------------------
        */

        .mt-small {
            margin-top: 2mm;
        }

        .mt-medium {
            margin-top: 5mm;
        }

    </style>

</head>


<body>


@php

    /*
    |--------------------------------------------------------------------------
    | SYSTEM SETTINGS
    |--------------------------------------------------------------------------
    */

    $logoDb = \App\Models\Setting::where('key', 'logo_sc')->value('value');

    $ttdDb = \App\Models\Setting::where('key', 'ttd_bendahara')->value('value');

    $logoPath = $logoDb
        ? storage_path('app/public/' . $logoDb)
        : null;

    $ttdPath = $ttdDb
        ? storage_path('app/public/' . $ttdDb)
        : null;


    /*
    |--------------------------------------------------------------------------
    | PARTY 2
    |--------------------------------------------------------------------------
    */

    $partyTwoName = $order->full_name
        ?? optional($order->user)->name
        ?? '';

    $partyTwoPosition = $order->position ?? '';

    $partyTwoOrganization = $order->organization ?? '';

    $partyTwoAddress = $order->address ?? '';

    $partyTwoPhone = $order->phone_number ?? '';

    $eventName = $order->proker_name ?? '';


    /*
    |--------------------------------------------------------------------------
    | AGREEMENT DATE
    |--------------------------------------------------------------------------
    */

    $agreementDate = $order->created_at
        ? \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('l, d F Y')
        : '';

    $agreementDateShort = $order->created_at
        ? \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('d F Y')
        : '';


    /*
    |--------------------------------------------------------------------------
    | HT ITEMS
    |--------------------------------------------------------------------------
    |
    | Hanya HT yang benar-benar dipesan.
    |
    */

    $htItems = $order->orderItems
        ->filter(function ($detail) {

            return $detail->item
                && $detail->item->transaction_type === 'Handy Talkie';

        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | SPLIT TABLE
    |--------------------------------------------------------------------------
    */

    $pageTwoItems = $htItems->slice(0, 2)->values();

    $pageThreeItems = $htItems->slice(2)->values();


    /*
    |--------------------------------------------------------------------------
    | RENTAL DAYS
    |--------------------------------------------------------------------------
    |
    | Jumlah hari rental menggunakan GAP tanggal.
    |
    | Minggu → Senin  = 0 hari
    | Minggu → Selasa = 1 hari
    | Minggu → Rabu   = 2 hari
    |
    */

    $rentalDays = null;


    if (
        $order->start_date &&
        $order->end_date
    ) {

        $startCarbon =
            \Carbon\Carbon::parse(
                $order->start_date
            )->startOfDay();

        $endCarbon =
            \Carbon\Carbon::parse(
                $order->end_date
            )->startOfDay();

        $dateDifference =
            $startCarbon->diffInDays(
                $endCarbon
            );

        $rentalDays =
            max(
                0,
                $dateDifference - 1
            );

    }


    /*
    |--------------------------------------------------------------------------
    | HT TOTAL
    |--------------------------------------------------------------------------
    */

    $htTotal = (int) $htItems->sum(function ($detail) {

        return (int) ($detail->subtotal_price ?? 0);

    });


    /*
    |--------------------------------------------------------------------------
    | MONEY
    |--------------------------------------------------------------------------
    */

    $money = function ($value) {

        return number_format((int) $value, 2, ',', '.');

    };


    /*
    |--------------------------------------------------------------------------
    | DYNAMIC COLOR
    |--------------------------------------------------------------------------
    */

    $dynamicClass = function ($value) {

        if ($value === null || trim((string) $value) === '') {

            return 'dynamic empty';

        }

        return 'dynamic filled';

    };


    /*
    |--------------------------------------------------------------------------
    | TERBILANG
    |--------------------------------------------------------------------------
    */

    $terbilang = function ($number) use (&$terbilang) {

        $number = (int) $number;

        $words = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas',
        ];

        if ($number === 0) {
            return 'Nol';
        }

        if ($number < 12) {
            return $words[$number];
        }

        if ($number < 20) {
            return $words[$number - 10] . ' Belas';
        }

        if ($number < 100) {

            $result = $words[(int) ($number / 10)] . ' Puluh';

            $remainder = $number % 10;

            if ($remainder > 0) {
                $result .= ' ' . $terbilang($remainder);
            }

            return $result;

        }

        if ($number < 200) {

            $remainder = $number - 100;

            return 'Seratus' . ($remainder > 0 ? ' ' . $terbilang($remainder) : '');

        }

        if ($number < 1000) {

            $result = $terbilang((int) ($number / 100)) . ' Ratus';

            $remainder = $number % 100;

            if ($remainder > 0) {
                $result .= ' ' . $terbilang($remainder);
            }

            return $result;

        }

        if ($number < 2000) {

            $remainder = $number - 1000;

            return 'Seribu' . ($remainder > 0 ? ' ' . $terbilang($remainder) : '');

        }

        if ($number < 1000000) {

            $result = $terbilang((int) ($number / 1000)) . ' Ribu';

            $remainder = $number % 1000;

            if ($remainder > 0) {
                $result .= ' ' . $terbilang($remainder);
            }

            return $result;

        }

        if ($number < 1000000000) {

            $result = $terbilang((int) ($number / 1000000)) . ' Juta';

            $remainder = $number % 1000000;

            if ($remainder > 0) {
                $result .= ' ' . $terbilang($remainder);
            }

            return $result;

        }

        if ($number < 1000000000000) {

            $result = $terbilang((int) ($number / 1000000000)) . ' Miliar';

            $remainder = $number % 1000000000;

            if ($remainder > 0) {
                $result .= ' ' . $terbilang($remainder);
            }

            return $result;

        }

        return '';

    };

@endphp



<!-- ================================================================== -->
<!-- PAGE 1 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="title">

        <div class="title-main">
            SURAT PERJANJIAN KERJASAMA
        </div>


        <div class="title-number">

            Nomor:

            <span class="{{ $dynamicClass($order->mou_number ?? $order->order_number) }}">

                {{ $order->mou_number ?? $order->order_number }}

            </span>

        </div>

    </div>



    <p class="paragraph">

        Pada hari ini

        <span class="{{ $dynamicClass($agreementDate) }}">
            {{ $agreementDate ?: '-' }}
        </span>,

        kami yang bertanda tangan di bawah ini:

    </p>



    <!-- PIHAK PERTAMA -->

    <table class="party-table">

        <tr>

            <td
                class="party-number"
                rowspan="5"
            >
                1.
            </td>

            <td class="party-label">
                Nama
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">
                Gregory Edgard Christian
            </td>

        </tr>


        <tr>

            <td class="party-label">
                Jabatan
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">
                Bendahara
            </td>

        </tr>


        <tr>

            <td class="party-label">
                Instansi
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">
                Student Council Universitas Ciputra Surabaya
            </td>

        </tr>


        <tr>

            <td class="party-label">
                Alamat
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">

                Citraland CBD Boulevard, RT: 04/RW: 01,
                Kelurahan Made,

                <br>

                Kec. Sambikerep, Kota Surabaya,
                Jawa Timur - 60219

            </td>

        </tr>


        <tr>

            <td class="party-label">
                No. Telpon
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">
                +62 813-3222-7372
            </td>

        </tr>

    </table>



    <p class="party-description">

        Yang selanjutnya dalam Surat Perjanjian Kerjasama ini
        bertindak untuk dan atas nama Student Council Universitas
        Ciputra Surabaya dan selanjutnya disebut sebagai
        <strong>PIHAK PERTAMA.</strong>

    </p>



    <!-- PIHAK KEDUA -->

    <table class="party-table">

        <tr>

            <td
                class="party-number"
                rowspan="5"
            >
                2.
            </td>

            <td class="party-label">
                Nama
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">

                <span class="{{ $dynamicClass($partyTwoName) }}">
                    {{ $partyTwoName ?: '-' }}
                </span>

            </td>

        </tr>


        <tr>

            <td class="party-label">
                Jabatan
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">

                <span class="{{ $dynamicClass($partyTwoPosition) }}">
                    {{ $partyTwoPosition ?: '-' }}
                </span>

            </td>

        </tr>


        <tr>

            <td class="party-label">
                Instansi
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">

                <span class="{{ $dynamicClass($partyTwoOrganization) }}">
                    {{ $partyTwoOrganization ?: '-' }}
                </span>

            </td>

        </tr>


        <tr>

            <td class="party-label">
                Alamat
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">

                <span class="{{ $dynamicClass($partyTwoAddress) }}">
                    {{ $partyTwoAddress ?: '-' }}
                </span>

            </td>

        </tr>


        <tr>

            <td class="party-label">
                No. Telpon
            </td>

            <td class="party-colon">
                :
            </td>

            <td class="party-value">

                <span class="{{ $dynamicClass($partyTwoPhone) }}">
                    {{ $partyTwoPhone ?: '-' }}
                </span>

            </td>

        </tr>

    </table>



    <p class="party-description">

        Yang selanjutnya dalam Surat Perjanjian Kerjasama ini
        bertindak untuk dan atas nama

        <span class="{{ $dynamicClass($partyTwoOrganization) }}">
            {{ $partyTwoOrganization ?: '-' }}
        </span>

        disebut sebagai <strong>PIHAK KEDUA.</strong>

    </p>



    <p class="paragraph">

        Berdasarkan hal-hal tersebut di atas,
        <strong>PARA PIHAK</strong> sepakat untuk membuat,
        menandatangani, dan melaksanakan
        <em>Memorandum of Understanding</em>
        (MoU) tentang penyewaan
        <em>Handy Talkie</em> (HT) untuk kegiatan

        <span class="{{ $dynamicClass($eventName) }}">
            {{ $eventName ?: '-' }}
        </span>,

        yang diatur dengan ketentuan sebagai berikut.

    </p>

</div>



<!-- ================================================================== -->
<!-- PAGE 2 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="article-title">

        <span class="line">
            PASAL I
        </span>

        <span class="line">
            JENIS KESEPAKATAN
        </span>

    </div>



    <p class="paragraph">

        Jenis kesepakatan yang disepakati dalam Surat Perjanjian
        Kerja Sama ini adalah kerja sama antara
        <strong>PIHAK PERTAMA</strong> dan
        <strong>PIHAK KEDUA</strong>, di mana
        <strong>PIHAK PERTAMA</strong> bersedia menyediakan
        penyewaan peralatan komunikasi berupa
        <em>Handy Talkie</em> (HT) sesuai dengan kebutuhan
        <strong>PIHAK KEDUA</strong> untuk pelaksanaan kegiatan

        <span class="{{ $dynamicClass($eventName) }}">
            {{ $eventName ?: '-' }}
        </span>,

        dengan rincian sebagai berikut:

    </p>



    <div class="sub-number">
        1) Pengambilan
    </div>



    <table class="schedule-table">

        <tr>

            <td class="schedule-label">
                Hari, Tanggal
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                <span class="{{ $dynamicClass($order->start_date) }}">

                    {{
                        $order->start_date
                            ? \Carbon\Carbon::parse($order->start_date)->locale('id')->translatedFormat('l, d F Y')
                            : '-'
                    }}

                </span>

            </td>

        </tr>


        <tr>

            <td class="schedule-label">
                Waktu Pengambilan
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                <span class="{{ $dynamicClass($order->start_time) }}">

                    {{
                        $order->start_time
                            ? \Carbon\Carbon::parse($order->start_time)->format('H.i') . ' WIB'
                            : '-'
                    }}

                </span>

            </td>

        </tr>


        <tr>

            <td class="schedule-label">
                Lokasi Pengambilan
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                Ruang Student Council, Main Building Lantai 2,

                <br>

                Universitas Ciputra Surabaya

            </td>

        </tr>

    </table>



    <div class="sub-number">
        2) Pengembalian
    </div>



    <table class="schedule-table">

        <tr>

            <td class="schedule-label">
                Hari, Tanggal
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                <span class="{{ $dynamicClass($order->end_date) }}">

                    {{
                        $order->end_date
                            ? \Carbon\Carbon::parse($order->end_date)->locale('id')->translatedFormat('l, d F Y')
                            : '-'
                    }}

                </span>

            </td>

        </tr>


        <tr>

            <td class="schedule-label">
                Waktu Pengembalian
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                <span class="{{ $dynamicClass($order->end_time) }}">

                    {{
                        $order->end_time
                            ? \Carbon\Carbon::parse($order->end_time)->format('H.i') . ' WIB'
                            : '-'
                    }}

                </span>

            </td>

        </tr>


        <tr>

            <td class="schedule-label">
                Lokasi Pengembalian
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                Ruang Student Council, Main Building Lantai 2,

                <br>

                Universitas Ciputra Surabaya

            </td>

        </tr>


        <tr>

            <td class="schedule-label">
                Durasi Rental
            </td>

            <td class="schedule-colon">
                :
            </td>

            <td class="schedule-value">

                <span class="{{ $dynamicClass($rentalDays) }}">

                    {{ $rentalDays ?? 0 }}

                    {{ ($rentalDays ?? 0) === 1 ? 'hari' : 'hari' }}

                </span>

            </td>

        </tr>

    </table>



    <!-- ============================================================= -->
    <!-- ORDER ITEMS ONLY -->
    <!-- ============================================================= -->

    <table class="items-table">

        <thead>

            <tr>

                <th class="col-no">
                    No
                </th>

                <th class="col-description">
                    Keterangan
                </th>

                <th class="col-quantity">
                    Jumlah
                </th>

                <th class="col-unit">
                    Satuan
                </th>

                <th class="col-price">
                    Harga / Hari
                </th>

                <th class="col-days">
                    Hari
                </th>

                <th class="col-subtotal">
                    Subtotal
                </th>

            </tr>

        </thead>



        <tbody>


            @if($pageTwoItems->count() > 0)


                @foreach($pageTwoItems as $index => $detail)

                    @php

                        $quantity =
                            (int) (
                                $detail->quantity
                                ?? 0
                            );


                        $subtotal =
                            (int) (
                                $detail->subtotal_price
                                ?? 0
                            );


                        $itemName =
                            $detail->item->name
                            ?? $detail->item->transaction_detail
                            ?? '';


                        $unitPrice =
                            $detail->item
                                ? (int) $detail->item->price
                                : 0;


                        $transactionDetail =
                            $detail->item->transaction_detail
                            ?? null;


                        $isFree =
                            $subtotal === 0
                            &&
                            $order->organization ===
                                'Student Council'
                            &&
                            $transactionDetail ===
                                'HT UV-5R';

                    @endphp



                    <tr>


                        <!-- NO -->

                        <td class="col-no">

                            {{ $index + 1 }}

                        </td>



                        <!-- DESCRIPTION -->

                        <td class="col-description">

                            <div class="item-name">

                                <span class="{{ $dynamicClass($itemName) }}">

                                    {{ $itemName ?: '-' }}

                                </span>


                                <br>


                                <span>
                                    (Fullset HT, Earphone,
                                    Charger, Antenna)
                                </span>


                                <br>


                                <span
                                    style="
                                        font-style:normal;
                                        font-size:8pt;
                                        color:#555555;
                                    "
                                >

                                    Harga dihitung berdasarkan
                                    hari rental.

                                </span>

                            </div>

                        </td>



                        <!-- QUANTITY -->

                        <td class="col-quantity">

                            <span class="dynamic filled">

                                {{ $quantity }}

                            </span>

                        </td>



                        <!-- UNIT -->

                        <td class="col-unit">

                            Pcs

                        </td>



                        <!-- PRICE / DAY -->

                        <td class="col-price">


                            @if($isFree)

                                <div class="free-label">
                                    FREE (SC)
                                </div>

                                <div class="free-note">
                                    Student Council
                                </div>

                            @else

                                <table class="money-table">

                                    <tr>

                                        <td class="money-prefix">
                                            Rp
                                        </td>

                                        <td class="money-value">

                                            {{ $money($unitPrice) }}

                                        </td>

                                    </tr>

                                </table>


                                <div class="price-note">
                                    / hari
                                </div>

                            @endif

                        </td>



                        <!-- DAYS -->

                        <td class="col-days">

                            <strong>
                                {{ $rentalDays ?? 0 }}
                            </strong>

                            <br>

                            <span
                                style="
                                    font-size:7.5pt;
                                    color:#555555;
                                "
                            >
                                hari
                            </span>

                        </td>



                        <!-- SUBTOTAL -->

                        <td class="col-subtotal">


                            @if(!$isFree)

                                <div class="formula">

                                    Rp
                                    {{
                                        number_format(
                                            $unitPrice,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                    ×

                                    {{ $rentalDays ?? 0 }}

                                    ×

                                    {{ $quantity }}

                                </div>

                            @else

                                <div
                                    class="formula"
                                    style="color:#a00000;"
                                >
                                    FREE (SC)
                                </div>

                            @endif


                            <table class="money-table">

                                <tr>

                                    <td class="money-prefix">
                                        Rp
                                    </td>

                                    <td class="money-value">

                                        {{ $money($subtotal) }}

                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>


                @endforeach


            @else


                <tr>

                    <td
                        colspan="7"
                        class="center"
                        style="height:16mm;"
                    >

                        Tidak ada Handy Talkie yang dipesan.

                    </td>

                </tr>


            @endif


        </tbody>

    </table>

</div>



<!-- ================================================================== -->
<!-- PAGE 3 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <table class="items-table">

        <thead>

            <tr>

                <th class="col-no">
                    No
                </th>

                <th class="col-description">
                    Keterangan
                </th>

                <th class="col-quantity">
                    Jumlah
                </th>

                <th class="col-unit">
                    Satuan
                </th>

                <th class="col-price">
                    Harga / Hari
                </th>

                <th class="col-days">
                    Hari
                </th>

                <th class="col-subtotal">
                    Subtotal
                </th>

            </tr>

        </thead>



        <tbody>


            @foreach($pageThreeItems as $index => $detail)

                @php

                    $quantity =
                        (int) (
                            $detail->quantity
                            ?? 0
                        );


                    $subtotal =
                        (int) (
                            $detail->subtotal_price
                            ?? 0
                        );


                    $itemName =
                        $detail->item->name
                        ?? $detail->item->transaction_detail
                        ?? '';


                    $unitPrice =
                        $detail->item
                            ? (int) $detail->item->price
                            : 0;


                    $transactionDetail =
                        $detail->item->transaction_detail
                        ?? null;


                    $isFree =
                        $subtotal === 0
                        &&
                        $order->organization ===
                            'Student Council'
                        &&
                        $transactionDetail ===
                            'HT UV-5R';


                    $rowNumber =
                        $index + 3;

                @endphp



                <tr>


                    <!-- NO -->

                    <td class="col-no">

                        {{ $rowNumber }}

                    </td>



                    <!-- DESCRIPTION -->

                    <td class="col-description">

                        <div class="item-name">

                            <span class="{{ $dynamicClass($itemName) }}">

                                {{ $itemName ?: '-' }}

                            </span>


                            <br>


                            <span>
                                (Fullset HT, Earphone,
                                Charger, Antenna)
                            </span>

                        </div>

                    </td>



                    <!-- QUANTITY -->

                    <td class="col-quantity">

                        <span class="dynamic filled">

                            {{ $quantity }}

                        </span>

                    </td>



                    <!-- UNIT -->

                    <td class="col-unit">

                        Pcs

                    </td>



                    <!-- PRICE / DAY -->

                    <td class="col-price">


                        @if($isFree)

                            <div class="free-label">
                                FREE (SC)
                            </div>

                            <div class="free-note">
                                Student Council
                            </div>

                        @else

                            <table class="money-table">

                                <tr>

                                    <td class="money-prefix">
                                        Rp
                                    </td>

                                    <td class="money-value">

                                        {{ $money($unitPrice) }}

                                    </td>

                                </tr>

                            </table>


                            <div class="price-note">
                                / hari
                            </div>

                        @endif

                    </td>



                    <!-- DAYS -->

                    <td class="col-days">

                        <strong>
                            {{ $rentalDays ?? 0 }}
                        </strong>

                        <br>

                        <span
                            style="
                                font-size:7.5pt;
                                color:#555555;
                            "
                        >
                            hari
                        </span>

                    </td>



                    <!-- SUBTOTAL -->

                    <td class="col-subtotal">


                        @if(!$isFree)

                            <div class="formula">

                                Rp
                                {{
                                    number_format(
                                        $unitPrice,
                                        0,
                                        ',',
                                        '.'
                                    )
                                }}

                                ×

                                {{ $rentalDays ?? 0 }}

                                ×

                                {{ $quantity }}

                            </div>

                        @else

                            <div
                                class="formula"
                                style="color:#a00000;"
                            >
                                FREE (SC)
                            </div>

                        @endif


                        <table class="money-table">

                            <tr>

                                <td class="money-prefix">
                                    Rp
                                </td>

                                <td class="money-value">

                                    {{ $money($subtotal) }}

                                </td>

                            </tr>

                        </table>

                    </td>

                </tr>


            @endforeach



            <!-- TOTAL -->

            <tr>

                <td
                    colspan="6"
                    class="right"
                    style="
                        height:11mm;
                        padding-right:8mm;
                    "
                >

                    <strong>
                        Total
                    </strong>

                </td>


                <td
                    class="col-subtotal"
                    style="
                        background-color:#fff2cc;
                        font-weight:bold;
                    "
                >

                    <table class="money-table">

                        <tr>

                            <td class="money-prefix">
                                Rp
                            </td>

                            <td class="money-value">

                                {{ $money($htTotal) }}

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>


        </tbody>

    </table>

</div>



<!-- ================================================================== -->
<!-- PAGE 4 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="article-title">

        <span class="line">
            PASAL II
        </span>

        <span class="line">
            HAK DAN KEWAJIBAN PIHAK PERTAMA
        </span>

    </div>



    <div class="sub-heading">
        Hak:
    </div>



    <ol class="legal-list">


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima pembayaran sesuai skema, nominal dan
            jadwal pembayaran yang telah disepakati sebagaimana
            tercantum dalam <strong>Pasal IV.</strong>

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima kembali <em>Handy Talkie</em> (HT) yang
            disewakan dalam kondisi baik, layak pakai, berfungsi
            dengan baik, baterai dalam keadaan terisi penuh
            (<em>fully charged</em>) sesuai waktu dan lokasi yang
            telah disepakati oleh <strong>PARA PIHAK</strong>
            sebagaimana diatur dalam <strong>Pasal I.</strong>

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menentukan fungsi dan pengaturan
            (<em>setting</em>) awal pada <em>Handy Talkie</em> (HT)
            yang disewakan, serta berhak memberikan maupun menolak
            persetujuan atas segala bentuk pembongkaran, modifikasi,
            perbaikan, atau perubahan frekuensi yang diajukan oleh
            <strong>PIHAK KEDUA.</strong>

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima kompensasi dari
            <strong>PIHAK KEDUA</strong>
            apabila terjadi kerusakan atau kehilangan
            <em>Handy Talkie</em> (HT) beserta kelengkapannya yang
            disebabkan oleh kesengajaan, kelalaian, maupun penggunaan
            yang tidak sesuai oleh <strong>PIHAK KEDUA</strong>,
            dengan ketentuan sebagaimana tercantum dalam
            <strong>Pasal V</strong> mengenai sanksi.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima kembali dokumen
            <em>Memorandum of Understanding</em> (MoU) yang telah
            ditandatangani oleh <strong>PIHAK KEDUA</strong>
            melalui <em>website Inventory</em> Student Council
            setelah pesanan berada pada status
            “<em>Waiting for MoU</em>”.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima bukti transfer atau bukti pembayaran dari
            <strong>PIHAK KEDUA</strong>
            melalui <em>website Inventory</em> Student Council
            apabila pesanan berada pada status
            “<em>Waiting for Payment</em>”.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima kuitansi yang telah ditandatangani oleh
            <strong>PIHAK KEDUA</strong>
            melalui <em>website Inventory</em> Student Council
            sebagai bukti penyelesaian administrasi keuangan setelah
            pesanan berada pada status
            “<em>Waiting for Kwitansi</em>”.

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 5 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <ol
        class="legal-list"
        start="8"
    >


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima tautan (<em>link</em>) Google Drive dari
            <strong>PIHAK KEDUA</strong>
            melalui <em>website Inventory</em> Student Council yang
            memuat bukti dokumentasi foto kondisi setiap tipe barang,
            dengan ketentuan penamaan <em>file</em> foto wajib
            disamakan dengan penamaan barang di <em>website</em>,
            apabila pesanan telah memasuki status
            ”<em>Handed Over</em>”.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berhak menerima dokumen Berita Acara Kerusakan dan/atau
            Kehilangan Barang yang telah diisi secara lengkap,
            meliputi bukti transfer denda dan ditandatangani oleh
            <strong>PIHAK KEDUA</strong>
            melalui <em>website Inventory</em> Student Council
            apabila pesanan berada pada status
            “<em>Returned(Damaged)</em>” terkait kendala, kerusakan,
            dan/atau kehilangan barang selama masa peminjaman.

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 6 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="sub-heading">
        Kewajiban:
    </div>



    <ol class="legal-list">


        <li>

            <strong>PIHAK PERTAMA</strong>
            berkewajiban untuk menyerahkan
            <em>Handy Talkie</em> (HT) beserta seluruh
            kelengkapannya (charger, antenna, dan earphone)
            dalam kondisi baik, layak pakai, berfungsi dengan baik,
            dan sesuai spesifikasi serta jumlah sebagaimana
            tercantum dalam <strong>Pasal I.</strong>

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berkewajiban mengatur dan menetapkan pembagian frekuensi
            <em>Handy Talkie</em> (HT) berdasarkan jumlah divisi
            yang disampaikan oleh <strong>PIHAK KEDUA.</strong>

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berkewajiban untuk melakukan pengecekan teknis
            (<em>quality control</em>) bersama
            <strong>PIHAK KEDUA</strong>
            dan memberikan panduan penggunaan maupun cara pengisian
            daya <em>Handy Talkie</em> (HT) kepada
            <strong>PIHAK KEDUA</strong>
            pada saat serah terima.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berkewajiban menerima komplain dan menyediakan
            <em>Handy Talkie</em> HT pengganti sesuai ketersediaan
            unit dengan spesifikasi setara atau lebih baik, apabila
            ditemukan HT yang cacat, error, atau rusak saat serah
            terima dan <em>quality control</em>. Setelah proses
            <em>quality control</em> selesai,
            <strong>PIHAK PERTAMA</strong>
            tidak berkewajiban menerima komplain lanjutan.

        </li>


        <li>

            <strong>PIHAK PERTAMA</strong>
            berkewajiban memberikan pemberitahuan kepada
            <strong>PIHAK KEDUA</strong>
            apabila terdapat kendala atau perubahan ketersediaan
            barang yang dapat mempengaruhi penyerahan barang sesuai
            dengan kesepakatan, segera setelah kondisi tersebut
            diketahui oleh <strong>PIHAK PERTAMA.</strong>

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 7 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="article-title">

        <span class="line">
            PASAL III
        </span>

        <span class="line">
            HAK DAN KEWAJIBAN PIHAK KEDUA
        </span>

    </div>



    <div class="sub-heading">
        Hak:
    </div>



    <ol class="legal-list">


        <li>

            <strong>PIHAK KEDUA</strong>
            berhak mengambil dan menggunakan peralatan komunikasi
            berupa <em>Handy Talkie</em> (HT) beserta seluruh
            kelengkapannya (charger, antenna, dan earphone) dalam
            kondisi baik, layak pakai, berfungsi dengan baik, dan
            sesuai spesifikasi serta jumlah sebagaimana tercantum
            dalam <strong>Pasal I.</strong>

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berhak melakukan pengecekan barang
            (<em>quality control</em>) dan mendapatkan panduan atau
            pengarahan cara penggunaan maupun cara pengisian daya
            <em>Handy Talkie</em> (HT) dari
            <strong>PIHAK PERTAMA</strong>
            pada saat serah terima barang.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berhak mengajukan komplain dan meminta unit pengganti
            (<em>replacement</em>) dengan spesifikasi setara atau
            lebih baik, disesuaikan dengan ketersediaan dari
            <strong>PIHAK PERTAMA</strong>, apabila ditemukan
            <em>Handy Talkie</em> (HT) yang cacat, error, atau rusak
            pada saat serah terima dan <em>quality control</em>.
            Setelah proses <em>quality control</em> selesai,
            <strong>PIHAK PERTAMA</strong>
            tidak lagi dapat menerima komplain.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berhak menerima pemberitahuan dari
            <strong>PIHAK PERTAMA</strong>
            apabila terdapat kendala atau perubahan ketersediaan
            barang yang dapat mempengaruhi penyerahan barang sesuai
            dengan kesepakatan.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berhak menerima hasil pemeriksaan terhadap barang yang
            dikembalikan apabila ditemukan kerusakan, kehilangan,
            atau ketidaksesuaian pada barang, sesuai dengan ketentuan
            dalam <strong>Pasal V.</strong>

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 8 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="sub-heading">
        Kewajiban:
    </div>



    <ol class="legal-list">


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban membayar biaya penyewaan peralatan dengan
            skema, nominal dan jadwal yang telah disepakati oleh
            <strong>PARA PIHAK</strong>, sesuai dengan ketentuan
            dalam <strong>Pasal IV.</strong>

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban menyediakan daftar plotting panitia yang
            menggunakan HT serta menyampaikan jumlah divisi kepada
            <strong>PIHAK PERTAMA</strong>
            untuk keperluan pengaturan frekuensi.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban menggunakan
            <em>Handy Talkie</em> (HT) sesuai dengan fungsi dan
            pengaturan (<em>setting</em>) yang telah ditentukan oleh
            <strong>PIHAK PERTAMA</strong>
            serta dilarang melakukan pembongkaran, modifikasi,
            perbaikan, atau perubahan frekuensi tanpa persetujuan
            terlebih dahulu oleh <strong>PIHAK PERTAMA.</strong>

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk menjaga dan mengembalikan seluruh
            peralatan kepada <strong>PIHAK PERTAMA</strong>
            dalam kondisi baik, layak pakai, dan berfungsi dengan
            baik. Apabila terjadi kerusakan atau kehilangan akibat
            kesengajaan, kelalaian, maupun penggunaan yang tidak
            sesuai oleh <strong>PIHAK KEDUA</strong>, maka
            <strong>PIHAK PERTAMA</strong> berhak menuntut
            pertanggungjawaban kepada <strong>PIHAK KEDUA</strong>
            sebagaimana tercantum dalam <strong>Pasal V</strong>
            mengenai sanksi.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban melakukan pengisian daya baterai
            <em>Handy Talkie</em> (HT) selama masa peminjaman untuk
            memastikan <em>Handy Talkie</em> (HT) dapat digunakan
            pada hari berikutnya, serta mengembalikan seluruh
            <em>Handy Talkie</em> dalam kondisi baterai terisi penuh
            (<em>fully charged</em>).

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengembalikan seluruh
            <em>Handy Talkie</em> (HT) tepat waktu sesuai yang sudah
            disepakati sebagaimana diatur dalam
            <strong>Pasal I</strong>
            dengan kondisi baik, layak pakai, berfungsi dengan baik,
            baterai dalam keadaan terisi penuh
            (<em>fully charged</em>).

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 9 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <ol
        class="legal-list"
        start="7"
    >


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunduh, menandatangani, dan
            mengunggah kembali dokumen
            <em>Memorandum of Understanding</em> (MoU) ke dalam
            website Inventory Student Council apabila pesanan berada
            pada status “<em>Waiting for MoU</em>”.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban melakukan pembayaran sesuai dengan nominal
            yang tertera pada Invoice dan mengunggah bukti transfer
            ke dalam website Inventory Student Council apabila
            pesanan berada pada status status
            “<em>Waiting for Payment</em>”.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunduh, menandatangani, dan
            mengunggah kembali dokumen Kwitansi ke dalam website
            Inventory Student Council sebagai bukti sah
            penyelesaian administrasi apabila pesanan berada pada
            status “<em>Waiting for Kwitansi</em>”.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunggah tautan (<em>link</em>)
            Google Drive ke dalam website Inventory Student Council
            sebelum mengembalikan barang secara fisik apabila pesanan
            memasuki status “<em>Handed Over</em>”, dengan ketentuan
            tautan wajib berisi bukti dokumentasi foto kondisi setiap
            tipe barang dan penamaan file foto di dalam Google Drive
            harus sama persis dengan penamaan barang yang tertera di
            website.

        </li>


        <li>

            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunduh, mengisi lampiran bukti
            transfer, menandatangani, dan mengunggah kembali dokumen
            Berita Acara Kerusakan dan/atau Kehilangan Barang ke
            dalam website Inventory Student Council apabila pesanan
            berada pada status “<em>Returned(Damaged)</em>” akibat
            kerusakan dan/atau kehilangan barang selama masa
            peminjaman.

        </li>


    </ol>



    <div class="article-title mt-medium">

        <span class="line">
            PASAL IV
        </span>

        <span class="line">
            BIAYA
        </span>

    </div>



    <ol class="legal-list">


        <li>

            Harga total dari barang-barang sebagaimana dicantumkan
            dalam Pasal I Perjanjian ini, dibayar oleh
            <strong>PIHAK KEDUA</strong>
            kepada <strong>PIHAK PERTAMA</strong>
            sebesar

            <span class="{{ $dynamicClass($htTotal) }}">

                Rp {{ $money($htTotal) }}-

            </span>

            (

            <span class="{{ $dynamicClass($htTotal) }}">

                {{ trim($terbilang($htTotal)) }} Rupiah

            </span>

            ).

        </li>


        <li>

            Nominal tersebut sudah dalam nilai bersih (neto).

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 10 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <ol
        class="legal-list"
        start="3"
    >


        <li>

            Pembayaran dilakukan secara lunas oleh
            <strong>PIHAK KEDUA</strong>
            kepada <strong>PIHAK PIHAK PERTAMA</strong>
            melalui transfer bank, setelah surat perjanjian ini
            ditandatangani dan paling lambat pada hari pengambilan
            barang.

        </li>


        <li>

            Metode pembayaran akan dilakukan melalui transfer bank
            dengan rincian:

        </li>


    </ol>



    <ol
        class="legal-list alpha"
        type="a"
    >


        <li>
            Nama Bank : Bank Central Asia (BCA)
        </li>

        <li>
            Nama Rekening : Chalistha Dea Yuwanda
        </li>

        <li>
            Nomor Rekening : 8620797163
        </li>


    </ol>



    <div class="article-title mt-medium">

        <span class="line">
            PASAL V
        </span>

        <span class="line">
            SANKSI
        </span>

    </div>



    <ol class="legal-list">


        <li>

            Apabila salah satu PIHAK tidak melaksanakan hak dan/atau
            kewajibannya sebagaimana diatur dalam Surat Perjanjian
            Kerja Sama ini, maka PIHAK lainnya berhak memberikan
            teguran dan meminta pemenuhan kewajiban tersebut dalam
            jangka waktu yang disepakati oleh PARA PIHAK.

        </li>


        <li>

            Apabila salah satu PIHAK tidak melaksanakan hak dan/atau
            kewajibannya sebagaimana diatur dalam Surat Perjanjian
            Kerja Sama ini, maka PIHAK lainnya berhak memberikan
            teguran dan meminta pemenuhan kewajiban tersebut dalam
            jangka waktu yang disepakati oleh PARA PIHAK.

        </li>


        <li>

            Apabila terjadi kerusakan atau kehilangan akibat
            kesengajaan, kelalaian, maupun penggunaan yang tidak
            sesuai oleh PIHAK KEDUA, maka PIHAK KEDUA wajib
            menanggung biaya penggantian sesuai dengan komponen yang
            rusak atau hilang, dengan rincian sebagai berikut:

        </li>


    </ol>



    <ol
        class="legal-list alpha"
        type="a"
    >


        <li>
            Unit HT Baofeng UV-5r : Rp500.000,-
        </li>

        <li>
            Housing Fullset Baofeng UV-5r : Rp250.000,-
        </li>

        <li>
            Layar LCD Baofeng UV-5r : Rp160.000,-
        </li>

        <li>
            Potensiometer Baofeng UV-5r : Rp 160.000,-
        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 11 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <ol
        class="legal-list alpha"
        type="a"
        start="5"
    >


        <li>
            Regulator Baofeng UV-5rr : Rp150.000,-
        </li>

        <li>
            Baterai Baofeng UV-5r : Rp175.000,-
        </li>

        <li>
            Earphone Baofeng UV-5r : Rp35.000,-
        </li>

        <li>
            Charger Baofeng UV-5r : Rp50.000,-
        </li>

        <li>
            Antenna Baofeng UV-5r : Rp35.000,-
        </li>

        <li>
            Unit HT Baofeng UV82 : Rp.375.000,-
        </li>

        <li>
            Headset dual PTT Baofeng UV82 : Rp.75.000,-
        </li>

        <li>
            Charger Baofeng UV82 : Rp.60.000,-
        </li>

        <li>
            Baterai Baofeng UV82 : Rp.100.000,-
        </li>

        <li>
            Antenna Baofeng UV82 : Rp.45.000,-
        </li>

        <li>
            Unit HT Baofeng 888s : Rp 200.000,-
        </li>

        <li>
            Headset Baofeng 888s : Rp 20.000,-
        </li>

        <li>
            Charger Baofeng 888s : Rp 25.000,-
        </li>

        <li>
            Baterai Baofeng 888s : Rp 50.000,-
        </li>

        <li>
            Antenna Baofeng 888s : Rp 20.000,-
        </li>


    </ol>



    <p class="paragraph">

        Kerusakan atau kehilangan komponen lain yang belum tercantum
        dalam daftar tersebut akan dibahas dan disepakati lebih
        lanjut oleh <strong>PARA PIHAK</strong> berdasarkan kondisi
        kerusakan atau kehilangan yang terjadi.

    </p>



    <ol
        class="legal-list"
        start="4"
    >


        <li>

            Apabila setelah diberikan teguran sebagaimana dimaksud
            pada ayat (1) pihak yang bersangkutan tetap tidak
            memenuhi kewajibannya tanpa alasan yang dapat
            dipertanggungjawabkan, maka PIHAK lainnya berhak
            mengenakan sanksi sesuai dengan ketentuan dalam Surat
            Perjanjian Kerja Sama ini, termasuk namun tidak terbatas
            pada:

            <ol
                class="legal-list alpha"
                type="a"
            >

                <li>

                    pengakhiran Surat Perjanjian Kerja Sama atas
                    kesepakatan atau karena wanprestasi;

                </li>

                <li>

                    pembayaran ganti rugi atau kompensasi sesuai
                    dengan ketentuan yang berlaku dalam Surat
                    Perjanjian Kerja Sama ini.

                </li>

            </ol>

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 12 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <ol
        class="legal-list"
        start="5"
    >


        <li>

            Pembayaran kompensasi atau ganti rugi sebagaimana
            dimaksud dalam Pasal ini wajib diselesaikan paling lambat
            7 (tujuh) hari kerja sejak disepakatinya nilai kompensasi
            atau ganti rugi oleh <strong>PARA PIHAK.</strong>

        </li>


    </ol>



    <div class="article-title">

        <span class="line">
            PASAL VI
        </span>

        <span class="line">
            JANGKA WAKTU PERJANJIAN
        </span>

    </div>



    <p class="paragraph">

        Perjanjian ini berlaku sejak tanggal ditandatangani oleh
        <strong>PARA PIHAK</strong>
        sampai dengan seluruh hak dan kewajiban
        <strong>PARA PIHAK</strong>
        berdasarkan Perjanjian ini telah dipenuhi, termasuk namun
        tidak terbatas pada penyerahan dan pengembalian
        <em>Handy Talkie</em> (HT), pembayaran biaya sewa, serta
        penyelesaian kewajiban lainnya sesuai dengan ketentuan dalam
        Perjanjian ini.

    </p>



    <div class="article-title">

        <span class="line">
            PASAL VII
        </span>

        <span class="line">
            PENYELESAIAN PERSELISIHAN
        </span>

    </div>



    <p class="paragraph">

        Bila terjadi perselisihan antara
        <strong>PIHAK PERTAMA</strong>
        dan <strong>PIHAK KEDUA</strong>
        maka akan diselesaikan dengan cara musyawarah dan mufakat.

    </p>



    <div class="article-title">

        <span class="line">
            PASAL VIII
        </span>

        <span class="line">
            LAIN-LAIN
        </span>

    </div>



    <ol class="legal-list">


        <li>

            Hal-hal yang belum diatur dalam Surat Perjanjian Kerja
            Sama ini akan disepakati kemudian oleh
            <strong>PARA PIHAK</strong>
            dan menjadi bagian yang tidak terpisahkan dari Perjanjian
            ini. Setiap perubahan terhadap Surat Perjanjian Kerja Sama
            ini hanya sah apabila dibuat secara tertulis dan disetujui
            oleh <strong>PARA PIHAK.</strong>

        </li>


        <li>

            Surat Perjanjian Kerja Sama ini disepakati semata-mata
            untuk menjaga agar tidak terjadi perselisihan antara
            kedua belah pihak tanpa ada maksud lain.

        </li>


    </ol>

</div>



<!-- ================================================================== -->
<!-- PAGE 13 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if($logoPath && file_exists($logoPath))

                        <img
                            src="{{ $logoPath }}"
                            alt="Student Council"
                        >

                    @endif

                </td>


                <td class="header-text">

                    <div class="header-main">
                        UNIVERSITAS CIPUTRA SURABAYA
                    </div>

                    <div class="header-main">
                        STUDENT COUNCIL
                    </div>

                    <div class="header-sub">
                        SURAT PERJANJIAN KERJASAMA
                    </div>

                    <div class="header-sub">
                        VENDOR SATU PINTU
                    </div>

                    <div class="header-address">

                        Citraland CBD Boulevard, Surabaya, 60219

                        <br>

                        Jawa Timur - Indonesia

                        <br>

                        Telepon: (031)7451699; Fax: (031)7451698

                        <br>

                        Email: studentcouncil@ciputra.ac.id

                    </div>

                </td>

            </tr>

        </table>

    </div>



    <div class="article-title">

        <span class="line">
            PASAL IX
        </span>

        <span class="line">
            PENUTUP
        </span>

    </div>



    <p class="paragraph">

        Demikian Surat Perjanjian Kerja Sama ini dibuat atas
        kesepakatan kedua belah pihak secara sadar dan tanpa tekanan
        dari pihak manapun. Serta dibuat 2 (dua) rangkap yang
        masing-masing mempunyai kekuatan hukum yang sama.

    </p>



    <div class="signature-date">

        Surabaya,

        <span class="{{ $dynamicClass($agreementDateShort) }}">

            {{ $agreementDateShort ?: '-' }}

        </span>

    </div>



    <table class="signature-table">

        <tr>


            <!-- PIHAK PERTAMA -->

            <td>

                <div class="signature-title">
                    PIHAK PERTAMA
                </div>


                <div class="signature-image">

                    @if($ttdPath && file_exists($ttdPath))

                        <img
                            src="{{ $ttdPath }}"
                            alt="TTD Bendahara"
                        >

                    @endif

                </div>


                <div class="signature-name">
                    Gregory Edgard Christian
                </div>


                <div class="signature-role">

                    Bendahara Student Council Universitas Ciputra

                    <br>

                    Surabaya

                </div>

            </td>



            <!-- PIHAK KEDUA -->

            <td>

                <div class="signature-title">
                    PIHAK KEDUA
                </div>


                <div class="signature-space">
                </div>


                <div class="{{ $dynamicClass($partyTwoName) }} signature-name">

                    {{ $partyTwoName ?: '-' }}

                </div>


                <div class="{{ $dynamicClass($partyTwoPosition) }} signature-role">

                    {{ $partyTwoPosition ?: '-' }}

                </div>

            </td>


        </tr>

    </table>

</div>



</body>

</html>