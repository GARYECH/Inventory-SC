<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        MoU Merchandise Baju -
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
            font-size: 9pt;
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


    $bajuItems =
        $order->orderItems
            ->filter(
                function ($detail) {

                    return
                        $detail->item &&
                        $detail->item->transaction_type ===
                            'Merchandise' &&
                        $detail->item->subcategory ===
                            'Baju';

                }
            );


    $grandTotal = 0;

    foreach ($bajuItems as $detail) {

        $grandTotal +=
            $detail->sizeBreakdowns
                ->sum('subtotal_price');

    }

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
                MERCHANDISE STUDENT COUNCIL 2026/2027
            </p>

        </td>

    </tr>

</table>


<div class="title">

    <h3>
        SURAT PERJANJIAN KERJA SAMA
    </h3>

    <p>
        MERCHANDISE BAJU
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


@if($bajuItems->count() > 0)

    @foreach($bajuItems as $detail)

        <p style="margin-top: 18px;">

            <strong>
                {{ $detail->item->name }}
            </strong>

            @if($detail->color_number)

                — Warna:
                <strong>
                    {{ $detail->color_number }}
                </strong>

            @endif

        </p>


        <table class="items-table">

            <thead>

                <tr>

                    <th width="6%">
                        No.
                    </th>

                    <th width="13%">
                        Ukuran
                    </th>

                    <th>
                        Divisi
                    </th>

                    <th width="12%">
                        Jumlah
                    </th>

                    <th width="15%">
                        Satuan
                    </th>

                    <th width="18%">
                        Harga Satuan
                    </th>

                    <th width="18%">
                        Subtotal
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach(
                    $detail->sizeBreakdowns
                    as $index => $breakdown
                )

                    <tr>

                        <td class="center">
                            {{ $index + 1 }}
                        </td>

                        <td class="center">
                            {{ $breakdown->size }}
                        </td>

                        <td>
                            {{ $breakdown->division }}
                        </td>

                        <td class="center">
                            {{ $breakdown->quantity }}
                        </td>

                        <td class="center">
                            Pcs
                        </td>

                        <td class="right">

                            Rp
                            {{
                                number_format(
                                    $breakdown->unit_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}

                        </td>

                        <td class="right">

                            Rp
                            {{
                                number_format(
                                    $breakdown->subtotal_price,
                                    0,
                                    ',',
                                    '.'
                                )
                            }}

                        </td>

                    </tr>

                @endforeach


                <tr>

                    <td
                        colspan="6"
                        class="right"
                    >

                        <strong>
                            Total
                        </strong>

                    </td>


                    <td class="right">

                        <strong>

                            Rp
                            {{
                                number_format(
                                    $detail->sizeBreakdowns
                                        ->sum('subtotal_price'),
                                    0,
                                    ',',
                                    '.'
                                )
                            }}

                        </strong>

                    </td>

                </tr>

            </tbody>

        </table>


        <p style="margin-top: 12px;">

            Detail desain:

            <a href="{{ $detail->design_link }}">
                {{ $detail->design_link }}
            </a>

        </p>

    @endforeach

@endif


<p style="margin-top: 20px;">

    <strong>
        Grand Total:
    </strong>

    Rp
    {{
        number_format(
            $grandTotal,
            0,
            ',',
            '.'
        )
    }}

</p>


</body>

</html>