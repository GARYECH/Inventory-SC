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
        | 1 .page = 1 halaman A4.
        | Margin dibuat lewat padding .page.
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

            font-family:
                "Times New Roman",
                Times,
                serif;

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
        | MANUAL PAGE
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
        | ARTICLE
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
        | ITEMS TABLE
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

            text-align: right;
        }

        .col-days {
            width: 10%;

            text-align: center;
        }

        .col-subtotal {
            width: 19%;

            text-align: right;
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
            margin-bottom: 24mm;
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

    $logoDb =
        \App\Models\Setting::where(
            'key',
            'logo_sc'
        )->value(
            'value'
        );

    $ttdDb =
        \App\Models\Setting::where(
            'key',
            'ttd_bendahara'
        )->value(
            'value'
        );


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


    /*
    |--------------------------------------------------------------------------
    | PARTY 2
    |--------------------------------------------------------------------------
    */

    $partyTwoName =
        $order->full_name
        ??
        optional(
            $order->user
        )->name
        ??
        '';

    $partyTwoPosition =
        $order->position
        ??
        '';

    $partyTwoOrganization =
        $order->organization
        ??
        '';

    $partyTwoAddress =
        $order->address
        ??
        '';

    $partyTwoPhone =
        $order->phone_number
        ??
        '';

    $eventName =
        $order->proker_name
        ??
        '';


    /*
    |--------------------------------------------------------------------------
    | DATES
    |--------------------------------------------------------------------------
    */

    $agreementDate =
        $order->created_at
            ? \Carbon\Carbon::parse(
                $order->created_at
            )->locale('id')
                ->translatedFormat(
                    'l, d F Y'
                )
            : '';

    $agreementDateShort =
        $order->created_at
            ? \Carbon\Carbon::parse(
                $order->created_at
            )->locale('id')
                ->translatedFormat(
                    'd F Y'
                )
            : '';


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
    | INTERNAL RENTAL ITEMS
    |--------------------------------------------------------------------------
    |
    | HANYA barang dengan:
    |
    | transaction_type   = Peralatan
    | transaction_detail = Internal Rental
    |
    */

    $internalItems =
        $order->orderItems
            ->filter(
                function (
                    $detail
                ) {

                    return
                        $detail->item &&
                        $detail->item->transaction_type ===
                            'Peralatan' &&
                        $detail->item->transaction_detail ===
                            'Internal Rental';

                }
            )
            ->values();


    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    $grandTotal =
        (int) $internalItems->sum(
            function (
                $detail
            ) {

                return (int) (
                    $detail->subtotal_price
                    ??
                    0
                );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | DYNAMIC CLASS
    |--------------------------------------------------------------------------
    */

    $dynamicClass =
        function (
            $value
        ) {

            if (
                $value === null
                ||
                trim(
                    (string) $value
                ) === ''
            ) {

                return 'dynamic empty';

            }

            return 'dynamic filled';

        };


    /*
    |--------------------------------------------------------------------------
    | MONEY
    |--------------------------------------------------------------------------
    */

    $money =
        function (
            $value
        ) {

            return number_format(
                (int) $value,
                2,
                ',',
                '.'
            );

        };


    /*
    |--------------------------------------------------------------------------
    | TERBILANG
    |--------------------------------------------------------------------------
    */

    $terbilang =
        function (
            $number
        ) use (
            &$terbilang
        ) {

            $number =
                (int) $number;


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


            if (
                $number === 0
            ) {
                return 'Nol';
            }


            if (
                $number < 12
            ) {
                return $words[
                    $number
                ];
            }


            if (
                $number < 20
            ) {

                return
                    $words[
                        $number - 10
                    ] .
                    ' Belas';

            }


            if (
                $number < 100
            ) {

                $result =
                    $words[
                        (int) (
                            $number / 10
                        )
                    ] .
                    ' Puluh';


                $remainder =
                    $number % 10;


                if (
                    $remainder > 0
                ) {

                    $result .=
                        ' ' .
                        $terbilang(
                            $remainder
                        );

                }


                return $result;

            }


            if (
                $number < 200
            ) {

                $remainder =
                    $number - 100;


                return
                    'Seratus' .
                    (
                        $remainder > 0
                            ? ' ' .
                                $terbilang(
                                    $remainder
                                )
                            : ''
                    );

            }


            if (
                $number < 1000
            ) {

                $result =
                    $terbilang(
                        (int) (
                            $number / 100
                        )
                    ) .
                    ' Ratus';


                $remainder =
                    $number % 100;


                if (
                    $remainder > 0
                ) {

                    $result .=
                        ' ' .
                        $terbilang(
                            $remainder
                        );

                }


                return $result;

            }


            if (
                $number < 2000
            ) {

                $remainder =
                    $number - 1000;


                return
                    'Seribu' .
                    (
                        $remainder > 0
                            ? ' ' .
                                $terbilang(
                                    $remainder
                                )
                            : ''
                    );

            }


            if (
                $number < 1000000
            ) {

                $result =
                    $terbilang(
                        (int) (
                            $number / 1000
                        )
                    ) .
                    ' Ribu';


                $remainder =
                    $number % 1000;


                if (
                    $remainder > 0
                ) {

                    $result .=
                        ' ' .
                        $terbilang(
                            $remainder
                        );

                }


                return $result;

            }


            if (
                $number < 1000000000
            ) {

                $result =
                    $terbilang(
                        (int) (
                            $number / 1000000
                        )
                    ) .
                    ' Juta';


                $remainder =
                    $number % 1000000;


                if (
                    $remainder > 0
                ) {

                    $result .=
                        ' ' .
                        $terbilang(
                            $remainder
                        );

                }


                return $result;

            }


            if (
                $number < 1000000000000
            ) {

                $result =
                    $terbilang(
                        (int) (
                            $number / 1000000000
                        )
                    ) .
                    ' Miliar';


                $remainder =
                    $number % 1000000000;


                if (
                    $remainder > 0
                ) {

                    $result .=
                        ' ' .
                        $terbilang(
                            $remainder
                        );

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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
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
            SURAT PERJANJIAN KERJA SAMA
        </div>


        <div class="title-number">

            Nomor:

            <span
                class="{{ $dynamicClass(
                    $order->mou_number
                    ??
                    $order->order_number
                ) }}"
            >

                {{
                    $order->mou_number
                    ??
                    $order->order_number
                }}

            </span>

        </div>

    </div>


    <p class="paragraph">

        Pada hari ini

        <span
            class="{{ $dynamicClass(
                $agreementDate
            ) }}"
        >
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

        Yang selanjutnya dalam Surat Perjanjian Kerja Sama ini
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

                <span
                    class="{{ $dynamicClass(
                        $partyTwoName
                    ) }}"
                >
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

                <span
                    class="{{ $dynamicClass(
                        $partyTwoPosition
                    ) }}"
                >
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

                <span
                    class="{{ $dynamicClass(
                        $partyTwoOrganization
                    ) }}"
                >
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

                <span
                    class="{{ $dynamicClass(
                        $partyTwoAddress
                    ) }}"
                >
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

                <span
                    class="{{ $dynamicClass(
                        $partyTwoPhone
                    ) }}"
                >
                    {{ $partyTwoPhone ?: '-' }}
                </span>

            </td>

        </tr>

    </table>


    <p class="party-description">

        Yang selanjutnya dalam Surat Perjanjian Kerjasama ini
        bertindak untuk dan atas nama

        <span
            class="{{ $dynamicClass(
                $partyTwoOrganization
            ) }}"
        >
            {{ $partyTwoOrganization ?: '-' }}
        </span>

        disebut sebagai <strong>PIHAK KEDUA.</strong>

    </p>


    <p class="paragraph">

        Berdasarkan hal-hal tersebut di atas,
        <strong>PARA PIHAK</strong> sepakat untuk membuat,
        menandatangani, dan melaksanakan
        <em>Memorandum of Understanding</em> (MoU)
        tentang penyewaan barang untuk kegiatan

        <span
            class="{{ $dynamicClass(
                $eventName
            ) }}"
        >
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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
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
        penyewaan barang sesuai dengan kebutuhan
        <strong>PIHAK KEDUA</strong> untuk pelaksanaan kegiatan

        <span
            class="{{ $dynamicClass(
                $eventName
            ) }}"
        >
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

                <span
                    class="{{ $dynamicClass(
                        $order->start_date
                    ) }}"
                >

                    {{
                        $order->start_date
                            ? \Carbon\Carbon::parse(
                                $order->start_date
                            )->locale('id')
                                ->translatedFormat(
                                    'l, d F Y'
                                )
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

                <span
                    class="{{ $dynamicClass(
                        $order->start_time
                    ) }}"
                >

                    {{
                        $order->start_time
                            ? \Carbon\Carbon::parse(
                                $order->start_time
                            )->format(
                                'H.i'
                            ) . ' WIB'
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

                Ruang Student Council,
                Main Building Lantai 2,
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

                <span
                    class="{{ $dynamicClass(
                        $order->end_date
                    ) }}"
                >

                    {{
                        $order->end_date
                            ? \Carbon\Carbon::parse(
                                $order->end_date
                            )->locale('id')
                                ->translatedFormat(
                                    'l, d F Y'
                                )
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

                <span
                    class="{{ $dynamicClass(
                        $order->end_time
                    ) }}"
                >

                    {{
                        $order->end_time
                            ? \Carbon\Carbon::parse(
                                $order->end_time
                            )->format(
                                'H.i'
                            ) . ' WIB'
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

                Ruang Student Council,
                Main Building Lantai 2,
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

                <span
                    class="{{ $dynamicClass(
                        $rentalDays
                    ) }}"
                >

                    {{ $rentalDays ?? 0 }} hari

                </span>

            </td>

        </tr>

    </table>


    <!-- ============================================================= -->
    <!-- ORDER ITEMS -->
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

            @if(
                $internalItems->count() > 0
            )

                @foreach(
                    $internalItems
                    as $index =>
                    $detail
                )

                    @php

                        $quantity =
                            (int) (
                                $detail->quantity
                                ??
                                0
                            );


                        $subtotal =
                            (int) (
                                $detail->subtotal_price
                                ??
                                0
                            );


                        $itemName =
                            $detail->item->name
                            ??
                            '';


                        $baseUnitPrice =
                            $detail->item
                                ? (int) (
                                    $detail->item->price
                                )
                                : 0;


                        /*
                        |--------------------------------------------------------------------------
                        | STUDENT COUNCIL FREE
                        |--------------------------------------------------------------------------
                        |
                        | Semua Internal Rental termasuk
                        | kategori Peralatan.
                        |
                        */

                        $isFree =
                            $order->organization ===
                            'Student Council';


                        $unitPrice =
                            $isFree
                                ? 0
                                : $baseUnitPrice;

                    @endphp


                    <tr>

                        <!-- NO -->

                        <td class="col-no">

                            {{ $index + 1 }}

                        </td>


                        <!-- DESCRIPTION -->

                        <td class="col-description">

                            <span
                                class="{{ $dynamicClass(
                                    $itemName
                                ) }}"
                            >

                                {{
                                    $itemName ?: '-'
                                }}

                            </span>


                            <br>


                            <span
                                style="
                                    font-size:8pt;
                                    color:#555555;
                                "
                            >

                                Internal Rental

                            </span>

                        </td>


                        <!-- QUANTITY -->

                        <td class="col-quantity">

                            <span class="dynamic filled">

                                {{ $quantity }}

                            </span>

                        </td>


                        <!-- UNIT -->

                        <td class="col-unit">

                            <em>Pcs</em>

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

                                            {{
                                                $money(
                                                    $unitPrice
                                                )
                                            }}

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


                            @if($isFree)

                                <div
                                    class="formula"
                                    style="color:#a00000;"
                                >

                                    FREE (SC)

                                </div>

                            @else

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

                            @endif


                            <table class="money-table">

                                <tr>

                                    <td class="money-prefix">
                                        Rp
                                    </td>

                                    <td class="money-value">

                                        {{
                                            $money(
                                                $subtotal
                                            )
                                        }}

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
                        style="height:18mm;"
                    >

                        Tidak ada barang Internal Rental
                        yang dipesan.

                    </td>

                </tr>

            @endif


            <!-- TOTAL -->

            <tr>

                <td
                    colspan="6"
                    class="center"
                    style="height:12mm;"
                >

                    <strong>
                        Total
                    </strong>

                </td>


                <td class="col-subtotal">

                    <table class="money-table">

                        <tr>

                            <td class="money-prefix">
                                Rp
                            </td>

                            <td class="money-value">

                                <strong>
                                    {{
                                        $money(
                                            $grandTotal
                                        )
                                    }}
                                </strong>

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
                        <br>
                        Telepon: (031)7451699; Fax: (031)7451698
                        <br>
                        Email: studentcouncil@ciputra.ac.id
                    </div>

                </td>

            </tr>

        </table>

    </div>

