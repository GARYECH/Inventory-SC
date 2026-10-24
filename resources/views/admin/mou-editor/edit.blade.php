<x-app-layout>
<div class="min-h-screen bg-slate-100 px-3 py-6 sm:px-6"><div class="mx-auto max-w-[1600px] space-y-4">
    <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center">
        <div><a href="{{ route('admin.mou-editor.index', ['order_id' => $order->id]) }}" class="text-sm font-bold text-indigo-600">← Daftar MoU</a><h1 class="mt-2 text-2xl font-black text-slate-950">Edit MoU dari Template Asli</h1><p class="mt-1 text-sm text-slate-600">Order {{ $order->order_number }} · {{ strtoupper(str_replace(['_', '-'], ' ', $document->mou_type)) }} · Dokumen #{{ $document->id }}</p>
        @if($latestVersion)<p class="mt-1 text-xs font-bold text-emerald-700">Versi terakhir: V{{ $latestVersion->version_number }} · {{ $latestVersion->status }}</p>@else<p class="mt-1 text-xs font-bold text-amber-700">Belum disimpan sebagai versi. Template asli akan dimuat di bawah.</p>@endif</div>
        <div class="flex flex-wrap gap-2"><a href="{{ route('admin.orders') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700">Orders</a><button type="button" id="reload-legacy" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Muat Ulang Template Asli</button></div>
    </div>

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><strong>Perlu diperbaiki:</strong><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-sky-950"><strong>Ini adalah render dari template MoU lama.</strong> Klik teks di dokumen untuk mengeditnya secara langsung. Logo, CSS, layout, tabel, dan format halaman berasal dari template yang sama. Jangan hapus elemen header/footer atau tabel jika masih diperlukan. Perubahan teks dapat membuat pagination bergeser, sebagaimana dokumen biasa saat teks bertambah.</div>

    <div class="sticky top-0 z-20 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-command="undo" class="toolbar">↶ Undo</button><button type="button" data-command="redo" class="toolbar">↷ Redo</button><span class="mx-1 hidden h-7 border-l border-slate-200 sm:inline-block"></span>
            <button type="button" data-command="bold" class="toolbar"><strong>B</strong></button><button type="button" data-command="italic" class="toolbar"><em>I</em></button><button type="button" data-command="underline" class="toolbar"><u>U</u></button><button type="button" data-command="insertUnorderedList" class="toolbar">• Daftar</button><button type="button" data-command="insertOrderedList" class="toolbar">1. Daftar</button>
            <button type="button" id="add-paragraph" class="toolbar">+ Paragraf</button><button type="button" id="add-pasal" class="toolbar">+ Blok Pasal</button><button type="button" id="add-page-break" class="toolbar">+ Pemisah Halaman</button>
            <span class="flex-1"></span><span id="editor-status" class="text-xs font-bold text-slate-500">Memuat template…</span>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-300 bg-slate-200 shadow-sm"><iframe id="mou-frame" title="Editor MoU dari template lama" class="block w-full bg-slate-200" style="height: min(78vh, 1050px); min-height: 620px; border: 0;"></iframe></div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="flex flex-col gap-3 lg:flex-row lg:items-end">
        <div class="flex-1"><label for="change-note" class="mb-1.5 block text-xs font-extrabold uppercase tracking-wide text-slate-600">Catatan versi</label><input id="change-note" maxlength="500" placeholder="Contoh: Revisi identitas pihak dan Pasal garansi" class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
        <div class="flex flex-wrap gap-2"><form id="save-form" method="POST" action="{{ route('admin.mou-editor.save', [$order->id, $document->id]) }}">@csrf @method('PUT')<input type="hidden" name="change_note" id="save-note"><textarea name="rendered_html" id="save-html" hidden></textarea><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-extrabold text-white hover:bg-indigo-700">Simpan Versi Baru</button></form>
        <form id="preview-form" method="POST" action="{{ route('admin.mou-editor.preview', [$order->id, $document->id]) }}" target="_blank">@csrf<textarea name="rendered_html" id="preview-html" hidden></textarea><button type="submit" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-extrabold text-slate-800 hover:bg-slate-50">Preview PDF</button></form></div>
    </div><p class="mt-3 text-xs leading-5 text-slate-500">Simpan akan membuat versi baru; versi sebelumnya tidak ditimpa. Preview PDF dirender dari HTML template yang sedang kamu edit, bukan dari desain PDF generik yang baru.</p></div>

    @if($versions->isNotEmpty())<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black text-slate-950">Riwayat Versi</h2><div class="mt-4 divide-y divide-slate-100">@foreach($versions as $version)<div class="flex flex-col justify-between gap-3 py-3 sm:flex-row sm:items-center"><div><p class="font-extrabold text-slate-900">Versi {{ $version->version_number }} <span class="ml-2 text-xs uppercase text-amber-700">{{ $version->status }}</span></p><p class="mt-1 text-sm text-slate-600">{{ $version->change_note ?: 'Tanpa catatan' }}</p><p class="mt-1 text-xs text-slate-400">{{ $version->created_at }} · User ID {{ $version->created_by ?? '-' }}</p></div>@if(data_get(json_decode($version->content, true), 'html'))<div class="flex flex-wrap gap-2"><a target="_blank" href="{{ route('admin.mou-editor.version-pdf', [$order->id, $document->id, $version->id]) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">PDF versi ini</a><form method="POST" action="{{ route('admin.mou-editor.restore', [$order->id, $document->id, $version->id]) }}" onsubmit="return confirm('Salin isi versi ini menjadi draft versi baru?');">@csrf<button type="submit" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800">Pulihkan ke draft baru</button></form></div>@else<span class="text-xs text-slate-400">Versi editor sebelumnya · data riwayat dipertahankan</span>@endif</div>@endforeach</div></section>@endif
</div></div>

<style>
.toolbar{border:1px solid #cbd5e1;border-radius:.65rem;padding:.55rem .8rem;font-size:.8rem;font-weight:800;color:#334155;background:#fff}.toolbar:hover{background:#f8fafc}.toolbar:focus{outline:2px solid #a5b4fc;outline-offset:1px}
</style>
<script>
(function(){
    const frame = document.getElementById('mou-frame');
    const status = document.getElementById('editor-status');
    const sourceUrl = @json($sourceUrl);
    const savedBase64 = @json($savedHtmlBase64);
    let ready = false;

    function decodeBase64Utf8(value){
        const binary = atob(value);
        const bytes = Uint8Array.from(binary, character => character.charCodeAt(0));
        return new TextDecoder('utf-8').decode(bytes);
    }
    function addLegacyBaseUrl(html){
        const baseUrl = new URL('/', new URL(sourceUrl, window.location.href));
        baseUrl.search = '';
        baseUrl.hash = '';
        const safeBase = htmlEscape(baseUrl.href);
        if (/<base\b/i.test(html)) return html;
        return html.replace(/<head\b[^>]*>/i, function (headTag) {
            return headTag + '<base href="' + safeBase + '">';
        });
    }
    function frameDocument(){
        try { return frame.contentDocument || frame.contentWindow.document; }
        catch (error) { return null; }
    }
    function markReady(){
        const doc = frameDocument();
        if (!doc || !doc.body || !doc.documentElement) {
            status.textContent = 'Template gagal dimuat';
            return;
        }
        doc.designMode = 'on';
        ready = true;
        status.textContent = 'Template siap diedit';
    }
    frame.addEventListener('load', markReady);
    if (savedBase64) {
        frame.srcdoc = addLegacyBaseUrl(decodeBase64Utf8(savedBase64));
    } else {
        frame.src = sourceUrl;
    }

    function htmlEscape(value){return String(value).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));}
    function currentDoc(){
        const doc=frameDocument();
        if(!ready || !doc){alert('Template belum siap. Tunggu sampai teks "Template siap diedit" muncul.');return null;}
        doc.designMode='on';
        frame.contentWindow.focus();
        return doc;
    }
    document.querySelectorAll('.toolbar').forEach(button=>button.addEventListener('mousedown',function(event){event.preventDefault();}));
    document.querySelectorAll('[data-command]').forEach(button=>button.addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        doc.execCommand(button.dataset.command,false,null);
    }));
    document.getElementById('add-paragraph').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        doc.execCommand('insertHTML',false,'<p>Isi paragraf baru.</p>');
    });
    document.getElementById('add-pasal').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        const title=prompt('Judul Pasal atau ketentuan baru:','PASAL X');if(title===null)return;
        const body=prompt('Isi Pasal baru:','Tuliskan isi ketentuan di sini.');if(body===null)return;
        const html='<p style="margin-top:10px"><strong>'+htmlEscape(title).replace(/\n/g,'<br>')+'</strong></p><p>'+htmlEscape(body).replace(/\n/g,'<br>')+'</p>';
        doc.execCommand('insertHTML',false,html);
    });
    document.getElementById('add-page-break').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        doc.execCommand('insertHTML',false,'<div style="page-break-before:always;break-before:page;height:1px"></div>');
    });
    document.getElementById('reload-legacy').addEventListener('click',function(){
        if(!confirm('Muat ulang isi template asli? Perubahan yang belum disimpan akan hilang.'))return;
        ready=false;status.textContent='Memuat template asli…';frame.src=sourceUrl;
    });
    function serializeEditor(){
        const doc=currentDoc();if(!doc)return false;
        const previousMode=doc.designMode;
        doc.designMode='off';
        let html='<!DOCTYPE html>\n'+doc.documentElement.outerHTML;
        const baseUrl = new URL('/', new URL(sourceUrl, window.location.href));
        const safeBase = htmlEscape(baseUrl.href);
        if (!/<base\b/i.test(html)) {
            html = html.replace(/<head\b[^>]*>/i, function (headTag) {
                return headTag + '<base href=\"' + safeBase + '\">';
            });
        }
        doc.designMode=previousMode;
        document.getElementById('save-html').value=html;
        document.getElementById('preview-html').value=html;
        document.getElementById('save-note').value=document.getElementById('change-note').value;
        return true;
    }
    document.getElementById('save-form').addEventListener('submit',function(event){if(!serializeEditor())event.preventDefault();});
    document.getElementById('preview-form').addEventListener('submit',function(event){if(!serializeEditor())event.preventDefault();});
})();
</script>
</x-app-layout>
