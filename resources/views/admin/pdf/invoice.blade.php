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
    | TOTAL
    |--------------------------------------------------------------------------
    */

    $total =
        (int) $order->total_price;


    /*
    |--------------------------------------------------------------------------
    | RENTAL DAYS
    |--------------------------------------------------------------------------
    |
    | Rental day menggunakan GAP tanggal.
    |
    | Minggu → Senin  = 0 hari
    | Minggu → Selasa = 1 hari
    | Minggu → Rabu   = 2 hari
    |
    */

    $rentalDays =
        null;


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
                0,
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

            Telepon: (031)7451699

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
                        Number
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
                        Invoice Num.
                    </td>

                    <td width="5%">
                        :
                    </td>

                    <td width="60%">
                        {{
                            $order->invoice_number
                            ??
                            $order->order_number
                        }}
                    </td>

                </tr>


                <tr>

                    <td>
                        Date
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
                            $order->start_date
                                ? \Carbon\Carbon::parse(
                                    $order->start_date
                                )
                                ->subDay()
                                ->format('d/m/Y')
                                : '-'
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


        @foreach(
            $order->orderItems
            as $index => $detail
        )

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


            <!-- ===================================================== -->
            <!-- ITEM ROW -->
            <!-- ===================================================== -->

            <tr>


                <!-- ================================================= -->
                <!-- NO -->
                <!-- ================================================= -->

                <td class="center">

                    {{ $index + 1 }}

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

                            {{ $rentalDays ?? 0 }}
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

                                {{ $rentalDays ?? 0 }}
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
                                            $displayUnitPrice,
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
                                    $displayUnitPrice,
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
                                        $subtotal,
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

        @endforeach



        <!-- ========================================================= -->
        <!-- SUBTOTAL -->
        <!-- ========================================================= -->

        <tr>

            <td
                colspan="4"
                class="right"
            >

                Subtotal

            </td>


            <td>

                Rp
                {{
                    number_format(
                        $total,
                        0,
                        ',',
                        '.'
                    )
                }},-

            </td>

        </tr>



        <!-- ========================================================= -->
        <!-- TAX -->
        <!-- ========================================================= -->

        <tr>

            <td
                colspan="4"
                class="right"
            >

                Tax

            </td>


            <td>

                Rp 0,-

            </td>

        </tr>



        <!-- ========================================================= -->
        <!-- TOTAL -->
        <!-- ========================================================= -->

        <tr>

            <td
                colspan="4"
                class="bg-red"
                style="
                    font-weight:bold;
                    text-align:right;
                "
            >

                Total

            </td>


            <td
                class="bg-red"
                style="font-weight:bold;"
            >

                Rp
                {{
                    number_format(
                        $total,
                        0,
                        ',',
                        '.'
                    )
                }},-

            </td>

        </tr>

    </tbody>

</table>



<!-- =============================================================== -->
<!-- PAYMENT + SIGNATURE -->
<!-- =============================================================== -->

<table style="margin-top:35px;">

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