</div>


<!-- ================================================================== -->
<!-- PAGE 4 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
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
            tercantum dalam Pasal IV.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima kembali barang yang disewakan sesuai
            dengan jumlah, spesifikasi, waktu dan lokasi pengembalian
            sebagaimana tercantum dalam Pasal I.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima kembali barang beserta seluruh bagian,
            komponen, dan kelengkapannya dalam kondisi baik, layak
            pakai, serta dapat digunakan sebagaimana mestinya sesuai
            dengan fungsi dan peruntukannya.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima pemberitahuan dari
            <strong>PIHAK KEDUA</strong>
            apabila selama masa penggunaan terjadi kerusakan,
            kehilangan, atau kondisi lain yang dapat mempengaruhi
            keadaan barang.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima kompensasi dari
            <strong>PIHAK KEDUA</strong>
            apabila terjadi kerusakan atau kehilangan barang yang
            menjadi tanggung jawab PIHAK KEDUA, sesuai dengan
            ketentuan dalam Pasal V.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima kembali dokumen
            <em>Memorandum of Understanding</em> (MoU) yang telah
            ditandatangani oleh <strong>PIHAK KEDUA</strong> melalui
            website Inventory Student Council pada status
            “<em>Waiting for MoU</em>” sebagai syarat persetujuan
            pesanan.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima bukti transfer atau bukti pembayaran dari
            <strong>PIHAK KEDUA</strong> melalui website Inventory
            Student Council sebagai tindak lanjut atas penerbitan
            invoice.
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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
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
            berhak menerima kuitansi yang telah ditandatangani oleh
            <strong>PIHAK KEDUA</strong> melalui website Inventory
            Student Council pada status “<em>Waiting for
            Pembayaran</em>” sebagai bukti sah penyelesaian
            administrasi keuangan.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima tautan (link) Google Drive dari
            <strong>PIHAK KEDUA</strong> melalui website Inventory
            Student Council saat proses pengembalian
            (<em>return</em>) pada status “<em>Handed Over</em>”,
            yang berisi bukti dokumentasi foto kondisi per tipe
            barang dengan ketentuan penamaan file foto yang wajib
            disamakan dengan penamaan barang di website.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima dokumen Berita Acara Kerusakan dan/atau
            Kehilangan Barang yang telah diisi dan ditandatangani
            oleh <strong>PIHAK KEDUA</strong> melalui website
            Inventory Student Council pada status
            “<em>Returned (Damaged)</em>” apabila terjadi kendala,
            kerusakan, atau kehilangan barang selama masa peminjaman.
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
                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
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
            berkewajiban untuk menyerahkan barang dengan tepat waktu,
            sesuai jumlah dan spesifikasi sebagaimana diatur dalam
            Pasal I, dalam kondisi baik, layak pakai, berfungsi
            dengan baik.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berkewajiban menerima komplain dan menyediakan barang
            pengganti sesuai ketersediaan unit dengan spesifikasi
            setara atau lebih baik, apabila ditemukan barang yang
            cacat/rusak saat penyerahan dan pengecekan barang sesuai
            dengan hari, tanggal, waktu dan lokasi sebagaimana
            tercantum dalam Pasal I. Setelah proses pengecekan barang
            selesai, <strong>PIHAK PERTAMA</strong> tidak berkewajiban
            menerima komplain lanjutan terkait kondisi barang yang
            dapat diketahui pada saat proses tersebut.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berkewajiban memberikan pemberitahuan kepada
            <strong>PIHAK KEDUA</strong> apabila terdapat kendala
            atau perubahan ketersediaan barang yang dapat
            mempengaruhi penyerahan barang sesuai dengan kesepakatan,
            segera setelah kondisi tersebut diketahui oleh
            <strong>PIHAK PERTAMA.</strong>
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berkewajiban melakukan pemeriksaan terhadap barang yang
            dikembalikan dan mengembalikan pembayaran yang telah
            diterima dari <strong>PIHAK KEDUA</strong> apabila
            sebelum atau pada saat penyerahan barang ditemukan kondisi
            yang menyebabkan barang tidak dapat diserahkan sesuai
            dengan jumlah, spesifikasi, kondisi, atau fungsi
            sebagaimana telah disepakati dan
            <strong>PIHAK PERTAMA</strong> tidak dapat menyediakan
            barang pengganti sesuai dengan ketentuan yang disepakati
            oleh <strong>PARA PIHAK.</strong>
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berkewajiban menyampaikan hasil pengecekan barang kepada
            <strong>PIHAK KEDUA</strong> apabila pada saat
            pengembalian ditemukan kerusakan, kehilangan, atau
            ketidaksesuaian pada barang, termasuk bentuk dan besaran
            kompensasi sesuai dengan ketentuan dalam Pasal V.
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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
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
            berhak menerima barang sesuai dengan jumlah dan
            spesifikasi sebagaimana diatur dalam Pasal I, dengan
            waktu dan lokasi penyerahan sebagaimana tercantum dalam
            Pasal I, dalam kondisi baik, layak pakai, dan berfungsi
            dengan baik.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berhak mengajukan komplain dan memperoleh barang
            pengganti sesuai ketersediaan unit dengan spesifikasi
            setara atau lebih baik, apabila ditemukan barang yang
            cacat/rusak saat penyerahan dan pengecekan barang sesuai
            dengan hari, tanggal, waktu dan lokasi sebagaimana
            tercantum dalam Pasal I. Setelah proses pengecekan barang
            selesai, <strong>PIHAK KEDUA</strong> tidak berhak
            mengajukan komplain lanjutan terkait kondisi barang yang
            dapat diketahui pada saat proses tersebut.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berhak menerima pemberitahuan dari
            <strong>PIHAK PERTAMA</strong> apabila terdapat kendala
            atau perubahan ketersediaan barang yang dapat
            mempengaruhi penyerahan barang sesuai dengan kesepakatan.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berhak menerima kembali pembayaran yang telah dilakukan
            kepada <strong>PIHAK PERTAMA</strong> apabila sebelum
            atau pada saat penyerahan barang ditemukan kondisi yang
            menyebabkan barang tidak dapat diserahkan sesuai dengan
            jumlah, spesifikasi, kondisi, atau fungsi sebagaimana
            telah disepakati dan <strong>PIHAK PERTAMA</strong> tidak
            dapat menyediakan barang pengganti sesuai dengan
            ketentuan yang disepakati oleh <strong>PARA PIHAK.</strong>
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berhak menerima hasil pemeriksaan terhadap barang yang
            dikembalikan apabila ditemukan kerusakan, kehilangan,
            atau ketidaksesuaian pada barang, sesuai dengan ketentuan
            dalam Pasal V.
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
                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
                        <br>
                        Telepon: (031)7451699; Fax: (031)7451698
                        <br>
                        Email: studentcouncil@ciputra.ac.id
                    </div>

                </td>

            </tr>

        </table>

    </div>

