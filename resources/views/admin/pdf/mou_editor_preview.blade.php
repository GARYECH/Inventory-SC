<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $content['document_title'] ?? 'MoU' }}</title>
    <style>
        @page { size: A4 portrait; margin: 22mm 18mm 20mm 18mm; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; color: #111827; font-size: 10pt; line-height: 1.55; }
        .label { color: #475569; font-size: 8pt; text-transform: uppercase; letter-spacing: .8px; font-weight: bold; }
        .title { text-align: center; font-size: 15pt; font-weight: bold; text-transform: uppercase; margin: 14px 0 4px; }
        .number { text-align: center; font-size: 9pt; margin-bottom: 22px; }
        .watermark { text-align: center; color: #b91c1c; border: 1px solid #fecaca; background: #fff1f2; padding: 6px; font-size: 8pt; font-weight: bold; margin-bottom: 18px; }
        h2 { font-size: 11pt; margin: 19px 0 7px; text-transform: uppercase; page-break-after: avoid; }
        p { margin: 5px 0 9px; }
        table { width: 100%; border-collapse: collapse; }
        .parties td { width: 50%; vertical-align: top; padding: 9px; border: 1px solid #d1d5db; }
        .items th, .items td { border: 1px solid #cbd5e1; padding: 6px; font-size: 8.5pt; }
        .items th { background: #f1f5f9; text-align: left; }
        .items .right { text-align: right; }
        .clause { page-break-inside: avoid; }
        .clause-body { white-space: normal; }
        .signatures { margin-top: 35px; page-break-inside: avoid; }
        .signatures td { width: 50%; text-align: center; vertical-align: top; padding: 6px 12px; }
        .sign-space { height: 72px; }
        .muted { color: #64748b; font-size: 8.5pt; }
        .footer { margin-top: 20px; border-top: 1px solid #cbd5e1; padding-top: 7px; color: #64748b; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="watermark">{{ $versionLabel }} · PERIKSA ISI DAN KETENTUAN SEBELUM DIGUNAKAN</div>
    <div class="label" style="text-align:center">Inventory-SC · Dokumen kerja sama</div>
    <div class="title">{{ $content['document_title'] ?? 'Memorandum of Understanding' }}</div>
    <div class="number">Nomor: {{ $content['document_number'] ?: '-' }}</div>

    <p>Pada hari ini, {{ !empty($content['signed_date']) ? \Carbon\Carbon::parse($content['signed_date'])->translatedFormat('l, d F Y') : '[tanggal]' }}, bertempat di {{ $content['signed_city'] ?: '[tempat]' }}, para pihak di bawah ini menyusun dokumen kerja sama dengan identitas sebagai berikut.</p>

    <table class="parties">
        <tr>
            <td>
                <p class="label">Pihak Pertama</p>
                <p><strong>{{ data_get($content, 'parties.first.institution', '-') }}</strong></p>
                @if(data_get($content, 'parties.first.address'))<p>{{ data_get($content, 'parties.first.address') }}</p>@endif
                <p>Diwakili oleh: {{ data_get($content, 'parties.first.representative') ?: '-' }}</p>
                <p>Jabatan: {{ data_get($content, 'parties.first.position') ?: '-' }}</p>
            </td>
            <td>
                <p class="label">Pihak Kedua</p>
                <p><strong>{{ data_get($content, 'parties.second.institution', '-') }}</strong></p>
                @if(data_get($content, 'parties.second.address'))<p>{{ data_get($content, 'parties.second.address') }}</p>@endif
                <p>Diwakili oleh: {{ data_get($content, 'parties.second.representative') ?: '-' }}</p>
                <p>Jabatan: {{ data_get($content, 'parties.second.position') ?: '-' }}</p>
            </td>
        </tr>
    </table>

    <p>Kedua pihak sepakat untuk melaksanakan kerja sama dengan ketentuan sebagaimana diatur dalam Pasal-Pasal berikut. Pastikan seluruh rumusan telah diperiksa dan disepakati sebelum dokumen digunakan.</p>

    @foreach(($content['clauses'] ?? []) as $index => $clause)
        <div class="clause">
            <h2>Pasal {{ $index + 1 }} — {{ $clause['title'] ?? '' }}</h2>
            <div class="clause-body">{!! nl2br(e($clause['body'] ?? '')) !!}</div>
        </div>
    @endforeach

    @php($snapshot = $content['transaction_snapshot'] ?? [])
    @if(!empty($snapshot['items']))
        <h2>Lampiran — Ringkasan Transaksi</h2>
        <p class="muted">Nomor transaksi: {{ $snapshot['order_number'] ?? $order->order_number }} · Proker: {{ $snapshot['proker_name'] ?? '-' }} · Tipe: {{ $snapshot['order_type'] ?? '-' }}</p>
        <table class="items">
            <thead><tr><th>Barang</th><th>Jumlah</th><th>Ukuran</th><th class="right">Subtotal (Rp)</th></tr></thead>
            <tbody>
                @foreach($snapshot['items'] as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '-' }}</td>
                        <td>{{ $item['quantity'] ?? 0 }}</td>
                        <td>{{ $item['size'] ?? '-' }}</td>
                        <td class="right">{{ number_format((int) ($item['subtotal'] ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table style="margin-top:8px">
            <tr><td style="text-align:right">Total sebelum pajak</td><td style="text-align:right;width:32%">Rp {{ number_format((int) ($snapshot['subtotal'] ?? 0), 0, ',', '.') }}</td></tr>
            @if((int) ($snapshot['tax'] ?? 0) > 0)<tr><td style="text-align:right">Pajak / gross-up</td><td style="text-align:right">Rp {{ number_format((int) $snapshot['tax'], 0, ',', '.') }}</td></tr>@endif
            <tr><td style="text-align:right;font-weight:bold;border-top:1px solid #64748b;padding-top:5px">Grand total</td><td style="text-align:right;font-weight:bold;border-top:1px solid #64748b;padding-top:5px">Rp {{ number_format((int) ($snapshot['grand_total'] ?? 0), 0, ',', '.') }}</td></tr>
        </table>
    @endif

    <p style="margin-top:22px">Demikian dokumen ini disusun untuk diperiksa dan disepakati para pihak. Penandatanganan dilakukan setelah isi dokumen dinyatakan benar oleh masing-masing pihak.</p>

    <table class="signatures">
        <tr>
            <td>{{ $content['signed_city'] ?: '[tempat]' }}, {{ !empty($content['signed_date']) ? \Carbon\Carbon::parse($content['signed_date'])->translatedFormat('d F Y') : '[tanggal]' }}<br><strong>Pihak Pertama</strong><div class="sign-space"></div><strong><u>{{ data_get($content, 'parties.first.representative') ?: '[Nama]' }}</u></strong><br>{{ data_get($content, 'parties.first.position') ?: '[Jabatan]' }}</td>
            <td>{{ $content['signed_city'] ?: '[tempat]' }}, {{ !empty($content['signed_date']) ? \Carbon\Carbon::parse($content['signed_date'])->translatedFormat('d F Y') : '[tanggal]' }}<br><strong>Pihak Kedua</strong><div class="sign-space"></div><strong><u>{{ data_get($content, 'parties.second.representative') ?: '[Nama]' }}</u></strong><br>{{ data_get($content, 'parties.second.position') ?: '[Jabatan]' }}</td>
        </tr>
    </table>

    <div class="footer">Order {{ $order->order_number }} · Dokumen MoU {{ $document->id }} · {{ $versionLabel }}</div>
</body>
</html>
