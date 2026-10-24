<x-app-layout>
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8"><div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div>
        <p class="text-xs font-black uppercase tracking-[0.2em] text-indigo-600">Inventory-SC · Documents</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">MoU Editor</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Edit langsung dokumen yang dirender dari template MoU lama. Desain dan file Blade template lama tidak diganti.</p>
    </div><a href="{{ route('admin.orders') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Kembali ke Orders</a></div>

    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-sky-950"><strong>Template asli tetap digunakan.</strong> Isi awal diambil dari render MoU legacy sesuai tipe transaksi. Saat disimpan, satu snapshot HTML dibuat untuk transaksi tersebut; template Blade dan PDF MoU lama tidak ditimpa. Selalu periksa preview sebelum ditandatangani.</div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-5 py-4"><h2 class="font-extrabold text-slate-900">Dokumen MoU per transaksi</h2><p class="mt-1 text-sm text-slate-500">Pilih transaksi untuk membuka tampilan template asli.</p></div>
    @if($documents->isEmpty())<div class="p-8 text-center"><p class="font-bold text-slate-800">Belum ada dokumen MoU.</p><p class="mt-1 text-sm text-slate-500">Dokumen muncul setelah transaksi memiliki record pada order_mou_documents.</p></div>
    @else<div class="divide-y divide-slate-100">@foreach($documents as $document)<div class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between"><div class="min-w-0">
        <p class="break-words font-extrabold text-slate-900">{{ $document->order_number }} · {{ strtoupper(str_replace(['_', '-'], ' ', $document->mou_type)) }}</p>
        <p class="mt-1 text-sm text-slate-600">Proker: {{ $document->proker_name ?: '-' }} · Tipe transaksi: {{ $document->order_type ?: '-' }}</p>
        <p class="mt-1 text-xs text-slate-400">Order ID {{ $document->order_id }} · Dokumen ID {{ $document->document_id }}</p>
    </div><a href="{{ route('admin.mou-editor.edit', [$document->order_id, $document->document_id]) }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-extrabold text-white hover:bg-indigo-700">Buka & Edit MoU →</a></div>@endforeach</div><div class="border-t border-slate-100 px-5 py-4">{{ $documents->links() }}</div>@endif
    </div>
</div></div>
</x-app-layout>
