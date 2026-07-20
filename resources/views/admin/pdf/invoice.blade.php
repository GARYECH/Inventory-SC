<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>INVOICE - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #000; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .header h2 { margin: 0; font-size: 14px; font-weight: normal; }
        .header p { margin: 2px 0; font-size: 10px; }
        .invoice-title { text-align: center; font-size: 18px; font-weight: bold; margin-top: 15px; margin-bottom: 20px; letter-spacing: 2px; }
        
        /* Tabel Info Kanan Kiri */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; padding: 2px 5px; border: none; }
        .meta-table .label { width: 80px; font-weight: bold; }
        .meta-table .colon { width: 10px; text-align: center; }
        
        /* Tabel Barang */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px; }
        .items-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .items-table .center { text-align: center; }
        .items-table .right { text-align: right; }
        
        /* Bagian Bawah */
        .bottom-section { width: 100%; margin-top: 30px; }
        .payment-box { float: left; width: 50%; }
        .payment-box p { margin: 2px 0; }
        .signature-box { float: right; width: 40%; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <!-- KOP SURAT SC -->
    <div class="header">
        <h1>STUDENT COUNCIL</h1>
        <h2>OF UNIVERSITAS CIPUTRA</h2>
        <p>UNIVERSITAS CIPUTRA SURABAYA<br>STUDENT COUNCIL</p>
        <p>Citraland CBD Boulevard, Surabaya, 60219<br>Jawa Timur - Indonesia</p>
        <p>Telepon: (031)7451699; Fax: (031)7451698<br>Email: studentcouncil@ciputra.ac.id</p>
    </div>

    <div class="invoice-title">INVOICE</div>

    <!-- INFORMASI BILLING & INVOICE[cite: 1] -->
    <table class="meta-table">
        <tr>
            <!-- Bagian Kiri (Bill To) -->
            <td width="50%">
                <table class="meta-table">
                    <tr><td colspan="3" style="font-weight: bold; text-decoration: underline; padding-bottom: 5px;">Bill To</td></tr>
                    <tr><td class="label">Name</td><td class="colon">:</td><td>{{ $order->user->name }}</td></tr>
                    <tr><td class="label">Proker</td><td class="colon">:</td><td>{{ $order->proker_name }} ({{ $order->department }})</td></tr>
                    <tr><td class="label">Number</td><td class="colon">:</td><td>{{ $order->phone_number }}</td></tr>
                </table>
            </td>
            <!-- Bagian Kanan (Invoice Details) -->
            <td width="50%">
                <table class="meta-table">
                    <tr><td class="label">Invoice Num.</td><td class="colon">:</td><td style="font-weight:bold;">{{ $order->order_number }}</td></tr>
                    <tr><td class="label">Date</td><td class="colon">:</td><td>{{ $order->created_at->format('d F Y') }}</td></tr>
                    <tr><td class="label">Due Date</td><td class="colon">:</td><td>{{ $order->start_date ? $order->start_date->format('d F Y') : '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TABEL RINCIAN BARANG[cite: 1] -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Description</th>
                <th width="10%">QTY</th>
                <th width="20%">Unit Price</th>
                <th width="20%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $detail)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $detail->item->name }}</td>
                <td class="center">{{ $detail->quantity }}</td>
                <td class="right">Rp {{ number_format($detail->item->price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <!-- Bagian Summary Total -->
            <tr>
                <td colspan="2" style="border: none;">
                    <span style="font-style: italic; font-size: 10px;">Total Amount in Words: <br> (Sesuai Nominal Angka)</span>
                </td>
                <td colspan="2" class="right" style="font-weight: bold;">Subtotal</td>
                <td class="right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border: none;">
                    <span style="font-size: 9px;">*Mohon untuk memilih salah satu<br>(Nett/Gross)</span>
                </td>
                <td colspan="2" class="right" style="font-weight: bold;">Tax</td>
                <td class="right">-</td>
            </tr>
            <tr>
                <td colspan="2" style="border: none; font-size: 9px;">*Contoh format penulisan<br>Rp 50.000,-</td>
                <td colspan="2" class="right" style="font-weight: bold; font-size: 14px;">Total</td>
                <td class="right" style="font-weight: bold; font-size: 14px;">Rp {{ number_format($order->total_price, 0, ',', '.') }},-</td>
            </tr>
        </tfoot>
    </table>

    <!-- BAGIAN PEMBAYARAN DAN TTD[cite: 1] -->
    <div class="bottom-section">
        <div class="payment-box">
            <p style="font-weight: bold; text-decoration: underline;">Payment Method</p>
            <p>Bank Central Asia (BCA)</p>
            <p>No Rekening: 8620797163</p>
            <p>a/n Chalistha Dea Yuwanda</p>
        </div>
        
        <div class="signature-box">
            <p>Diketahui,</p>
            <br><br><br><br>
            <p style="text-decoration: underline;">(...........................................)</p>
            <p>Bendahara "{{ $order->proker_name }}"</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>