</div>


<!-- ================================================================== -->
<!-- PAGE 9 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">
                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
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
            berkewajiban melakukan pembayaran sesuai skema, nominal
            dan jadwal pembayaran yang telah disepakati sebagaimana
            tercantum dalam Pasal IV.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berkewajiban menerima barang sesuai dengan jumlah dan
            spesifikasi sebagaimana diatur dalam Pasal I, dengan
            waktu dan lokasi penyerahan sebagaimana tercantum dalam
            Pasal I.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berkewajiban mengembalikan barang beserta seluruh bagian,
            komponen, dan kelengkapannya dalam kondisi baik, layak
            pakai, serta dapat digunakan sebagaimana mestinya sesuai
            dengan fungsi dan peruntukannya, serta sesuai dengan
            jumlah, spesifikasi, waktu dan lokasi pengembalian
            sebagaimana tercantum dalam Pasal I.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berkewajiban memberikan pemberitahuan kepada
            <strong>PIHAK PERTAMA</strong> apabila selama masa
            penggunaan terjadi kerusakan, kehilangan, atau kondisi
            lain yang dapat mempengaruhi keadaan barang, serta
            memberikan kompensasi apabila kerusakan atau kehilangan
            tersebut menjadi tanggung jawab PIHAK KEDUA, sesuai
            dengan ketentuan dalam Pasal V.
        </li>


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
            berkewajiban untuk melakukan pembayaran sesuai nominal
            yang tertera pada invoice dan mengunggah bukti transfer
            ke dalam website Inventory Student Council apabila
            pesanan berada pada status
            “<em>Waiting for Pembayaran</em>”.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunduh, menandatangani, dan
            mengunggah kembali dokumen kuitansi ke dalam website
            Inventory Student Council sebagai bukti sah penyelesaian
            administrasi apabila pesanan berada pada status
            pengunggahan Kwitansi.
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
                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
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
            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunggah tautan (link) Google Drive
            ke dalam website Inventory Student Council sebelum
            mengembalikan barang secara fisik apabila pesanan
            memasuki pada status “<em>Handed Over</em>”, dengan
            ketentuan tautan wajib berisi bukti dokumentasi foto
            kondisi per tipe barang dan penamaan file foto di dalam
            Google Drive harus sama persis dengan penamaan barang
            yang tertera di website.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunduh, mengisi, menandatangani,
            dan mengunggah kembali dokumen Berita Acara Kerusakan
            dan/atau Kehilangan Barang ke dalam website Inventory
            Student Council pada status “<em>Returned
            (Damaged)</em>” apabila pesanan berada pada status
            penyelesaian masalah akibat kerusakan dan/atau kehilangan
            barang selama masa peminjaman.
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
            <strong>PIHAK KEDUA</strong> kepada
            <strong>PIHAK PERTAMA</strong> sebesar

            <span
                class="{{ $dynamicClass(
                    $grandTotal
                ) }}"
            >
                Rp {{ $money($grandTotal) }}-
            </span>

            (

            <span
                class="{{ $dynamicClass(
                    $grandTotal
                ) }}"
            >

                {{
                    trim(
                        $terbilang(
                            $grandTotal
                        )
                    )
                }}

                Rupiah

            </span>

            ).

        </li>


        <li>

            Biaya sewa dihitung berdasarkan harga sewa per hari,
            dikalikan dengan jumlah hari rental dan jumlah barang
            yang disewa, sesuai dengan rincian pada tabel Pasal I.

        </li>


        <li>
            Nominal tersebut sudah dalam nilai bersih (neto).
        </li>


        <li>
            Pembayaran dilakukan secara lunas oleh
            <strong>PIHAK KEDUA</strong> kepada
            <strong>PIHAK PERTAMA</strong> melalui transfer bank,
            setelah surat perjanjian ini ditandatangani dan paling
            lambat pada tanggal

            <span
                class="{{ $dynamicClass(
                    $order->payment_due_date
                    ??
                    $order->start_date
                ) }}"
            >

                {{
                    $order->payment_due_date
                        ? \Carbon\Carbon::parse(
                            $order->payment_due_date
                        )->locale('id')
                            ->translatedFormat(
                                'd F Y'
                            )
                        : (
                            $order->start_date
                                ? \Carbon\Carbon::parse(
                                    $order->start_date
                                )->locale('id')
                                    ->translatedFormat(
                                        'd F Y'
                                    )
                                : '-'
                        )
                }}

            </span>,

            sebelum pengambilan barang.
        </li>


        <li>

            Metode pembayaran akan dilakukan melalui transfer bank
            dengan rincian:

            <div class="mt-small">

                - Nama Bank :
                Bank Central Asia (BCA)

                <br>

                - Nama Rekening :
                Chalistha Dea Yuwanda

                <br>

                - Nomor Rekening :
                8620797163

            </div>

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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
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

            Apabila terjadi kerusakan atau kehilangan barang akibat
            kesengajaan, kelalaian, maupun penggunaan yang tidak
            sesuai dengan fungsi atau peruntukannya oleh
            <strong>PIHAK KEDUA</strong>, maka
            <strong>PIHAK KEDUA</strong> wajib menanggung biaya
            perbaikan, penggantian bagian atau komponen, maupun
            penggantian barang sesuai dengan tingkat kerusakan atau
            kehilangan yang terjadi.

        </li>


        <li>

            Proses analisis tingkat kerusakan atau kehilangan barang,
            beserta penetapan besaran denda atau nominal biaya
            perbaikan/penggantian sebagaimana dimaksud pada ayat (2),
            dilakukan dan ditetapkan sepenuhnya oleh
            <strong>PIHAK PERTAMA</strong>. Hasil penetapan tersebut
            akan diinformasikan dan didiskusikan secara transparan
            dengan <strong>PIHAK KEDUA</strong> sebagai dasar
            penyelesaian kompensasi.

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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )

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
                        Jawa Timur – Indonesia
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
        start="4"
    >

        <li>

            <strong>PIHAK PERTAMA</strong>
            berkewajiban melakukan pemeriksaan terhadap kondisi
            barang dan menyampaikan Berita Acara Kerusakan dan/atau
            Kehilangan Barang kepada <strong>PIHAK KEDUA</strong>
            yang memuat jenis dan kondisi kerusakan dan/atau
            kehilangan, bagian atau komponen yang terdampak, serta
            besaran biaya perbaikan atau penggantian yang wajib
            dibayarkan oleh <strong>PIHAK KEDUA.</strong>

        </li>


        <li>

            Apabila setelah diberikan teguran sebagaimana dimaksud
            pada ayat (1) pihak yang bersangkutan tetap tidak
            memenuhi kewajibannya tanpa alasan yang dapat
            dipertanggungjawabkan, maka PIHAK lainnya berhak
            mengenakan sanksi sesuai dengan ketentuan dalam Surat
            Perjanjian Kerja Sama ini, termasuk namun tidak terbatas
            pada:

            <div class="mt-small">

                a. pengakhiran Surat Perjanjian Kerja Sama atas
                kesepakatan atau karena wanprestasi;

                <br>

                b. pembayaran ganti rugi atau kompensasi sesuai
                dengan ketentuan yang berlaku dalam Surat Perjanjian
                Kerja Sama ini;

                <br>

                c. tidak direkomendasikan untuk menjalin kerja sama
                kembali dengan Student Council Universitas Ciputra
                Surabaya pada kegiatan selanjutnya.

            </div>

        </li>


        <li>

            Pembayaran kompensasi atau ganti rugi sebagaimana
            dimaksud dalam Pasal ini wajib diselesaikan paling lambat
            7 (tujuh) hari kerja sejak disepakatinya nilai kompensasi
            atau ganti rugi oleh <strong>PARA PIHAK.</strong>

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

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
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
            PASAL VI
        </span>

        <span class="line">
            JANGKA WAKTU PERJANJIAN
        </span>

    </div>


    <p class="paragraph">

        Perjanjian ini berlaku sejak tanggal ditandatangani oleh
        <strong>PARA PIHAK</strong> sampai dengan seluruh hak dan
        kewajiban <strong>PARA PIHAK</strong> berdasarkan Perjanjian
        ini telah dipenuhi, termasuk namun tidak terbatas pada
        penyerahan dan pengembalian barang, pembayaran biaya sewa,
        serta penyelesaian kewajiban lainnya sesuai dengan ketentuan
        dalam Perjanjian ini.

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
        <strong>PIHAK PERTAMA</strong> dan
        <strong>PIHAK KEDUA</strong> maka akan diselesaikan
        dengan cara musyawarah dan mufakat.

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
            <strong>PARA PIHAK</strong> dan menjadi bagian yang tidak
            terpisahkan dari Perjanjian ini. Setiap perubahan
            terhadap Surat Perjanjian Kerja Sama ini hanya sah
            apabila dibuat secara tertulis dan disetujui oleh
            <strong>PARA PIHAK.</strong>
        </li>


        <li>
            Surat Perjanjian Kerja Sama ini disepakati semata-mata
            untuk menjaga agar tidak terjadi perselisihan antara
            kedua belah pihak tanpa ada maksud lain.
        </li>

    </ol>


    <div class="article-title mt-medium">

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

