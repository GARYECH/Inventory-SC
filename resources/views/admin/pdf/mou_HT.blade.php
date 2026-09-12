<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>
        MoU Handy Talkie - {{ $order->order_number }}
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #111827;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .section {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <h1>
        SURAT PERJANJIAN PEMINJAMAN<br>
        HANDY TALKIE
    </h1>

    <p>
        Nomor Order:
        <strong>{{ $order->order_number }}</strong>
    </p>

    <p>
        Yang bertanda tangan di bawah ini menyatakan bahwa
        peminjaman Handy Talkie dilakukan sesuai dengan
        ketentuan inventaris Student Council.
    </p>

    <div class="section">

        <strong>Rincian Handy Talkie:</strong>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Handy Talkie</th>
                    <th>Jumlah</th>
                </tr>
            </thead>

            <tbody>

                @php
                    $number = 1;
                @endphp

                @foreach(
                    $order->orderItems
                    as $detail
                )

                    @php
                        $item = $detail->item;
                    @endphp

                    @if(
                        $item &&
                        $item->transaction_type === 'Handy Talkie'
                    )

                        <tr>
                            <td>
                                {{ $number++ }}
                            </td>

                            <td>
                                {{
                                    $item->transaction_detail
                                    ??
                                    $item->name
                                }}
                            </td>

                            <td>
                                {{ $detail->quantity }}
                                unit
                            </td>
                        </tr>

                    @endif

                @endforeach

            </tbody>
        </table>

    </div>

    <div class="section">

        <p>
            Tanggal Pengambilan:
            <strong>
                {{
                    $order->start_date
                    ? $order->start_date->format('d M Y')
                    : '-'
                }}
            </strong>

            pukul

            <strong>
                {{ $order->start_time ?? '-' }}
            </strong>
        </p>

        <p>
            Tanggal Pengembalian:
            <strong>
                {{
                    $order->end_date
                    ? $order->end_date->format('d M Y')
                    : '-'
                }}
            </strong>

            pukul

            <strong>
                {{ $order->end_time ?? '-' }}
            </strong>
        </p>

    </div>

    <div class="section">

        <p>
            Peminjam wajib menjaga seluruh barang yang dipinjam
            dan mengembalikannya sesuai jumlah, kondisi,
            tanggal, dan waktu yang telah disepakati.
        </p>

    </div>

</body>
</html>