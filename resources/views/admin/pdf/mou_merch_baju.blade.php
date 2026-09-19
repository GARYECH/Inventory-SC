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
        | Margin dibuat melalui padding .page agar konsisten
        | dengan rendering Dompdf yang kita gunakan.
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
        |
        | Data belum ada = MERAH.
        | Data sudah ada = HITAM.
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
        | BAJU ITEMS TABLE
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

            padding: 2mm 1.5mm;

            vertical-align: middle;

            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .items-table th {
            background-color: #d9e5f7;

            text-align: center;

            font-size: 9.5pt;

            font-weight: bold;

            line-height: 1.2;
        }

        .items-table td {
            font-size: 9.5pt;

            line-height: 1.35;
        }

        .col-no {
            width: 7%;

            text-align: center;
        }

        .col-description {
            width: 15%;

            text-align: left;
        }

        .col-division {
            width: 30%;

            text-align: left;
        }

        .col-quantity {
            width: 10%;

            text-align: center;
        }

        .col-unit {
            width: 9%;

            text-align: center;
        }

        .col-price {
            width: 14.5%;
        }

        .col-subtotal {
            width: 14.5%;
        }

        .item-name {
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

            font-size: 9.5pt;

            line-height: 1.2;
        }

        .money-prefix {
            width: 18%;

            text-align: left;
        }

        .money-value {
            width: 82%;

            text-align: right;

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | SPECIFICATION
        |--------------------------------------------------------------------------
        */

        .spec-table {
            width: 100%;

            table-layout: fixed;

            border-collapse: collapse;

            margin-top: 1mm;
        }

        .spec-table td {
            padding: 0;

            border: none;

            vertical-align: top;

            line-height: 1.6;
        }

        .spec-label {
            width: 23%;
        }

        .spec-colon {
            width: 4%;

            text-align: center;
        }

        .spec-value {
            width: 73%;
        }

        .design-link {
            overflow-wrap: break-word;
            word-wrap: break-word;
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
    | PARTY 2 DATA
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
    | DATE
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
    | BAJU ITEMS
    |--------------------------------------------------------------------------
    |
    | Hanya item Merchandise dengan subcategory Baju.
    |
    */

    $bajuItems =
        $order->orderItems
            ->filter(
                function (
                    $detail
                ) {

                    return
                        $detail->item &&
                        $detail->item->transaction_type ===
                            'Merchandise' &&
                        $detail->item->subcategory ===
                            'Baju';

                }
            )
            ->values();


    /*
    |--------------------------------------------------------------------------
    | BUILD SIZE / DIVISION ROWS
    |--------------------------------------------------------------------------
    */

    $bajuRows =
        collect();


    foreach (
        $bajuItems
        as $detail
    ) {

        foreach (
            $detail->sizeBreakdowns
            as $breakdown
        ) {

            $bajuRows->push([
                'item_name' =>
                    $detail->item->name
                    ??
                    'Baju',

                'size' =>
                    $breakdown->size
                    ??
                    '',

                'division' =>
                    $breakdown->division
                    ??
                    '',

                'quantity' =>
                    (int) (
                        $breakdown->quantity
                        ??
                        0
                    ),

                'unit_price' =>
                    (int) (
                        $breakdown->unit_price
                        ??
                        0
                    ),

                'subtotal_price' =>
                    (int) (
                        $breakdown->subtotal_price
                        ??
                        0
                    ),

                'color_number' =>
                    $detail->color_number
                    ??
                    '',

                'design_link' =>
                    $detail->design_link
                    ??
                    '',
            ]);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | GRAND TOTAL
    |--------------------------------------------------------------------------
    */

    $grandTotal =
        (int) $bajuItems->sum(
            function (
                $detail
            ) {

                return (int) (
                    $detail->subtotal_price
                    ??
                    $detail->sizeBreakdowns
                        ->sum(
                            'subtotal_price'
                        )
                    ??
                    0
                );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | DYNAMIC COLOR
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


    /*
    |--------------------------------------------------------------------------
    | SPECIFICATION HELPERS
    |--------------------------------------------------------------------------
    */

    $firstBaju =
        $bajuItems->first();


    $colorNumber =
        $firstBaju
            ? (
                $firstBaju->color_number
                ??
                ''
            )
            : '';


    $designLinks =
        $bajuItems
            ->pluck(
                'design_link'
            )
            ->filter(
                function (
                    $link
                ) {

                    return
                        trim(
                            (string) $link
                        ) !== '';

                }
            )
            ->unique()
            ->values();

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
            class="{{ $dynamicClass($agreementDate) }}"
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
                    class="{{ $dynamicClass($partyTwoName) }}"
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
                    class="{{ $dynamicClass($partyTwoPosition) }}"
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
                    class="{{ $dynamicClass($partyTwoOrganization) }}"
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
                    class="{{ $dynamicClass($partyTwoAddress) }}"
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
                    class="{{ $dynamicClass($partyTwoPhone) }}"
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
            class="{{ $dynamicClass($partyTwoOrganization) }}"
        >
            {{ $partyTwoOrganization ?: '-' }}
        </span>

        disebut sebagai <strong>PIHAK KEDUA.</strong>

    </p>


    <p class="paragraph">

        Berdasarkan hal-hal tersebut di atas,
        <strong>PARA PIHAK</strong> sepakat untuk membuat,
        menandatangani, dan melaksanakan
        <em>Memorandum of Understanding</em>
        (MoU) tentang pembelian baju untuk kegiatan

        <span
            class="{{ $dynamicClass($eventName) }}"
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
        <strong>PIHAK PERTAMA</strong> bersedia menyediakan barang
        berupa baju sesuai dengan kebutuhan
        <strong>PIHAK KEDUA</strong> untuk pelaksanaan kegiatan

        <span
            class="{{ $dynamicClass($eventName) }}"
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


    <table class="items-table">

        <thead>

            <tr>

                <th class="col-no">
                    No
                </th>

                <th class="col-description">
                    Keterangan
                </th>

                <th class="col-division">
                    Divisi
                </th>

                <th class="col-quantity">
                    Jumlah
                </th>

                <th class="col-unit">
                    Satuan
                </th>

                <th class="col-price">
                    Harga
                    <br>
                    Satuan
                </th>

                <th class="col-subtotal">
                    Subtotal
                </th>

            </tr>

        </thead>


        <tbody>

            @if(
                $bajuRows->count() > 0
            )

                @foreach(
                    $bajuRows
                    as $index =>
                    $row
                )

                    <tr>

                        <td class="col-no">

                            {{ $index + 1 }}

                        </td>


                        <td class="col-description">

                            <span
                                class="{{
                                    $dynamicClass(
                                        $row['item_name']
                                    )
                                }}"
                            >
                                {{
                                    $row['item_name']
                                    ?: 'Baju'
                                }}
                                {{
                                    $row['size']
                                }}
                            </span>

                        </td>


                        <td class="col-division">

                            <span
                                class="{{
                                    $dynamicClass(
                                        $row['division']
                                    )
                                }}"
                            >
                                {{
                                    $row['division']
                                    ?: '-'
                                }}
                            </span>

                        </td>


                        <td class="col-quantity">

                            <span class="dynamic filled">
                                {{ $row['quantity'] }}
                            </span>

                        </td>


                        <td class="col-unit">
                            <em>Pcs</em>
                        </td>


                        <td class="col-price">

                            <table class="money-table">

                                <tr>

                                    <td class="money-prefix">
                                        Rp
                                    </td>

                                    <td class="money-value">

                                        {{
                                            $money(
                                                $row['unit_price']
                                            )
                                        }}

                                    </td>

                                </tr>

                            </table>

                        </td>


                        <td class="col-subtotal">

                            <table class="money-table">

                                <tr>

                                    <td class="money-prefix">
                                        Rp
                                    </td>

                                    <td class="money-value">

                                        {{
                                            $money(
                                                $row['subtotal_price']
                                            )
                                        }}

                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>

                @endforeach


                {{-- Empty rows retained only for visual spacing --}}

                @for(
                    $emptyRow = $bajuRows->count();
                    $emptyRow < 5;
                    $emptyRow++
                )

                    <tr>

                        <td
                            class="col-no"
                            style="height:8mm;"
                        >
                        </td>

                        <td class="col-description"></td>

                        <td class="col-division"></td>

                        <td class="col-quantity"></td>

                        <td class="col-unit"></td>

                        <td class="col-price"></td>

                        <td class="col-subtotal"></td>

                    </tr>

                @endfor

            @else

                <tr>

                    <td
                        colspan="7"
                        class="center"
                        style="height:18mm;"
                    >
                        Tidak ada detail Baju yang ditemukan.
                    </td>

                </tr>

            @endif


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
                                    {{ $money($grandTotal) }}
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


    <p class="paragraph">
        Adapun spesifikasi baju tersebut adalah sebagai berikut.
    </p>


    <table class="spec-table">

        <tr>

            <td class="spec-label">
                - Bahan
            </td>

            <td class="spec-colon">
                :
            </td>

            <td class="spec-value">
                Cotton Combed 24s
            </td>

        </tr>


        <tr>

            <td class="spec-label">
                - Tipe Sablon
            </td>

            <td class="spec-colon">
                :
            </td>

            <td class="spec-value">
                DTF
            </td>

        </tr>


        <tr>

            <td class="spec-label">
                - Ukuran Baju
            </td>

            <td class="spec-colon">
                :
            </td>

            <td class="spec-value">
                S - 5XL
            </td>

        </tr>


        <tr>

            <td class="spec-label">
                - Warna
            </td>

            <td class="spec-colon">
                :
            </td>

            <td class="spec-value">

                <span
                    class="{{ $dynamicClass($colorNumber) }}"
                >
                    {{ $colorNumber ?: '-' }}
                </span>

            </td>

        </tr>


        <tr>

            <td class="spec-label">
                - Desain
            </td>

            <td class="spec-colon">
                :
            </td>

            <td class="spec-value">

                @if(
                    $designLinks->count() > 0
                )

                    @foreach(
                        $designLinks
                        as $designIndex =>
                        $designLink
                    )

                        <div
                            class="design-link {{
                                $dynamicClass(
                                    $designLink
                                )
                            }}"
                        >

                            {{ $designLink }}

                        </div>

                    @endforeach

                @else

                    <span class="dynamic empty">
                        (Insert mockup design per divisi + link element/asset)
                    </span>

                @endif

            </td>

        </tr>

    </table>


    <div class="article-title mt-medium">

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
            berhak menerima tautan Google Drive dari
            <strong>PIHAK KEDUA</strong>
            yang memuat kelengkapan spesifikasi berupa file desain
            elemen/logo beresolusi tinggi format PNG, referensi
            penempatan desain pada baju (<em>mock-up</em>), serta
            panduan detail ukuran selambat-lambatnya 21 (dua puluh
            satu) hari kerja sebelum proses produksi dimulai.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menolak pengajuan retur dari
            <strong>PIHAK KEDUA</strong>
            apabila pengajuan dilakukan setelah batas waktu
            <em>quality control</em>, yaitu 3 (tiga) hari kerja sejak
            seluruh barang diterima. Pengajuan retur wajib disertai
            dokumentasi yang menunjukkan kondisi cacat atau rusak
            pada barang serta informasi identitas barang sebagaimana
            dimaksud pada ayat 7.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima kembali dokumen
            <em>Memorandum of Understanding</em> (MoU) yang telah
            ditandatangani oleh <strong>PIHAK KEDUA</strong>
            melalui website Inventory Student Council apabila
            pesanan berada pada status “<em>Waiting for MoU</em>”.
        </li>

    </ol>

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
            <strong>PIHAK PERTAMA</strong>
            berhak menerima bukti transfer atau bukti pembayaran
            dari <strong>PIHAK KEDUA</strong> melalui website
            Inventory Student Council apabila pesanan berada pada
            status “<em>Waiting for Payment</em>”.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima kuitansi yang telah ditandatangani oleh
            <strong>PIHAK KEDUA</strong> melalui website Inventory
            Student Council sebagai bukti sah penyelesaian
            administrasi keuangan apabila pesanan berada pada status
            “<em>Waiting for Kwitansi</em>”.
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berhak menerima tautan (link) Google Drive dari
            <strong>PIHAK KEDUA</strong> melalui website Inventory
            Student Council yang memuat dokumentasi barang yang
            diajukan untuk retur pada status “<em>Handed Over</em>”.
            Dokumentasi wajib disertai foto yang menunjukkan kondisi
            cacat atau rusak pada barang serta diberi nama dengan
            format “<strong>Nama Barang - Ukuran - Divisi</strong>”.
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
            berkewajiban untuk menyerahkan barang dalam kondisi
            baik, layak pakai, berfungsi dengan baik, dan sesuai
            spesifikasi serta jumlah sebagaimana tercantum dalam
            <strong>Pasal I.</strong>
        </li>


        <li>
            <strong>PIHAK PERTAMA</strong>
            berkewajiban menerima komplain dan menyediakan barang
            pengganti sesuai ketersediaan unit dengan spesifikasi
            setara atau lebih baik, apabila ditemukan barang yang
            cacat produksi atau kerusakan saat serah terima dan
            <em>quality control</em>. Setelah proses
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


        <li>
            <strong>PIHAK PERTAMA</strong>
            berkewajiban menerima pengajuan pengembalian barang
            cacat (<em>retur</em>) dari <strong>PIHAK KEDUA</strong>
            selambat-lambatnya 3 (tiga) hari kerja setelah barang
            diterima. Klasifikasi cacat meliputi:

            <div class="mt-small">

                - Baju & Jahitan: Kaos berlubang atau sobek,
                jahitan terlepas atau jebol (<em>open seam</em>),

                <br>

                - Sablon: Kualitas <em>print</em> tidak sesuai,
                sablon terkelupas, <em>crack</em> (retak),
                warna memudar, serta penempatan sablon yang tidak
                presisi (miring, meluber, tidak simetris, atau
                salah posisi).

                <br>

                - Ukuran: Selisih ukuran pakaian yang melampaui
                batas toleransi 2 cm dari panduan ukuran
                (<em>size chart</em>) yang disepakati.

            </div>

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


    <div class="paragraph">

        banyak sisa benang tercabut
        (<em>loose threads</em>), warna kain belang, serta terdapat
        noda produksi (kotoran/tinta).

        <br><br>

        - Sablon: Kualitas <em>print</em> tidak sesuai, sablon
        terkelupas, <em>crack</em> (retak), warna memudar, serta
        penempatan sablon yang tidak presisi (miring, meluber,
        tidak simetris, atau salah posisi).

        <br><br>

        - Ukuran: Selisih ukuran pakaian yang melampaui batas
        toleransi 2 cm dari panduan ukuran (<em>size chart</em>)
        yang disepakati.

    </div>


    <div class="mt-small">

        <strong>
            5.
        </strong>

        &nbsp;

        <strong>PIHAK PERTAMA</strong>
        berkewajiban menyelesaikan barang <em>retur</em> atau barang
        pengganti dalam waktu pengerjaan paling lambat 5 (lima) hari
        kerja sejak barang cacat diserahkan kembali oleh
        <strong>PIHAK KEDUA.</strong>

    </div>

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
            berhak mengambil dan menggunakan barang dalam kondisi
            baik, layak pakai, berfungsi dengan baik, dan sesuai
            spesifikasi serta jumlah sebagaimana tercantum dalam
            <strong>Pasal I.</strong>
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berhak mengajukan komplain dan meminta unit pengganti
            (<em>replacement</em>) dengan spesifikasi setara atau
            lebih baik, disesuaikan dengan ketersediaan dari
            <strong>PIHAK PERTAMA</strong>, apabila ditemukan
            barang yang cacat produksi atau kerusakan pada saat
            serah terima dan <em>quality control</em>. Setelah
            proses <em>quality control</em> selesai,
            <strong>PIHAK PERTAMA</strong> tidak lagi dapat menerima
            komplain.
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
            berhak melakukan pengecekan barang
            (<em>quality control</em>) dan mengajukan pengembalian
            barang cacat (<em>retur</em>) kepada PERTAMA
            selambat-lambatnya 3 (tiga) hari kerja setelah barang
            diterima.

            <br>

            Klasifikasi cacat meliputi:

            <br>

            - Baju & Jahitan: Kaos berlubang atau sobek,
            jahitan terlepas atau jebol (<em>open seam</em>),
            banyak sisa benang tercabut (<em>loose threads</em>),
            warna kain belang, serta terdapat noda produksi
            (kotoran/tinta).

            <br>

            - Sablon: Kualitas <em>print</em> tidak sesuai,
            sablon terkelupas, <em>crack</em> (retak), warna
            memudar, serta penempatan sablon yang tidak presisi
            (miring, meluber, tidak simetris, atau salah posisi).

            <br>

            - Ukuran: Selisih ukuran pakaian yang melampaui batas
            toleransi 2 cm dari panduan ukuran
            (<em>size chart</em>) yang disepakati.

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
            <strong>PIHAK KEDUA</strong>
            berhak menerima penyelesaian barang <em>retur</em> atau
            barang pengganti dari <strong>PIHAK KEDUA</strong> dalam
            waktu pengerjaan paling lambat 5 (lima) hari kerja sejak
            barang cacat diserahkan kembali.
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
            berkewajiban membayar biaya pembelian barang dengan
            skema, nominal dan jadwal yang telah disepakati oleh
            <strong>PARA PIHAK</strong>, sesuai dengan ketentuan
            dalam <strong>Pasal IV.</strong>
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            mengirim tautan Google Drive ke
            <strong>PIHAK PERTAMA</strong>
            yang memuat kelengkapan spesifikasi berupa file desain
            elemen/logo beresolusi tinggi format PNG, referensi
            penempatan desain pada baju (<em>mock-up</em>), serta
            panduan detail ukuran selambat-lambatnya 21 (dua puluh
            satu) hari kerja sebelum proses produksi dimulai.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            wajib mengajukan klaim <em>retur</em> paling lambat
            3 (tiga) hari kerja sejak seluruh barang diterima dan
            menyertakan dokumentasi yang menunjukkan kondisi cacat
            atau rusak pada barang serta identitas barang sebagaimana
            dimaksud pada ayat 7.
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
            yang tertera pada Invoice dan mengunggah bukti transfer
            ke dalam website Inventory Student Council apabila
            pesanan berada pada status “<em>Waiting for Pembayaran</em>”.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            berkewajiban untuk mengunduh, menandatangani, dan
            mengunggah kembali dokumen kuitansi ke dalam website
            Inventory Student Council sebagai bukti sah
            penyelesaian administrasi apabila pesanan berada pada
            status pengunggahan kuitansi.
        </li>


        <li>
            <strong>PIHAK KEDUA</strong>
            wajib mengunggah tautan (link) Google Drive melalui
            website Inventory Student Council yang memuat foto
            kondisi barang yang cacat atau rusak untuk keperluan
            retur pada status “<em>Handed Over</em>”. Setiap foto
            wajib diberi nama dengan format
            “<strong>Nama Barang - Ukuran - Divisi</strong>”.
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
            PASAL IV
        </span>

        <span class="line">
            BIAYA
        </span>

    </div>


    <ol class="legal-list">

        <li>

            Harga total dari barang-barang sebagaimana dicantumkan
            dalam <strong>Pasal I</strong> Perjanjian ini, dibayar
            oleh <strong>PIHAK KEDUA</strong> kepada
            <strong>PIHAK PERTAMA</strong> sebesar

            <span
                class="{{ $dynamicClass($grandTotal) }}"
            >
                Rp {{ $money($grandTotal) }},-
            </span>

            (

            <span
                class="{{ $dynamicClass($grandTotal) }}"
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
            Nominal tersebut sudah dalam nilai bersih (neto).
        </li>


        <li>
            Pembayaran dilakukan secara lunas oleh
            <strong>PIHAK KEDUA</strong> kepada
            <strong>PIHAK PERTAMA</strong> melalui transfer bank,
            setelah surat perjanjian ini ditandatangani dan paling
            lambat pada hari pengambilan barang.
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
            Apabila salah satu <strong>PIHAK</strong> tidak
            melaksanakan hak dan/atau kewajibannya sebagaimana
            diatur dalam Surat Perjanjian Kerja Sama ini, maka
            <strong>PIHAK</strong> lainnya berhak memberikan
            teguran dan meminta pemenuhan kewajiban tersebut dalam
            jangka waktu yang disepakati oleh
            <strong>PARA PIHAK.</strong>
        </li>


        <li>

            Apabila setelah diberikan teguran sebagaimana dimaksud
            pada ayat (1) pihak yang bersangkutan tetap tidak
            memenuhi kewajibannya tanpa alasan yang dapat
            dipertanggungjawabkan, maka
            <strong>PIHAK</strong> lainnya berhak mengenakan sanksi
            sesuai dengan ketentuan dalam Surat Perjanjian Kerja Sama
            ini, termasuk namun tidak terbatas pada:

            <div class="mt-small">

                a. pengakhiran Surat Perjanjian Kerja Sama atas
                kesepakatan atau karena wanprestasi;

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


    <div class="paragraph">

        pembayaran ganti rugi atau kompensasi sesuai dengan ketentuan
        yang berlaku dalam Surat Perjanjian Kerja Sama ini.

    </div>


    <div class="article-title mt-medium">

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
        penyerahan barang, pembayaran biaya pembelian, penyelesaian
        proses retur atau penggantian barang apabila ada, serta
        pemenuhan kewajiban lainnya sesuai dengan ketentuan dalam
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
            terhadap Surat Perjanjian Kerja Sama ini hanya sah apabila
            dibuat secara tertulis dan disetujui oleh
            <strong>PARA PIHAK.</strong>
        </li>


        <li>
            Surat Perjanjian Kerja Sama ini disepakati semata-mata
            untuk menjaga agar tidak terjadi perselisihan antara
            kedua belah pihak tanpa ada maksud lain.
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

        <span
            class="{{ $dynamicClass($agreementDateShort) }}"
        >
            {{ $agreementDateShort ?: '-' }}
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
                    class="{{ $dynamicClass($partyTwoName) }} signature-name"
                >
                    {{ $partyTwoName ?: '-' }}
                </div>


                <div
                    class="{{ $dynamicClass($partyTwoPosition) }} signature-role"
                >
                    {{ $partyTwoPosition ?: '-' }}
                </div>

            </td>

        </tr>

    </table>

</div>


</body>

</html>