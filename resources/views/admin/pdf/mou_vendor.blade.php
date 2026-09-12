<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        MoU Vendor - {{ $order->order_number }}
    </title>

    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .signature-box {
            width: 100%;
            margin-top: 50px;
        }

        .signature-col {
            width: 50%;
            float: left;
            text-align: center;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

@php
    $vendorItems =
        $order->orderItems->filter(
            function ($detail) {
                return
                    $detail->item &&
                    $detail->item->transaction_type ===
                        'Peralatan' &&
                    $detail->item->transaction_detail ===
                        'Vendor Rental';
            }
        );

    $vendorTotal =
        $vendorItems->sum(
            'subtotal_price'
        );
@endphp

<div class="header">

    <div class="title">
        STUDENT COUNCIL UNIVERSITAS CIPUTRA
    </div>

    <div>
        Surat Perjanjian Penyewaan Alat
        (Vendor Eksternal)
    </div>

    <div>
        No. Ref:
        {{ $order->order_number }}
    </div>

</div>

<p>
    Pada hari ini telah disetujui penyewaan alat oleh:
</p>

<ul>
    <li>
        <strong>Nama Pemesan:</strong>
        {{ $order->user->name }}
    </li>

    <li>
        <strong>Proker / Event:</strong>
        {{ $order->proker_name }}
    </li>

    <li>
        <strong>Organisasi:</strong>
        {{ $order->organization }}
    </li>

    <li>
        <strong>Periode Sewa:</strong>
        {{
            $order->start_date
            ? $order->start_date->format('d M Y')
            : '-'
        }}
        s/d
        {{
            $order->end_date
            ? $order->end_date->format('d M Y')
            : '-'
        }}
    </li>
</ul>

<p>
    Dengan rincian barang vendor sebagai berikut:
</p>

<table>

    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>

    <tbody>

        @foreach(
            $vendorItems
            as $index => $detail
        )

            <tr>

                <td class="center">
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $detail->item->name }}
                </td>

                <td class="center">
                    {{ $detail->quantity }}
                </td>

                <td class="right">
                    Rp
                    {{ number_format(
                        $detail->subtotal_price,
                        0,
                        ',',
                        '.'
                    ) }}
                </td>

            </tr>

        @endforeach

    </tbody>

    <tfoot>

        <tr>

            <th
                colspan="3"
                class="right"
            >
                GRAND TOTAL:
            </th>

            <th class="right">
                Rp
                {{ number_format(
                    $vendorTotal,
                    0,
                    ',',
                    '.'
                ) }}
            </th>

        </tr>

    </tfoot>

</table>

<div
    style="
        margin-top:20px;
        padding:10px;
        border:1px solid #d9534f;
        background-color:#f9f2f2;
    "
>
    <strong>Ketentuan Sewa Vendor:</strong>
    Pihak peminjam wajib menjaga kondisi alat.
    Segala biaya kerusakan, kehilangan, atau
    keterlambatan yang dibebankan oleh vendor
    menjadi tanggung jawab pihak peminjam sesuai
    dengan tagihan vendor.
</div>

<div class="signature-box">

    <div class="signature-col">

        <p>
            Pihak Peminjam,
        </p>

        <br><br><br><br>

        <p>
            <strong>
                ({{ $order->user->name }})
            </strong>
        </p>

    </div>

    <div class="signature-col">

        <p>
            Admin Inventory SC,
        </p>

        <br><br><br><br>

        <p>
            <strong>
                (................................)
            </strong>
        </p>

    </div>

    <div class="clear"></div>

</div>

</body>
</html>