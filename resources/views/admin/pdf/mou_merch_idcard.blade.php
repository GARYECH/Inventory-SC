<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        MoU Merchandise ID Card -
        {{ $order->order_number }}
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        .kop-table td {
            vertical-align: middle;
        }

        .kop-text {
            text-align: right;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 12pt;
        }

        .kop-text p {
            margin: 0;
            font-size: 9pt;
        }

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h3 {
            margin: 0;
            font-size: 13pt;
            text-decoration: underline;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 10pt;
        }

        .items-table th {
            background: #f3f4f6;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
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

    $logoPath =
        $logoDb
            ? storage_path(
                'app/public/' . $logoDb
            )
            : null;

    $idCardItems =
        $order->orderItems->filter(
            function ($detail) {
                return
                    $detail->item &&
                    $detail->item->transaction_type ===
                        'Merchandise' &&
                    $detail->item->subcategory ===
                        'ID Card';
            }
        );

    $grandTotal =
        $idCardItems->sum(
            'subtotal_price'
        );

@endphp

<table class="kop-table">

    <tr>

        <td width="25%">

            @if(
                $logoPath &&
                file_exists($logoPath)
            )

                <img
                    src="{{ $logoPath }}"
                    style="max-height:65px;"
                >

            @endif

        </td>

        <td
            width="75%"
            class="kop-text"
        >

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
                ID CARD STUDENT COUNCIL 2026/2027
            </p>

        </td>

    </tr>

</table>

<div class="title">

    <h3>
        SURAT PERJANJIAN KERJA SAMA
    </h3>

    <p>
        MERCHANDISE ID CARD
    </p>

    <p>
        Nomor:
        {{
            $order->mou_number
            ??
            $order->order_number
        }}
    </p>

</div>

<p>
    Nomor Order:
    <strong>
        {{ $order->order_number }}
    </strong>
</p>

<p>
    Nama:
    <strong>
        {{ $order->user->name }}
    </strong>
</p>

<p>
    Organisasi:
    <strong>
        {{ $order->organization }}
    </strong>
</p>

<table class="items-table">

    <thead>

        <tr>

            <th>No.</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>

        </tr>

    </thead>

    <tbody>

        @foreach(
            $idCardItems
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
            @endphp

            <tr>

                <td class="center">
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $detail->item->name }}
                </td>

                <td class="center">
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

        <tr>

            <td
                colspan="4"
                class="right"
            >
                <strong>
                    Total
                </strong>
            </td>

            <td class="right">
                <strong>
                    Rp
                    {{ number_format(
                        $grandTotal,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </td>

        </tr>

    </tbody>

</table>

<p style="margin-top:20px;">
    Detail desain ID Card:
</p>

@foreach(
    $idCardItems
    as $detail
)

    <p>
        {{ $detail->item->name }}
        -
        <a href="{{ $detail->design_link }}">
            {{ $detail->design_link }}
        </a>
    </p>

@endforeach

</body>
</html>