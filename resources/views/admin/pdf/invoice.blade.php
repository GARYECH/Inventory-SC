<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        INVOICE -
        {{
            $order->invoice_number
            ??
            $order->order_number
        }}
    </title>

    <style>

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .bg-red {
            background-color: #a00000;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table {
            margin-bottom: 20px;
            border: 1px solid #000;
            page-break-inside: auto;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table tr {
            page-break-inside: avoid;
        }

        .amount-words {
            vertical-align: top;
            line-height: 1.5;
            padding: 8px !important;
        }

        .amount-words .words-value {
            color: #a00000;
        }

        .small-muted {
            margin-top: 3px;
            color: #555;
            font-size: 9px;
            line-height: 1.3;
        }

        .reference-link {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-all;
        }

        .tax-note {
            margin: 5px 0 0;
            font-size: 12px;
            line-height: 1.35;
        }

        .payment-signature {
            page-break-inside: avoid;
        }

        .items-table th {
            background-color: #a00000;
            color: #fff;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 8px 5px;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .no-border-table td {
            border: none !important;
            padding: 0 !important;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

    </style>

</head>


<body>


@php

    /*
    |--------------------------------------------------------------------------
    | LOGO + TTD
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


    /*
    |--------------------------------------------------------------------------
    | ITEM SUBTOTAL
    |--------------------------------------------------------------------------
    |
    | Subtotal hanya berasal dari seluruh
    | order item.
    |
    | HT Fee dan Transport dihitung terpisah.
    |
    */

    $itemSubtotal =
        (int) $order->orderItems->sum(
            function ($detail) {

                return (int) (
                    $detail->subtotal_price
                    ?? 0
                );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | HT FEE
    |--------------------------------------------------------------------------
    */

    $htFee =
        (int) (
            $order->ht_fee
            ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | TRANSPORT FEE
    |--------------------------------------------------------------------------
    */

    $transportFee =
        (int) (
            $order->transport_fee
            ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | GRAND TOTAL
    |--------------------------------------------------------------------------
    |
    | Menggunakan accessor grand_total_price
    | dari Order.
    |
    | item subtotal
    | + ht fee
    | + transport fee
    |
    */

    $grossTotal = (int) $order->grand_total_price;
    $hasWithholdingTax = (int) $order->tax_amount > 0;

    $total = $hasWithholdingTax
        ? (int) $order->total_price
        : $grossTotal;

    $grossUpDisplay = function ($value) use ($hasWithholdingTax) {
        $amount = (int) $value;

        if (!$hasWithholdingTax || $amount <= 0) {
            return $amount;
        }

        return (int) round(
            $amount / (1 - \App\Models\Order::TAX_RATE)
        );
    };


    /*
    |--------------------------------------------------------------------------
    | JUMLAH TERBILANG
    |--------------------------------------------------------------------------
    */
    $terbilang = function ($number) use (&$terbilang) {
        $number = (int) $number;

        $words = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
            'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas',
        ];

        if ($number === 0) {
            return 'Nol';
        }

        if ($number < 0) {
            return 'Minus ' . $terbilang(abs($number));
        }

        if ($number < 12) {
            return $words[$number];
        }

        if ($number < 20) {
            return $words[$number - 10] . ' Belas';
        }

        if ($number < 100) {
            $result = $words[(int) floor($number / 10)] . ' Puluh';
            $remainder = $number % 10;

            return $remainder > 0
                ? $result . ' ' . $terbilang($remainder)
                : $result;
        }

        if ($number < 200) {
            $remainder = $number - 100;

            return 'Seratus' . (
                $remainder > 0 ? ' ' . $terbilang($remainder) : ''
            );
        }

        if ($number < 1000) {
            $result = $terbilang((int) floor($number / 100)) . ' Ratus';
            $remainder = $number % 100;

            return $remainder > 0
                ? $result . ' ' . $terbilang($remainder)
                : $result;
        }

        if ($number < 2000) {
            $remainder = $number - 1000;

            return 'Seribu' . (
                $remainder > 0 ? ' ' . $terbilang($remainder) : ''
            );
        }

        if ($number < 1000000) {
            $result = $terbilang((int) floor($number / 1000)) . ' Ribu';
            $remainder = $number % 1000;

            return $remainder > 0
                ? $result . ' ' . $terbilang($remainder)
                : $result;
        }

        if ($number < 1000000000) {
            $result = $terbilang((int) floor($number / 1000000)) . ' Juta';
            $remainder = $number % 1000000;

            return $remainder > 0
                ? $result . ' ' . $terbilang($remainder)
                : $result;
        }

        if ($number < 1000000000000) {
            $result = $terbilang((int) floor($number / 1000000000)) . ' Miliar';
            $remainder = $number % 1000000000;

            return $remainder > 0
                ? $result . ' ' . $terbilang($remainder)
                : $result;
        }

        return (string) $number;
    };

    /*
    |--------------------------------------------------------------------------
    | RENTAL DAYS
    |--------------------------------------------------------------------------
    |
    | Rental day menggunakan GAP tanggal.
    |
    | Tanggal pengambilan dan pengembalian
    | tidak dihitung sebagai hari sewa.
    |
    | Minimum biaya rental = 1 hari.
    |
    */

    $rentalDays =
        1;


    if (
        $order->start_date &&
        $order->end_date &&
        in_array(
            $order->order_type,
            [
                'Peralatan',
                'Handy Talkie',
            ],
            true
        )
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
                1,
                $dateDifference - 1
            );
    }


@endphp



<!-- =============================================================== -->
<!-- HEADER -->
<!-- =============================================================== -->

<table style="margin-bottom:15px;">

    <tr>

        <td
            width="30%"
            style="vertical-align:middle;"
        >

            @if(
                $logoPath &&
                file_exists($logoPath)
            )

                <img
                    src="{{ $logoPath }}"
                    alt="Logo SC"
                    style="max-height:70px;"
                >

            @endif

        </td>


        <td
            width="70%"
            style="
                text-align:right;
                line-height:1.2;
            "
        >

            <strong>
                UNIVERSITAS CIPUTRA SURABAYA
            </strong>

            <br>

            <strong>
                STUDENT COUNCIL
            </strong>

            <br>

            Citraland CBD Boulevard,
            Surabaya, 60219

            <br>

            Jawa Timur – Indonesia

            <br>

            Telepon: (031)7451699; Fax: (031)7451698

            <br>

            Email:
            studentcouncil@ciputra.ac.id

        </td>

    </tr>

</table>



<hr
    style="
        border:0;
        border-top:1px solid #000;
        margin:0 0 15px 0;
    "
>



<!-- =============================================================== -->
<!-- TITLE -->
<!-- =============================================================== -->

<div
    style="
        text-align:center;
        font-size:18px;
        font-weight:bold;
        margin-bottom:25px;
    "
>
    INVOICE
</div>



<!-- =============================================================== -->
<!-- BILL TO + INVOICE INFO -->
<!-- =============================================================== -->

<table style="margin-bottom:20px;">

    <tr>

        <td
            width="48%"
            style="vertical-align:top;"
        >

            <div
                class="bg-red"
                style="
                    padding:5px 10px;
                    font-weight:bold;
                    margin-bottom:5px;
                "
            >
                Bill To
            </div>


            <table cellpadding="3">

                <tr>

                    <td width="25%">
                        Name
                    </td>

                    <td width="5%">
                        :
                    </td>

                    <td width="70%">
                        {{ $order->user->name }}
                    </td>

                </tr>


                <tr>

                    <td>
                        Address
                    </td>

                    <td>
                        :
                    </td>

                    <td>
                        {{ $order->address ?? '-' }}
                    </td>

                </tr>


                <tr>

                    <td>
                        Phone No.
                    </td>

                    <td>
                        :
                    </td>

                    <td>
                        {{ $order->phone_number }}
                    </td>

                </tr>

            </table>

        </td>


        <td width="4%"></td>


        <td
            width="48%"
            style="
                vertical-align:top;
                padding-top:25px;
            "
        >

            <table cellpadding="3">

                <tr>

                    <td width="35%">
                        Invoice No.
                    </td>

                    <td width="5%">
                        :
                    </td>

                    <td
                        width="60%"
                        style="color:#000000;"
                    >
                        {{
                            $order->invoice_number
                            ??
                            $order->order_number
                        }}
                    </td>

                </tr>


                <tr>

                    <td>
                        Created
                    </td>

                    <td>
                        :
                    </td>

                    <td>
                        {{
                            $order->created_at
                                ->format('d/m/Y')
                        }}
                    </td>

                </tr>


                <tr>

                    <td>
                        Due Date
                    </td>

                    <td>
                        :
                    </td>

                    <td>
                        {{
                            $order->invoice_due_date
                                ? \Carbon\Carbon::parse(
                                    $order->invoice_due_date
                                )->format('d/m/Y')
                                : (
                                    $order->start_date
                                        ? \Carbon\Carbon::parse(
                                            $order->start_date
                                        )->subDay()->format('d/m/Y')
                                        : '-'
                                )
                        }}
                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>



<!-- =============================================================== -->
<!-- ITEMS TABLE -->
<!-- =============================================================== -->

<table class="items-table">

    <thead>

        <tr>

            <th width="5%">
                No
            </th>

            <th width="40%">
                Description
            </th>

            <th width="7%">
                QTY
            </th>

            <th width="24%">
                Unit Price
            </th>

            <th width="24%">
                Sub Total
            </th>

        </tr>

    </thead>


    <tbody>


    @php
        $invoiceRowNumber = 1;
        foreach ($order->orderItems as $index => $detail) {
    @endphp

            @php

                /*
                |--------------------------------------------------------------------------
                | BASIC DATA
                |--------------------------------------------------------------------------
                */

                $quantity =
                    (int) $detail->quantity;

                $subtotal =
                    (int) $detail->subtotal_price;

                $item =
                    $detail->item;

                $transactionType =
                    $item
                        ? $item->transaction_type
                        : null;

                $transactionDetail =
                    $item
                        ? $item->transaction_detail
                        : null;

                $subcategory =
                    $item
                        ? $item->subcategory
                        : null;


                /*
                |--------------------------------------------------------------------------
                | RENTAL ITEM
                |--------------------------------------------------------------------------
                */

                $isRental =
                    in_array(
                        $transactionType,
                        [
                            'Peralatan',
                            'Handy Talkie',
                        ],
                        true
                    );


                /*
                |--------------------------------------------------------------------------
                | STUDENT COUNCIL FREE
                |--------------------------------------------------------------------------
                */

                $isFree =
                    false;


                if (
                    $subtotal === 0 &&
                    $order->organization ===
                        'Student Council'
                ) {

                    if (
                        $transactionType ===
                        'Habis Pakai'
                    ) {

                        $isFree =
                            true;

                    } elseif (
                        $transactionType ===
                        'Peralatan'
                    ) {

                        $isFree =
                            true;

                    } elseif (
                        $transactionType ===
                        'Handy Talkie'
                        &&
                        $transactionDetail ===
                        'HT UV-5R'
                    ) {

                        $isFree =
                            true;

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | NORMAL UNIT PRICE
                |--------------------------------------------------------------------------
                */

                $baseUnitPrice =
                    $item
                        ? (int) $item->price
                        : 0;


                /*
                |--------------------------------------------------------------------------
                | Baju SIZE EXTRA
                |--------------------------------------------------------------------------
                */

                $sizeAdditionalPrice =
                    (int) (
                        $detail->size_additional_price
                        ?? 0
                    );


                /*
                |--------------------------------------------------------------------------
                | DISPLAY UNIT PRICE
                |--------------------------------------------------------------------------
                |
                | Rental:
                |
                | harga per hari
                |
                | Non-rental:
                |
                | harga per pcs
                |
                */

                if ($isRental) {

                    $displayUnitPrice =
                        $baseUnitPrice;

                } else {

                    $displayUnitPrice =
                        $baseUnitPrice
                        +
                        $sizeAdditionalPrice;
                }


            @endphp


            @if(
                $subcategory === 'Baju'
                && $detail->sizeBreakdowns
                && $detail->sizeBreakdowns->isNotEmpty()
            )

                @php
                    $sizePriceGroups = $detail->sizeBreakdowns->groupBy(
                        function ($breakdown) use ($baseUnitPrice) {
                            $additionalPrice = (int) (
                                $breakdown->size_additional_price ?? 0
                            );

                            return (int) (
                                $breakdown->unit_price
                                ?? ($baseUnitPrice + $additionalPrice)
                            );
                        }
                    );
                @endphp

                @foreach($sizePriceGroups as $priceKey => $priceGroup)

                    @php
                        $sizeUnitPrice = (int) $priceKey;

                        $sizeQuantity = (int) $priceGroup->sum(
                            'quantity'
                        );

                        $sizeSubtotal = (int) $priceGroup->sum(
                            function ($breakdown) use ($sizeUnitPrice) {
                                return (int) (
                                    $breakdown->subtotal_price
                                    ?? (
                                        (int) ($breakdown->quantity ?? 0)
                                        * $sizeUnitPrice
                                    )
                                );
                            }
                        );

                        $sizeNames = $priceGroup
                            ->pluck('size')
                            ->map(function ($size) {
                                return strtoupper(trim((string) $size));
                            })
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        $sizeOrder = [
                            'XS', 'S', 'M', 'L', 'XL',
                            '2XL', '3XL', '4XL', '5XL'
                        ];

                        usort($sizeNames, function ($a, $b) use ($sizeOrder) {
                            $aIndex = array_search($a, $sizeOrder, true);
                            $bIndex = array_search($b, $sizeOrder, true);

                            return ($aIndex === false ? 999 : $aIndex)
                                <=> ($bIndex === false ? 999 : $bIndex);
                        });

                        $sizeLabels = implode(', ', $sizeNames);

                        if (count($sizeNames) > 1) {
                            $firstSize = array_search($sizeNames[0], $sizeOrder, true);
                            $lastSize = array_search(
                                $sizeNames[count($sizeNames) - 1],
                                $sizeOrder,
                                true
                            );

                            if (
                                $firstSize !== false &&
                                $lastSize !== false &&
                                $lastSize - $firstSize + 1 === count($sizeNames)
                            ) {
                                $sizeLabels = $sizeNames[0] . '–'
                                    . $sizeNames[count($sizeNames) - 1];
                            }
                        }
                    @endphp

                    <tr>
                        <td class="center">
                            {{ $invoiceRowNumber++ }}
                        </td>

                        <td>
                            <strong>{{ $item->name ?? 'Baju' }}</strong>

                            <br>

                            <span style="font-size:10px;color:#555;">
                                Ukuran: {{ $sizeLabels }}
                            </span>


                        </td>

                        <td class="center">
                            {{ $sizeQuantity }}
                        </td>

                        <td>
                            <table class="no-border-table">
                                <tr>
                                    <td width="20%">Rp</td>

                                    <td width="80%" style="text-align:right;">
                                        {{ number_format(
                                            $grossUpDisplay($sizeUnitPrice),
                                            0,
                                            ',',
                                            '.'
                                        ) }},-
                                    </td>
                                </tr>
                            </table>


                        </td>

                        <td>
                            <table class="no-border-table">
                                <tr>
                                    <td width="20%">Rp</td>

                                    <td width="80%" style="text-align:right;">
                                        {{ number_format(
                                            $grossUpDisplay($sizeSubtotal),
                                            0,
                                            ',',
                                            '.'
                                        ) }},-
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                @endforeach

            @else

            <!-- ===================================================== -->
            <!-- ITEM ROW -->
            <!-- ===================================================== -->

            <tr>


                <!-- ================================================= -->
                <!-- NO -->
                <!-- ================================================= -->

                <td class="center">

                    {{ $invoiceRowNumber++ }}

                </td>



                <!-- ================================================= -->
                <!-- DESCRIPTION -->
                <!-- ================================================= -->

                <td>

                    <strong>
                        {{ $item->name ?? '-' }}
                    </strong>


                    @if($transactionDetail)

                        <br>

                        <span
                            style="
                                font-size:10px;
                                color:#666;
                            "
                        >

                            {{
                                $transactionDetail
                            }}

                        </span>

                    @endif


                    @if($subcategory)

                        <br>

                        <span
                            style="
                                font-size:10px;
                                color:#666;
                            "
                        >

                            {{
                                $subcategory
                            }}

                        </span>

                    @endif


                    @if($detail->size)

                        <br>

                        <span
                            style="
                                font-size:10px;
                                color:#666;
                            "
                        >

                            Size:
                            {{ $detail->size }}

                        </span>

                    @endif






                    <!-- ================================================= -->
                    <!-- RENTAL DETAIL -->
                    <!-- ================================================= -->

                    @if($isRental)

                        <div
                            style="
                                margin-top:5px;
                                padding-top:4px;
                                border-top:1px dotted #999;
                                font-size:10px;
                                color:#555;
                            "
                        >

                            <strong>
                                Rental:
                            </strong>

                            {{
                                $order->start_date
                                    ? \Carbon\Carbon::parse(
                                        $order->start_date
                                    )->format('d/m/Y')
                                    : '-'
                            }}

                            →

                            {{
                                $order->end_date
                                    ? \Carbon\Carbon::parse(
                                        $order->end_date
                                    )->format('d/m/Y')
                                    : '-'
                            }}

                            <br>

                            <strong>
                                Durasi:
                            </strong>

                           {{ max(1, (int) ($rentalDays ?? 1)) }}
                            {{ ($rentalDays ?? 0) === 1 ? 'hari' : 'hari' }}

                        </div>

                    @endif

                </td>



                <!-- ================================================= -->
                <!-- QTY -->
                <!-- ================================================= -->

                <td class="center">

                    {{ $quantity }}

                </td>



                <!-- ================================================= -->
                <!-- UNIT PRICE -->
                <!-- ================================================= -->

                <td>


                    @if($isFree)

                        <div
                            style="
                                text-align:center;
                                color:#a00000;
                                font-weight:bold;
                                font-size:11px;
                            "
                        >

                            FREE (SC)

                        </div>


                        @if($isRental)

                            <div
                                style="
                                    margin-top:4px;
                                    text-align:center;
                                    font-size:9px;
                                    color:#666;
                                "
                            >

                                {{ max(1, (int) ($rentalDays ?? 1)) }}
                                {{ ($rentalDays ?? 0) === 1 ? 'hari' : 'hari' }}
                                rental

                            </div>

                        @endif


                    @else


                        <table class="no-border-table">

                            <tr>

                                <td width="20%">
                                    Rp
                                </td>

                                <td
                                    width="80%"
                                    style="text-align:right;"
                                >

                                    {{
                                        number_format(
                                            $grossUpDisplay($displayUnitPrice),
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }},-

                                </td>

                            </tr>

                        </table>


                        @if($isRental)

                            <div
                                style="
                                    margin-top:3px;
                                    text-align:right;
                                    font-size:9px;
                                    color:#666;
                                    font-style:italic;
                                "
                            >

                                / hari

                            </div>

                        @endif


                    @endif

                </td>



                <!-- ================================================= -->
                <!-- SUBTOTAL -->
                <!-- ================================================= -->

                <td>


                    @if($isRental && !$isFree)

                        <div
                            style="
                                font-size:9px;
                                color:#666;
                                margin-bottom:3px;
                                text-align:right;
                            "
                        >

                            {{
                                number_format(
                                    $grossUpDisplay($displayUnitPrice),
                                    0,
                                    ',',
                                    '.'
                                )
                            }}

                            ×

                            {{ max(1, (int) ($rentalDays ?? 1)) }}

                            ×

                            {{ $quantity }}

                        </div>

                    @endif


                    <table class="no-border-table">

                        <tr>

                            <td width="20%">
                                Rp
                            </td>

                            <td
                                width="80%"
                                style="text-align:right;"
                            >

                                {{
                                    number_format(
                                        $grossUpDisplay($subtotal),
                                        0,
                                        ',',
                                        '.'
                                    )
                                }},-

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

            @endif

        @php
        }
        @endphp



        @php
            $summaryRowCount = 2
                + ($htFee > 0 ? 1 : 0)
                + ($order->order_type === 'Handy Talkie' ? 1 : 0)
                + ((int) $order->tax_amount > 0 ? 1 : 0);
        @endphp

        <tr>
            <td colspan="3" rowspan="{{ $summaryRowCount }}" class="amount-words">
                <strong>Jumlah Terbilang:</strong>
                <br>
                <span class="words-value" style="color: #000000 !important;">
                    {{ trim($terbilang($total)) }} Rupiah
                </span>
            </td>

            <td class="right">Subtotal</td>

            <td>
                Rp
                {{ number_format($grossUpDisplay($itemSubtotal), 0, ',', '.') }},-
            </td>
        </tr>

        @if($htFee > 0)
            <tr>
                <td class="right">HT Fee</td>
                <td>
                    Rp
                    {{ number_format($grossUpDisplay($htFee), 0, ',', '.') }},-
                </td>
            </tr>
        @endif

        @if($order->order_type === 'Handy Talkie')
            <tr>
                <td class="right">Transport HT</td>
                <td>
                    @if($transportFee > 0)
                        Rp
                        {{ number_format($grossUpDisplay($transportFee), 0, ',', '.') }},-
                    @else
                        GRATIS
                    @endif
                </td>
            </tr>
        @endif

        @if((int) $order->tax_amount > 0)
            <tr>
                <td class="right">
                    <strong>Tax</strong>
                    <br>
                    PPh 23 ({{ (int) (100 * \App\Models\Order::TAX_RATE) }}%) - Gross Up
                </td>

                <td>
                    <table class="no-border-table">
                        <tr>
                            <td width="20%">(Rp</td>
                            <td width="80%" style="text-align:right;">
                                {{ number_format($order->tax_amount, 0, ',', '.') }},-)
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif

        <tr>
            <td class="bg-red" style="font-weight:bold;text-align:right;">
                Total
            </td>

            <td class="bg-red" style="font-weight:bold;">
                Rp
                {{ number_format($total, 0, ',', '.') }},-
            </td>
        </tr>

    </tbody>

</table>

<p class="tax-note">
    <strong>Catatan:</strong>
    Pajak yang timbul atas transaksi ini menjadi tanggungan pihak
    penyewa/pembeli sesuai kesepakatan para pihak.
</p>



<!-- =============================================================== -->
<!-- PAYMENT + SIGNATURE -->
<!-- =============================================================== -->

<table class="payment-signature" style="margin-top:35px;">

    <tr>


        <!-- ========================================================= -->
        <!-- PAYMENT -->
        <!-- ========================================================= -->

        <td
            width="50%"
            style="vertical-align:top;"
        >

            <div
                class="bg-red"
                style="
                    padding:5px 10px;
                    font-weight:bold;
                    width:80%;
                "
            >

                Payment Method

            </div>


            <p>
                Bank Central Asia (BCA)
            </p>


            <p>

                No Rekening:
                8620797163

            </p>


            <p>

                a/n Chalistha Dea Yuwanda

            </p>

        </td>



        <!-- ========================================================= -->
        <!-- SIGNATURE -->
        <!-- ========================================================= -->

        <td
            width="50%"
            style="
                vertical-align:top;
                text-align:center;
            "
        >

            <p>
                Diketahui,
            </p>


            @if(
                $ttdPath &&
                file_exists($ttdPath)
            )

                <img
                    src="{{ $ttdPath }}"
                    alt="TTD Bendahara"
                    style="
                        max-height:70px;
                        margin:10px 0 5px;
                    "
                >

            @else

                <br><br><br><br>

            @endif


            <p
                style="
                    font-weight:bold;
                    text-decoration:underline;
                "
            >
                Gregory Edgard Christian
            </p>


            <p>
                Bendahara Student Council
            </p>

        </td>

    </tr>

</table>


</body>

</html>