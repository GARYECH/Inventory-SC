<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MoU Vendor - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
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
        <div>Surat Perjanjian Penyewaan Alat (Vendor Eksternal)</div>
        <div>No. Ref: {{ $order->order_number }}</div>
    </div>

    <p>Pada hari ini, tanggal <strong>{{ now()->format('d F Y') }}</strong>, telah disetujui penyewaan alat oleh:</p>
    
    <ul>
        <li><strong>Nama Pemesan:</strong> {{ $order->user->name }}</li>
        <li><strong>Proker / Event:</strong> {{ $order->proker_name }}</li>
        <li><strong>Departemen:</strong> {{ $order->department }}</li>
        <li><strong>Periode Sewa:</strong> {{ $order->start_date->format('d M Y') }} s/d {{ $order->end_date->format('d M Y') }}</li>
    </ul>

    <p>Dengan rincian barang dari vendor sebagai berikut:</p>

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

    <!-- PASAL KHUSUS VENDOR YANG LEBIH KETAT -->
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #d9534f; background-color: #f9f2f2;">
        <p style="margin: 0;"><strong>Ketentuan Sewa Vendor (Pihak Ketiga):</strong> Pihak peminjam wajib menjaga kondisi alat. Karena ini adalah barang sewaan dari vendor luar kampus, segala bentuk denda keterlambatan atau biaya perbaikan akibat kerusakan akan dibebankan <strong>100%</strong> kepada pihak peminjam sesuai dengan tagihan dari vendor aslinya. Student Council SC hanya bertindak sebagai fasilitator.</p>
    </div>

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