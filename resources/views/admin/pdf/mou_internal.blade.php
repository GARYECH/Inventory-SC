<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MoU Internal - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        table { w-full; border-collapse: collapse; margin-top: 15px; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .signature-box { width: 100%; margin-top: 50px; }
        .signature-col { width: 50%; float: left; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">STUDENT COUNCIL UNIVERSITAS CIPUTRA</div>
        <div>Surat Perjanjian Peminjaman Alat (Internal)</div>
        <div>No. Ref: {{ $order->order_number }}</div>
    </div>

    <p>Pada hari ini, tanggal <strong>{{ now()->format('d F Y') }}</strong>, telah disetujui peminjaman alat oleh:</p>
    
    <ul>
        <li><strong>Nama Pemesan:</strong> {{ $order->user->name }}</li>
        <li><strong>Proker / Event:</strong> {{ $order->proker_name }}</li>
        <li><strong>Departemen:</strong> {{ $order->department }}</li>
        <li><strong>Periode Pinjam:</strong> {{ $order->start_date->format('d M Y') }} s/d {{ $order->end_date->format('d M Y') }}</li>
    </ul>

    <p>Dengan rincian barang sebagai berikut:</p>

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
            @foreach($order->orderItems as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->item->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">GRAND TOTAL:</th>
                <th>Rp {{ number_format($order->total_price, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <p><strong>Ketentuan (SOP):</strong> Pihak peminjam wajib menjaga kondisi barang dan bertanggung jawab penuh atas segala kerusakan atau kehilangan yang terjadi selama masa peminjaman.</p>

    <div class="signature-box">
        <div class="signature-col">
            <p>Pihak Peminjam,</p>
            <br><br><br><br>
            <p><strong>({{ $order->user->name }})</strong></p>
        </div>
        <div class="signature-col">
            <p>Admin Inventory SC,</p>
            <br><br><br><br>
            <p><strong>(...........................................)</strong></p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>