</div>


<!-- ================================================================== -->
<!-- PAGE 14 -->
<!-- ================================================================== -->

<div class="page">

    <div class="header">

        <table class="header-table">

            <tr>

                <td class="header-logo">

                    @if(
                        $logoPath &&
                        file_exists($logoPath)
                    )
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
                        Jawa Timur – Indonesia
                        <br>
                        Telepon: (031)7451699; Fax: (031)7451698
                        <br>
                        Email: studentcouncil@ciputra.ac.id
                    </div>

                </td>

            </tr>

        </table>

    </div>


    <div class="signature-date">

        Surabaya,

        <span
            class="{{ $dynamicClass(
                $agreementDateShort
            ) }}"
        >

            {{
                $agreementDateShort ?: '-'
            }}

        </span>

    </div>


    <table class="signature-table">

        <tr>

            <!-- ===================================================== -->
            <!-- PIHAK PERTAMA -->
            <!-- ===================================================== -->

            <td>

                <div class="signature-title">
                    PIHAK PERTAMA
                </div>


                <div class="signature-image">

                    @if(
                        $ttdPath &&
                        file_exists($ttdPath)
                    )

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


            <!-- ===================================================== -->
            <!-- PIHAK KEDUA -->
            <!-- ===================================================== -->

            <td>

                <div class="signature-title">
                    PIHAK KEDUA
                </div>


                <div class="signature-space">
                </div>


                <div
                    class="{{ $dynamicClass(
                        $partyTwoName
                    ) }} signature-name"
                >
                    {{ $partyTwoName ?: '-' }}
                </div>


                <div
                    class="{{ $dynamicClass(
                        $partyTwoPosition
                    ) }} signature-role"
                >
                    {{ $partyTwoPosition ?: '-' }}
                </div>

            </td>

        </tr>

    </table>

</div>


</body>

</html>