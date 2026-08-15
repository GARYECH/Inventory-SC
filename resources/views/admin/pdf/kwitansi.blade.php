<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KUITANSI - {{ $order->kwitansi_number ?? $order->order_number }}</title>
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 14px; 
            color: #000; 
            line-height: 1.5; 
            margin: 0; 
            padding: 10px 20px; 
        }
        
        /* Warna Merah Marun SC */
        .bg-red { background-color: #9b0000; color: #ffffff; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        
        /* Tabel Utama Kiri */
        .main-table { margin-bottom: 20px; }
        .main-table td { padding: 8px 5px; vertical-align: top; }
        
        /* Tabel Rincian Kanan Bawah */
        .sub-table { width: 55%; float: right; margin-top: 10px; }
        .sub-table td { padding: 6px 5px; vertical-align: top; }
    </style>
</head>
<body>

    <!-- 🌟 MAGIC SCRIPT: NUMBER TO ENGLISH WORDS & ASSET PATHS 🌟 -->
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

    <hr style="border: 0; border-top: 1px solid #000; margin: 0 0 20px 0;">

    <!-- JUDUL -->
    <div style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 30px;">
        KUITANSI
    </div>

    <!-- MAIN INFORMASI -->
    <table class="main-table">
        <tr>
            <td class="bg-red" style="width: 25%; padding-left: 10px;">Receipt Number</td>
            <td style="width: 3%; text-align: center;">:</td>
            <td style="width: 72%; font-weight: bold;">
                {{ $order->kwitansi_number ?? 'KWT-'.$order->order_number }}
            </td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">Received from</td>
            <td style="text-align: center;">:</td>
            <td>{{ $order->proker_name }}</td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">Total</td>
            <td style="text-align: center;">:</td>
            <td>Rp {{ number_format($order->total_price, 0, ',', '.') }},-</td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">Total Amount in Words</td>
            <td style="text-align: center;">:</td>
            <td style="font-style: italic;">{{ $amountInWords }}</td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">Description</td>
            <td style="text-align: center;">:</td>
            <td>Payment for Order {{ $order->order_number }}</td>
        </tr>
    </table>

    <!-- SUB INFORMASI (TOTAL, TAX, AMOUNT PAID) -->
    <table class="sub-table">
        <tr>
            <td style="width: 40%; padding-left: 10px;">Total</td>
            <td style="width: 5%; text-align: center;">:</td>
            <td style="width: 55%; font-weight: bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }},-</td>
        </tr>
        <tr>
            <td style="padding-left: 10px;">
                Tax<br>
                <span style="font-size: 11px;">PPh 21 (2%) - Gross/Net</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">:</td>
            <td style="vertical-align: middle;">Rp 0,-</td>
        </tr>
        <tr>
            <td class="bg-red" style="padding-left: 10px;">Amount Paid</td>
            <td style="text-align: center; vertical-align: middle;">:</td>
            <td style="font-weight: bold; vertical-align: middle;">Rp {{ number_format($order->total_price, 0, ',', '.') }},-</td>
        </tr>
    </table>
    
    <div style="clear: both;"></div>

    <!-- TANDA TANGAN (PAYER & RECIPIENT) -->
    <table style="width: 100%; margin-top: 50px; text-align: center;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">Surabaya, {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">Payer,</td>
            <td style="padding-top: 10px;">Recipient,</td>
        </tr>
        <tr>
            <!-- KIRI: BENDAHARA PROKER / MAHASISWA -->
            <td style="height: 120px; vertical-align: bottom;">
                <br><br><br><br>
                <span style="font-weight: bold; text-decoration: underline; color: #cc0000;">
                    {{ $order->treasurer_name }}
                </span><br>
                <span style="color: #cc0000;">
                    Bendahara "{{ $order->proker_name }}"
                </span>
            </td>
            
            <!-- KANAN: BENDAHARA STUDENT COUNCIL (GREGORY) -->
            <td style="height: 120px; vertical-align: bottom;">
                <div>
                    @if($ttdPath && file_exists($ttdPath))
                        <img src="{{ $ttdPath }}" style="max-height: 80px; margin-bottom: 5px;">
                    @else
                        <br><br><br><br>
                    @endif
                </div>
                <span style="font-weight: bold; text-decoration: underline; color: #cc0000;">
                    Gregory Edgard Christian
                </span><br>
                <span style="color: #cc0000;">
                    Bendahara Student Council
                </span>
            </td>
        </tr>
    </table>

</body>
</html>