<x-app-layout>
<div class="min-h-screen bg-slate-100 px-3 py-6 sm:px-6">
    <div class="mx-auto max-w-7xl space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <a href="{{ route('admin.mou-editor.index', ['order_id' => $order->id]) }}" class="text-sm font-bold text-indigo-600">← Daftar MoU</a>
            <h1 class="mt-2 text-2xl font-black text-slate-950">View Signed MoU</h1>
            <p class="mt-1 text-sm text-slate-600">Order {{ $order->order_number }} · {{ strtoupper(str_replace(['_', '-'], ' ', $document->mou_type)) }} · Dokumen #{{ $document->id }}</p>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <strong>Proses belum berhasil:</strong>
                <ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="rounded-2xl border border-amber-300 bg-amber-50 p-5 text-sm leading-6 text-amber-950">
            <h2 class="font-black">Snapshot HTML bertanda tangan tidak tersedia</h2>
            <p class="mt-1">Karena dokumen ini tidak mempunyai snapshot HTML dari saat penandatanganan, halaman ini tidak membuka editor template biasa. PDF yang ditampilkan di bawah berasal dari file PDF signed aktif atau salinan arsip yang masih tersimpan.</p>
            @if(empty($document->signed_file_path))
                <p class="mt-2 font-bold">Catatan: record dokumen saat ini tidak memiliki PDF aktif. File di bawah adalah salinan arsip, bukan file aktif yang tertaut pada dokumen.</p>
            @endif
            <p class="mt-2 text-xs">Mengunggah PDF koreksi akan menjadikannya View Signed terbaru dan mempertahankan file sumber di arsip. Pastikan revisi telah disetujui. Perubahan isi substantif mungkin memerlukan penandatanganan ulang; tanda tangan yang terlihat pada PDF bukan verifikasi tanda tangan digital tersertifikasi.</p>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="font-black text-slate-900">PDF signed yang tersimpan</h2>
                    @if($signedPdfSourcePath !== '')
                        <p class="mt-1 break-all text-xs text-slate-500">{{ $signedPdfSourcePath }}</p>
                    @else
                        <p class="mt-1 text-sm text-rose-700">Tidak ditemukan PDF aktif maupun PDF arsip di storage.</p>
                    @endif
                </div>
                @if($signedPdfSourceUrl)
                    <a href="{{ $signedPdfSourceUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Buka PDF di Tab Baru ↗</a>
                @endif
            </div>
            @if($signedPdfSourceUrl)
                <iframe src="{{ $signedPdfSourceUrl }}#toolbar=1&navpanes=0" title="PDF View Signed MoU" class="block w-full bg-slate-200" style="height: min(78vh, 1100px); min-height: 620px; border: 0;"></iframe>
            @else
                <div class="p-8 text-center text-sm text-slate-600">Belum ada file yang bisa ditampilkan. Jangan gunakan template biasa sebagai pengganti PDF signed.</div>
            @endif
        </section>

        @if($signedPdfSourcePath !== '')
            <section class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Unggah PDF Koreksi untuk View Signed</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">Edit salinan PDF menggunakan aplikasi PDF terlebih dahulu, lalu unggah PDF koreksi di sini. Sistem akan menyimpan file sumber ke arsip dan mengubah View Signed ke PDF baru.</p>
                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.mou-editor.reissue-signed-upload', [$order->id, $document->id]) }}" onsubmit="return confirm('Jadikan PDF unggahan sebagai View Signed terbaru? File sumber akan tetap disimpan di arsip.');" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="corrected-pdf" class="mb-1 block text-xs font-bold text-slate-700">PDF koreksi (maks. 10 MB)</label>
                        <input id="corrected-pdf" type="file" name="corrected_pdf" accept="application/pdf,.pdf" required class="block w-full rounded-xl border border-slate-300 bg-white p-2 text-sm">
                    </div>
                    <div>
                        <label for="change-note-upload" class="mb-1 block text-xs font-bold text-slate-700">Alasan koreksi</label>
                        <input id="change-note-upload" type="text" name="change_note_upload" maxlength="500" required placeholder="Contoh: Koreksi nomor atau format dokumen" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <label class="flex items-start gap-2 text-xs leading-5 text-slate-600">
                        <input type="checkbox" name="confirm_visual_reissue_upload" value="1" required class="mt-1 rounded border-slate-400">
                        <span>Saya memastikan koreksi telah disetujui dan memahami bahwa perubahan isi substantif dapat memerlukan tanda tangan ulang.</span>
                    </label>
                    <button type="submit" class="rounded-xl bg-amber-800 px-5 py-3 text-sm font-extrabold text-white hover:bg-amber-900">Arsipkan Sumber & Jadikan PDF Ini View Signed</button>
                </form>
            </section>
        @endif

        @if($signedHistory->isNotEmpty())
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Arsip PDF Signed Sebelumnya</h2>
                <p class="mt-1 text-sm text-slate-500">Tautan di bawah membuka file arsip dan tidak mengubah View Signed aktif.</p>
                <div class="mt-4 divide-y divide-slate-100">
                    @foreach($signedHistory as $signedVersion)
                        <div class="flex flex-col justify-between gap-3 py-3 sm:flex-row sm:items-center">
                            <div>
                                <p class="break-all text-sm font-bold text-slate-800">{{ basename($signedVersion->signed_file_path) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $signedVersion->mou_number ?: 'Nomor MoU tidak tercatat' }} · {{ $signedVersion->superseded_at }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $signedVersion->reason ?: 'Tanpa catatan' }}</p>
                            </div>
                            @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($signedVersion->signed_file_path))
                                <a target="_blank" rel="noopener noreferrer" href="{{ asset('storage/' . $signedVersion->signed_file_path) }}" class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">Buka PDF Arsip ↗</a>
                            @else
                                <span class="text-xs font-bold text-rose-700">File tidak ditemukan</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
</x-app-layout>
