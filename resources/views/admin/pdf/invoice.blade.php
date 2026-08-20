<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>INVOICE - {{ $order->invoice_number ?? $order->order_number }}</title>
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 13px; color: #000; line-height: 1.4; margin: 0; padding: 0;
        }
        .bg-red { background-color: #a00000; color: #ffffff; }
        table { width: 100%; border-collapse: collapse; }
        .items-table { margin-bottom: 20px; border: 1px solid #000; }
        .items-table th { background-color: #a00000; color: #ffffff; text-align: center; font-weight: bold; border: 1px solid #000; padding: 8px 5px; }
        .items-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .no-border-table td { border: none !important; padding: 0 !important; }
    </style>
</head>
<body>

    <!-- 🌟 MAGIC SCRIPT: NUMBER TO ENGLISH WORDS 🌟 -->
    @php
        $logoDb = \App\Models\Setting::where('key', 'logo_sc')->value('value');
        $ttdDb = \App\Models\Setting::where('key', 'ttd_bendahara')->value('value');
        $logoPath = $logoDb ? storage_path('app/public/' . $logoDb) : null;
        $ttdPath = $ttdDb ? storage_path('app/public/' . $ttdDb) : null;

        if (!function_exists('numberToWords')) {
            function numberToWords($num) {
                $ones = [
                    0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 
                    10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
                ];
                $tens = [
                    0 => '', 1 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
                ];
                if ($num == 0) return 'Zero';
                if ($num < 20) return $ones[$num];
                if ($num < 100) return $tens[floor($num / 10)] . ($num % 10 !== 0 ? ' ' . $ones[$num % 10] : '');
                if ($num < 1000) return $ones[floor($num / 100)] . ' Hundred' . ($num % 100 !== 0 ? ' and ' . numberToWords($num % 100) : '');
                if ($num < 1000000) return numberToWords(floor($num / 1000)) . ' Thousand' . ($num % 1000 !== 0 ? ' ' . numberToWords($num % 1000) : '');
                if ($num < 1000000000) return numberToWords(floor($num / 1000000)) . ' Million' . ($num % 1000000 !== 0 ? ' ' . numberToWords($num % 1000000) : '');
                return $num;
            }
        }
        $amountInWords = numberToWords($order->total_price) . ' Rupiah';
    @endphp

    <!-- KOP SURAT SC -->
    <table style="margin-bottom: 15px;">
        <tr>
            <td width="30%" style="vertical-align: middle;">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo SC" style="max-height: 70px;"> 
                @endif
            </td>
            <td width="70%" style="text-align: right; line-height: 1.2;">
                <span style="font-weight: bold; font-size: 14px;">UNIVERSITAS CIPUTRA SURABAYA</span><br>
                <span style="font-weight: bold; font-size: 14px;">STUDENT COUNCIL</span><br>
                Citraland CBD Boulevard, Surabaya, 60219<br>
                Jawa Timur – Indonesia<br>
                Telepon: (031)7451699; Fax: (031)7451698<br>
                Email: studentcouncil@ciputra.ac.id
            </td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px solid #000; margin: 0 0 15px 0;">

    <div style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 25px; letter-spacing: 1px;">INVOICE</div>

    <!-- INFORMASI BILLING & INVOICE -->
    <table style="margin-bottom: 20px;">
        <tr>
            <!-- Bagian Kiri (Bill To) -->
            <td width="48%" style="vertical-align: top;">
                <div class="bg-red" style="padding: 5px 10px; font-weight: bold; margin-bottom: 5px; width: 100%; box-sizing: border-box;">Bill To</div>
                <table cellpadding="3">
                    <tr><td width="25%">Name</td><td width="5%">:</td><td width="70%">{{ $order->user->name }}</td></tr>
                    <!-- 🌟 ALAMAT TELAH DIUBAH MENJADI ALAMAT ASLI DARI DATABASE 🌟 -->
                    <tr><td>Address</td><td>:</td><td>{{ $order->address ?? '-' }}</td></tr>
                    <tr><td>Number</td><td>:</td><td>{{ $order->phone_number }}</td></tr>
                </table>
            </td>
            <td width="4%"></td>
            <!-- Bagian Kanan (Invoice Details) -->
            <td width="48%" style="vertical-align: top; padding-top: 25px;">
                <table cellpadding="3">
                    <!-- 🌟 INVOICE NUMBER: Akan pakai yg manual kalau diisi Admin, kalau kosong pakai auto (ORD-xxx) 🌟 -->
                    <tr><td width="35%">Invoice Num.</td><td width="5%">:</td><td width="60%">{{ $order->invoice_number ?? $order->order_number }}</td></tr>
                    <tr><td>Date</td><td>:</td><td>{{ $order->created_at->format('d/m/Y') }}</td></tr>
                    <!-- 🌟 DUE DATE: OTOMATIS H-1 DARI TANGGAL MULAI PINJAM 🌟 -->
                    <tr><td>Due Date</td><td>:</td><td>{{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->subDays(1)->format('d/m/Y') : '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TABEL RINCIAN BARANG -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th><th width="40%">Description</th><th width="7%">QTY</th><th width="24%">Unit Price</th><th width="24%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $detail->item->name }}</td>
                <td style="text-align: center;">{{ $detail->quantity }}</td>
                
                <!-- 🌟 PERBAIKAN HARGA SATUAN: MENGGUNAKAN LOGIKA PINTAR 🌟 -->
                <td>
                    @if($detail->price == 0)
                        <div style="text-align: center; color: #a00000; font-weight: bold; font-size: 11px; margin-top: 2px;">FREE (SC Internal)</div>
                    @else
                        <table class="no-border-table"><tr><td style="text-align: left; width: 20%;">Rp</td><td style="text-align: right; width: 80%;">{{ number_format($detail->price, 0, ',', '.') }},-</td></tr></table>
                    @endif
                </td>
                
                <!-- 🌟 PERBAIKAN SUBTOTAL: MENGGUNAKAN LOGIKA PINTAR 🌟 -->
                <td>
                    <table class="no-border-table"><tr><td style="text-align: left; width: 20%;">Rp</td><td style="text-align: right; width: 80%;">{{ number_format($detail->subtotal_price, 0, ',', '.') }},-</td></tr></table>
                </td>
            </tr>
            @endforeach
            
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            
            <!-- Footer: Amount in words & Subtotal -->
            <tr>
                <td colspan="2" rowspan="2" style="vertical-align: top; padding: 10px;">
                    <span style="font-weight: bold;">Total Amount in Words:</span><br><br>
                    <!-- 🌟 HASIL TERJEMAHAN NOMINAL KE BAHASA INGGRIS DIMUNCULKAN DI SINI 🌟 -->
                    <span><em>{{ $amountInWords }}</em></span>
                </td>
                <td colspan="2">Subtotal</td>
                <td>
                    <table class="no-border-table"><tr><td style="text-align: left; width: 20%;">Rp</td><td style="text-align: right; width: 80%;">{{ number_format($order->total_price, 0, ',', '.') }},-</td></tr></table>
                </td>
            </tr>
            <tr>
                <td colspan="2">Tax</td>
                <td>
                    <table class="no-border-table"><tr><td style="text-align: left; width: 20%;">Rp</td><td style="text-align: right; width: 80%;">0,-</td></tr></table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: none; background-color: #ffffff;"></td>
                <td colspan="2" class="bg-red" style="font-weight: bold;">Total</td>
                <td class="bg-red">
                    <table class="no-border-table" style="color: #ffffff; font-weight: bold;"><tr><td style="text-align: left; width: 20%;">Rp</td><td style="text-align: right; width: 80%;">{{ number_format($order->total_price, 0, ',', '.') }},-</td></tr></table>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 35px;">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <div class="bg-red" style="padding: 5px 10px; font-weight: bold; margin-bottom: 10px; width: 80%;">Payment Method</div>
                <p style="margin: 5px 0;">Bank Central Asia (BCA)</p><p style="margin: 5px 0;">No Rekening: 8620797163</p><p style="margin: 5px 0;">a/n Chalistha Dea Yuwanda</p>
            </td>
            <td width="50%" style="vertical-align: top; text-align: center;">
                <p style="margin: 5px 0;">Diketahui,</p>
                <div>
                    @if($ttdPath && file_exists($ttdPath))
                        <img src="{{ $ttdPath }}" alt="TTD Bendahara" style="max-height: 70px; margin-top: 10px; margin-bottom: 5px;">
                    @else
                        <br><br><br><br>
                    @endif
                </div>
                <p style="margin: 5px 0; font-weight: bold; text-decoration: underline;">Gregory Edgard Christian</p>
                <p style="margin: 5px 0;">Bendahara Student Council</p>
            </td>
        </tr>
    </table>
</body>
</html>