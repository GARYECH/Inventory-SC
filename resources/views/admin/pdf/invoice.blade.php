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

    $total =
        (int) $order->total_price;

@endphp

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
                $quantity =
                    (int) $detail->quantity;

                $subtotal =
                    (int) $detail->subtotal_price;

                $unitPrice =
                    $quantity > 0
                        ? intdiv(
                            $subtotal,
                            $quantity
                        )
                        : 0;

                $isFree =
                    $subtotal === 0 &&
                    $order->organization ===
                        'Student Council' &&
                    $detail->item &&
                    $detail->item->transaction_type ===
                        'Habis Pakai';
            @endphp

            <tr>

                <td class="center">
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $detail->item->name }}

                    @if(
                        $detail->item->transaction_detail
                    )
                        <br>

                        <span
                            style="
                                font-size:10px;
                                color:#666;
                            "
                        >
                            {{
                                $detail->item
                                    ->transaction_detail
                            }}
                        </span>
                    @endif

                    @if(
                        $detail->item->subcategory
                    )
                        <br>

                        <span
                            style="
                                font-size:10px;
                                color:#666;
                            "
                        >
                            {{
                                $detail->item
                                    ->subcategory
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

                </td>

                <td class="center">
                    {{ $quantity }}
                </td>

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
                                            $unitPrice,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }},-
                                </td>

                            </tr>

                        </table>

                    @endif

                </td>

                <td>

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

        <tr>

            <td
                colspan="4"
                class="bg-red"
                style="font-weight:bold;text-align:right;"
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

<table style="margin-top:35px;">

    <tr>

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