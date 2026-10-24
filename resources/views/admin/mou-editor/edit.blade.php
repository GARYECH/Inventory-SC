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

    <div class="sticky top-0 z-30 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="editor-undo" class="toolbar" title="Undo (Ctrl/Cmd+Z)">↶ Undo</button>
            <button type="button" id="editor-redo" class="toolbar" title="Redo (Ctrl/Cmd+Y)">↷ Redo</button>
            <span class="mx-1 hidden h-7 border-l border-slate-200 sm:inline-block"></span>
            <select id="format-block" class="toolbar-select" aria-label="Format paragraf" title="Format paragraf">
                <option value="P">Paragraf</option><option value="H1">Heading 1</option><option value="H2">Heading 2</option><option value="H3">Heading 3</option><option value="BLOCKQUOTE">Kutipan</option><option value="PRE">Teks monospasi</option>
            </select>
            <select id="font-family" class="toolbar-select" aria-label="Jenis font" title="Jenis font">
                <option value="Arial">Arial</option><option value="Times New Roman">Times New Roman</option><option value="Georgia">Georgia</option><option value="Calibri">Calibri</option><option value="Verdana">Verdana</option>
            </select>
            <select id="font-size" class="toolbar-select" aria-label="Ukuran font" title="Ukuran font">
                <option value="2">10 pt</option><option value="3" selected>12 pt</option><option value="4">14 pt</option><option value="5">18 pt</option><option value="6">24 pt</option><option value="7">36 pt</option>
            </select>
            <button type="button" data-command="bold" class="toolbar" title="Bold"><strong>B</strong></button>
            <button type="button" data-command="italic" class="toolbar" title="Italic"><em>I</em></button>
            <button type="button" data-command="underline" class="toolbar" title="Underline"><u>U</u></button>
            <button type="button" data-command="strikeThrough" class="toolbar" title="Coret">S̶</button>
            <button type="button" data-command="removeFormat" class="toolbar" title="Hapus format teks">Hapus format</button>
        </div>
        <div class="mt-2 flex flex-wrap items-center gap-2">
            <button type="button" data-command="justifyLeft" class="toolbar" title="Rata kiri">☰ Kiri</button>
            <button type="button" data-command="justifyCenter" class="toolbar" title="Rata tengah">☷ Tengah</button>
            <button type="button" data-command="justifyRight" class="toolbar" title="Rata kanan">☰ Kanan</button>
            <button type="button" data-command="justifyFull" class="toolbar" title="Rata kiri-kanan">☷ Justify</button>
            <button type="button" data-command="insertUnorderedList" class="toolbar">• Daftar</button>
            <button type="button" data-command="insertOrderedList" class="toolbar">1. Daftar</button>
            <label class="toolbar-color" title="Warna teks">Teks <input id="text-color" type="color" value="#222222" aria-label="Warna teks"></label>
            <label class="toolbar-color" title="Warna sorot">Sorot <input id="highlight-color" type="color" value="#fff2a8" aria-label="Warna sorot"></label>
            <button type="button" id="insert-link" class="toolbar">Tambah Link</button>
            <button type="button" data-command="unlink" class="toolbar">Lepas Link</button>
            <button type="button" data-command="insertHorizontalRule" class="toolbar">Garis</button>
            <button type="button" id="insert-paragraph" class="toolbar">+ Paragraf</button>
            <button type="button" id="insert-pasal" class="toolbar">+ Blok Pasal</button>
            <button type="button" id="insert-table" class="toolbar">+ Tabel</button>
            <button type="button" id="insert-page-break" class="toolbar">Pemisah Halaman</button>
        </div>
        <div class="mt-2 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-2">
            <span class="toolbar-caption">Halaman</span>
            <select id="page-select" class="toolbar-select" aria-label="Pilih halaman"><option value="0">Halaman 1</option></select>
            <button type="button" id="add-blank-page" class="toolbar">+ Halaman Kosong</button>
            <button type="button" id="duplicate-page" class="toolbar">Duplikat Halaman</button>
            <button type="button" id="delete-page" class="toolbar toolbar-danger">Hapus Halaman</button>
            <button type="button" id="move-page-up" class="toolbar" title="Pindahkan halaman ke atas">↑ Pindah</button>
            <button type="button" id="move-page-down" class="toolbar" title="Pindahkan halaman ke bawah">↓ Pindah</button>
            <span class="toolbar-caption ml-1">Zoom</span>
            <select id="zoom-level" class="toolbar-select" aria-label="Zoom dokumen"><option value="50">50%</option><option value="60">60%</option><option value="70">70%</option><option value="80">80%</option><option value="90">90%</option><option value="100" selected>100%</option><option value="110">110%</option><option value="125">125%</option></select>
            <span class="flex-1"></span>
            <span id="page-count" class="text-xs font-bold text-slate-500">Halaman 1 dari 1</span>
            <span id="editor-status" class="text-xs font-bold text-slate-500">Memuat template…</span>
        </div>
        <div class="mt-2 flex flex-wrap items-center gap-2 rounded-lg bg-slate-50 px-3 py-2">
            <span class="toolbar-caption">Tabel terpilih</span>
            <button type="button" id="table-add-row" class="toolbar">+ Baris</button>
            <button type="button" id="table-delete-row" class="toolbar">Hapus Baris</button>
            <button type="button" id="table-add-column" class="toolbar">+ Kolom</button>
            <button type="button" id="table-delete-column" class="toolbar">Hapus Kolom</button>
            <button type="button" id="table-delete" class="toolbar toolbar-danger">Hapus Tabel</button>
            <span class="text-xs text-slate-500">Pilih sel tabel terlebih dahulu. Mengubah tabel bawaan MoU dapat mengubah layout.</span>
        </div>
    </div>

    <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-950">
        <strong>Catatan halaman:</strong> Hapus Halaman menghapus semua isi pada halaman itu, termasuk teks hukum, tabel, header, dan footer di halaman tersebut. Gunakan Undo atau simpan versi baru untuk menjaga riwayat. Template asli tidak ikut berubah.
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
.toolbar{border:1px solid #cbd5e1;border-radius:.65rem;padding:.48rem .68rem;font-size:.76rem;font-weight:800;color:#334155;background:#fff;white-space:nowrap}
.toolbar:hover{background:#f8fafc}.toolbar:focus,.toolbar-select:focus{outline:2px solid #a5b4fc;outline-offset:1px}
.toolbar-select{max-width:210px;border:1px solid #cbd5e1;border-radius:.65rem;padding:.48rem .6rem;background:#fff;color:#334155;font-size:.76rem;font-weight:700}
.toolbar-color{display:inline-flex;align-items:center;gap:.35rem;border:1px solid #cbd5e1;border-radius:.65rem;padding:.25rem .45rem;background:#fff;color:#334155;font-size:.74rem;font-weight:800}
.toolbar-color input{width:24px;height:23px;padding:0;border:0;background:transparent;cursor:pointer}
.toolbar-caption{font-size:.72rem;font-weight:800;color:#64748b}
.toolbar-danger{border-color:#fecaca;color:#991b1b;background:#fff}
.toolbar-danger:hover{background:#fef2f2}
@media(max-width:640px){.toolbar-select{max-width:140px}.toolbar{padding:.42rem .55rem}}
</style>

<script>
(function(){
    const frame = document.getElementById('mou-frame');
    const status = document.getElementById('editor-status');
    const pageSelect = document.getElementById('page-select');
    const pageCount = document.getElementById('page-count');
    const sourceUrl = @json($sourceUrl);
    const savedBase64 = @json($savedHtmlBase64);
    let ready = false;
    let savedRange = null;
    let activePageIndex = 0;
    let historySnapshots = [];
    let historyIndex = -1;
    let historyTimer = null;
    let applyingHistory = false;
    const maxHistory = 60;

    function decodeBase64Utf8(value){
        const binary = atob(value);
        const bytes = Uint8Array.from(binary, character => character.charCodeAt(0));
        return new TextDecoder('utf-8').decode(bytes);
    }
    function frameDocument(){
        try { return frame.contentDocument || frame.contentWindow.document; }
        catch (error) { return null; }
    }
    function applyEditorA4Layout(doc){
        if (!doc || !doc.head || !doc.body) return;
        let style = doc.getElementById('mou-editor-a4-preview-style');
        if (!style) {
            style = doc.createElement('style');
            style.id = 'mou-editor-a4-preview-style';
            style.textContent = `
                :root { --mou-editor-zoom: 100%; background:#e2e8f0 !important; min-width:0 !important; }
                body { display:flex !important; flex-direction:column !important; align-items:center !important; width:100% !important; min-width:0 !important; max-width:none !important; margin:0 !important; padding:18px 0 28px !important; gap:16px !important; background:#e2e8f0 !important; overflow-x:auto !important; zoom:var(--mou-editor-zoom) !important; }
                .page { box-sizing:border-box !important; width:210mm !important; max-width:calc(100vw - 36px) !important; min-height:297mm !important; height:auto !important; flex:0 0 auto !important; margin:0 auto 16px !important; background:#fff !important; border:1px solid #cbd5e1 !important; box-shadow:0 3px 16px rgba(15,23,42,.18) !important; overflow:visible !important; page-break-after:auto !important; }
                .page.mou-editor-active-page { outline:2px solid #93c5fd !important; outline-offset:3px !important; }
                .mou-editor-page-break { display:block !important; height:0 !important; margin:12px 0 !important; border-top:2px dashed #64748b !important; position:relative !important; page-break-before:always !important; break-before:page !important; }
                .mou-editor-page-break:after { content:'PEMISAH HALAMAN' !important; display:inline-block !important; position:relative !important; top:-10px !important; padding:0 8px !important; background:#e2e8f0 !important; color:#475569 !important; font:700 10px Arial,sans-serif !important; letter-spacing:.08em !important; }
                .mou-editor-blank-content { box-sizing:border-box !important; min-height:220mm !important; padding:12mm 15mm !important; }
                .mou-editor-new-table { width:100% !important; border-collapse:collapse !important; table-layout:fixed !important; margin:8px 0 !important; }
                .mou-editor-new-table td, .mou-editor-new-table th { border:1px solid #94a3b8 !important; padding:6px !important; min-width:18px !important; vertical-align:top !important; }
                .mou-editor-new-table th { background:#f1f5f9 !important; font-weight:700 !important; }
                @media print { html,body{background:#fff!important} body{display:block!important;padding:0!important;gap:0!important;zoom:100%!important} .page{width:auto!important;max-width:none!important;min-height:0!important;margin:0!important;border:0!important;box-shadow:none!important;outline:0!important;page-break-after:always!important} }
            `;
            doc.head.appendChild(style);
        }
        return style;
    }
    function getPages(doc){
        if(!doc || !doc.body) return [];
        let pages = Array.from(doc.querySelectorAll('body > .page'));
        if(!pages.length) pages = Array.from(doc.querySelectorAll('body .page')).filter(page => !page.parentElement.closest('.page'));
        return pages;
    }
    function pageForNode(node){
        if(!node) return null;
        const element = node.nodeType === 1 ? node : node.parentElement;
        return element && element.closest ? element.closest('.page') : null;
    }
    function rememberSelection(){
        const doc=frameDocument();
        if(!doc || !doc.getSelection) return;
        const selection=doc.getSelection();
        if(selection && selection.rangeCount){
            const range=selection.getRangeAt(0);
            if(doc.body.contains(range.commonAncestorContainer)) savedRange=range.cloneRange();
        }
        updatePageFromSelection();
    }
    function restoreSelection(doc){
        if(!doc || !savedRange) return;
        try {
            if(!doc.body.contains(savedRange.commonAncestorContainer)) return;
            const selection=doc.getSelection();
            selection.removeAllRanges();
            selection.addRange(savedRange);
        } catch(error) {}
    }
    function currentDoc(shouldFocus=true){
        const doc=frameDocument();
        if(!ready || !doc){alert('Template belum siap. Tunggu sampai status Template siap diedit muncul.');return null;}
        doc.designMode='on';
        restoreSelection(doc);
        if(shouldFocus) frame.contentWindow.focus();
        return doc;
    }
    function cleanBodySnapshot(doc){
        const clone=doc.body.cloneNode(true);
        clone.querySelectorAll('.mou-editor-active-page').forEach(page=>page.classList.remove('mou-editor-active-page'));
        clone.querySelectorAll('[data-mou-editor-ui]').forEach(node=>node.remove());
        return clone.innerHTML;
    }
    function resetHistory(doc){
        if(historyTimer){clearTimeout(historyTimer);historyTimer=null;}
        historySnapshots=[cleanBodySnapshot(doc)];
        historyIndex=0;
        updateHistoryButtons();
    }
    function captureHistory(force=false){
        const doc=frameDocument();
        if(!doc || !ready || applyingHistory) return;
        if(historyTimer){clearTimeout(historyTimer);historyTimer=null;}
        const snapshot=cleanBodySnapshot(doc);
        if(!force && historyIndex>=0 && historySnapshots[historyIndex]===snapshot) return;
        if(historyIndex<historySnapshots.length-1) historySnapshots=historySnapshots.slice(0,historyIndex+1);
        historySnapshots.push(snapshot);
        historyIndex=historySnapshots.length-1;
        if(historySnapshots.length>maxHistory){historySnapshots.shift();historyIndex--;}
        updateHistoryButtons();
    }
    function scheduleHistory(){
        if(applyingHistory) return;
        if(historyTimer) clearTimeout(historyTimer);
        historyTimer=setTimeout(()=>captureHistory(false),450);
    }
    function flushHistory(){ captureHistory(false); }
    function updateHistoryButtons(){
        const undo=document.getElementById('editor-undo');
        const redo=document.getElementById('editor-redo');
        if(undo) undo.disabled=historyIndex<=0;
        if(redo) redo.disabled=historyIndex<0 || historyIndex>=historySnapshots.length-1;
    }
    function moveHistory(direction){
        flushHistory();
        const target=historyIndex+direction;
        if(target<0 || target>=historySnapshots.length) return;
        const doc=frameDocument(); if(!doc) return;
        applyingHistory=true;
        historyIndex=target;
        doc.body.innerHTML=historySnapshots[historyIndex];
        doc.designMode='on';
        applyEditorA4Layout(doc);
        savedRange=null;
        applyingHistory=false;
        updatePageList(doc, Math.min(activePageIndex, Math.max(0,getPages(doc).length-1)));
        updateHistoryButtons();
        status.textContent='Perubahan dipulihkan';
    }
    function updatePageList(doc, selectedIndex=activePageIndex){
        const pages=getPages(doc);
        activePageIndex=Math.max(0,Math.min(selectedIndex,Math.max(0,pages.length-1)));
        pages.forEach((page,index)=>page.classList.toggle('mou-editor-active-page',index===activePageIndex));
        if(pageSelect){
            pageSelect.innerHTML='';
            pages.forEach((page,index)=>{
                const option=doc.createElement('option');
                option.value=String(index);
                option.textContent='Halaman '+(index+1);
                pageSelect.appendChild(option);
            });
            if(!pages.length){const option=document.createElement('option');option.value='0';option.textContent='Tidak ada halaman';pageSelect.appendChild(option);}
            pageSelect.value=String(activePageIndex);
        }
        if(pageCount) pageCount.textContent='Halaman '+(pages.length?activePageIndex+1:0)+' dari '+pages.length;
        const up=document.getElementById('move-page-up');
        const down=document.getElementById('move-page-down');
        if(up) up.disabled=activePageIndex<=0;
        if(down) down.disabled=activePageIndex>=pages.length-1;
        const deleteButton=document.getElementById('delete-page');
        if(deleteButton) deleteButton.disabled=pages.length<=1;
    }
    function updatePageFromSelection(){
        const doc=frameDocument(); if(!doc || !ready) return;
        const selection=doc.getSelection();
        if(!selection || !selection.rangeCount) return;
        const page=pageForNode(selection.anchorNode);
        if(!page) return;
        const index=getPages(doc).indexOf(page);
        if(index>=0 && index!==activePageIndex) updatePageList(doc,index);
    }
    function updateVisiblePage(doc){
        if(!doc || !ready) return;
        const pages=getPages(doc); if(!pages.length) return;
        let bestIndex=activePageIndex; let bestDistance=Infinity;
        pages.forEach((page,index)=>{
            const rect=page.getBoundingClientRect();
            const distance=rect.top>=0?rect.top:Math.abs(rect.top)+rect.height;
            if(rect.bottom>0 && rect.top<doc.defaultView.innerHeight && distance<bestDistance){bestDistance=distance;bestIndex=index;}
        });
        if(bestIndex!==activePageIndex) updatePageList(doc,bestIndex);
    }
    function choosePage(index, scroll=true){
        const doc=currentDoc(false); if(!doc)return;
        const pages=getPages(doc);
        if(index<0 || index>=pages.length)return;
        activePageIndex=index;
        updatePageList(doc,index);
        if(scroll) pages[index].scrollIntoView({behavior:'smooth',block:'start'});
        status.textContent='Halaman '+(index+1)+' dipilih';
    }
    function markReady(){
        const doc=frameDocument();
        if(!doc || !doc.body || !doc.documentElement){status.textContent='Template gagal dimuat';return;}
        applyEditorA4Layout(doc);
        doc.designMode='on';
        doc.addEventListener('input',scheduleHistory,true);
        doc.addEventListener('keyup',rememberSelection,true);
        doc.addEventListener('mouseup',rememberSelection,true);
        doc.addEventListener('selectionchange',rememberSelection,true);
        doc.defaultView.addEventListener('scroll',()=>updateVisiblePage(doc),{passive:true});
        doc.addEventListener('keydown',handleShortcuts);
        ready=true;
        activePageIndex=0;
        updatePageList(doc,0);
        resetHistory(doc);
        status.textContent='Template siap diedit';
    }
    frame.addEventListener('load',markReady);
    if(savedBase64){frame.srcdoc=decodeBase64Utf8(savedBase64);}else{frame.src=sourceUrl;}

    function htmlEscape(value){return String(value).replace(/[&<>"']/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));}
    function beginStructureChange(doc){flushHistory();}
    function endStructureChange(doc,message){
        applyEditorA4Layout(doc);
        updatePageList(doc,activePageIndex);
        captureHistory(true);
        rememberSelection();
        if(message) status.textContent=message;
    }
    function command(doc,name,value=null){
        restoreSelection(doc);
        try{doc.execCommand('styleWithCSS',false,true);}catch(error){}
        doc.execCommand(name,false,value);
        rememberSelection();
        scheduleHistory();
    }
    function activePage(doc){
        const pages=getPages(doc);
        activePageIndex=Math.max(0,Math.min(activePageIndex,Math.max(0,pages.length-1)));
        return pages[activePageIndex] || null;
    }
    function activeCell(doc){
        const selection=doc.getSelection();
        if(!selection || !selection.rangeCount)return null;
        const node=selection.anchorNode;
        const el=node && node.nodeType===1?node:node && node.parentElement;
        return el && el.closest ? el.closest('td,th') : null;
    }
    function maybeConfirmTemplateTable(table,action){
        if(!table.classList.contains('mou-editor-new-table')){
            return confirm('Tabel ini berasal dari template MoU. '+action+' dapat mengubah layout tabel asli. Tetap lanjutkan?');
        }
        return true;
    }
    document.querySelectorAll('.toolbar').forEach(button=>button.addEventListener('mousedown',function(event){event.preventDefault();rememberSelection();}));
    document.querySelectorAll('[data-command]').forEach(button=>button.addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        if(button.dataset.command==='undo'){moveHistory(-1);return;}
        if(button.dataset.command==='redo'){moveHistory(1);return;}
        command(doc,button.dataset.command);
    }));
    document.getElementById('editor-undo').addEventListener('click',()=>moveHistory(-1));
    document.getElementById('editor-redo').addEventListener('click',()=>moveHistory(1));
    document.getElementById('format-block').addEventListener('change',function(){const doc=currentDoc();if(doc)command(doc,'formatBlock',this.value);});
    document.getElementById('font-family').addEventListener('change',function(){const doc=currentDoc();if(doc)command(doc,'fontName',this.value);});
    document.getElementById('font-size').addEventListener('change',function(){const doc=currentDoc();if(doc)command(doc,'fontSize',this.value);});
    document.getElementById('text-color').addEventListener('input',function(){const doc=currentDoc();if(doc)command(doc,'foreColor',this.value);});
    document.getElementById('highlight-color').addEventListener('input',function(){const doc=currentDoc();if(doc)command(doc,'hiliteColor',this.value);});
    document.getElementById('insert-paragraph').addEventListener('click',function(){const doc=currentDoc();if(!doc)return;command(doc,'insertHTML','<p>Isi paragraf baru.</p>');});
    document.getElementById('insert-pasal').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        const title=prompt('Judul Pasal atau ketentuan baru:','PASAL X');if(title===null)return;
        const body=prompt('Isi Pasal baru:','Tuliskan isi ketentuan di sini.');if(body===null)return;
        const html='<p style="margin-top:10px"><strong>'+htmlEscape(title).replace(/\n/g,'<br>')+'</strong></p><p>'+htmlEscape(body).replace(/\n/g,'<br>')+'</p>';
        command(doc,'insertHTML',html);
    });
    document.getElementById('insert-link').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        let url=prompt('Masukkan URL https://, http://, atau mailto:');if(url===null || !url.trim())return;
        url=url.trim();
        if(!/^(https?:\/\/|mailto:)/i.test(url)){alert('URL harus dimulai dengan https://, http://, atau mailto:.');return;}
        restoreSelection(doc);
        const selection=doc.getSelection();
        if(selection && selection.toString().trim()) command(doc,'createLink',url);
        else {const label=prompt('Teks link:','Buka tautan');if(label===null)return;command(doc,'insertHTML','<a href="'+htmlEscape(url)+'">'+htmlEscape(label)+'</a>');}
    });
    document.getElementById('insert-page-break').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        command(doc,'insertHTML','<div class="mou-editor-page-break" style="page-break-before:always;break-before:page;height:1px;"></div><p></p>');
    });
    document.getElementById('insert-table').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        const rowsText=prompt('Jumlah baris tabel (1–12):','3');if(rowsText===null)return;
        const colsText=prompt('Jumlah kolom tabel (1–8):','3');if(colsText===null)return;
        const rows=parseInt(rowsText,10),cols=parseInt(colsText,10);
        if(!Number.isInteger(rows)||!Number.isInteger(cols)||rows<1||rows>12||cols<1||cols>8){alert('Jumlah baris 1–12 dan kolom 1–8.');return;}
        let html='<table class="mou-editor-new-table" style="width:100%;border-collapse:collapse;table-layout:fixed;margin:8px 0">';
        for(let r=0;r<rows;r++){html+='<tr>';for(let c=0;c<cols;c++){const tag=r===0?'th':'td';html+='<'+tag+' style="border:1px solid #94a3b8;padding:6px;vertical-align:top">'+(r===0?'Judul':'Isi')+'</'+tag+'>';}html+='</tr>';}
        html+='</table><p></p>';
        command(doc,'insertHTML',html);
    });
    pageSelect.addEventListener('change',function(){choosePage(parseInt(this.value,10)||0);});
    document.getElementById('zoom-level').addEventListener('change',function(){
        const doc=currentDoc(false);if(!doc)return;
        const style=doc.getElementById('mou-editor-a4-preview-style');
        if(style)style.textContent=style.textContent.replace(/--mou-editor-zoom:\s*[^;]+;/,'--mou-editor-zoom: '+this.value+'%;');
        status.textContent='Zoom '+this.value+'%';
    });
    document.getElementById('add-blank-page').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        const pages=getPages(doc);if(!pages.length){alert('Tidak ada halaman untuk dijadikan dasar.');return;}
        beginStructureChange(doc);
        const source=activePage(doc)||pages[pages.length-1];
        const page=source.cloneNode(true);
        page.classList.remove('mou-editor-active-page');
        Array.from(page.children).forEach(child=>{if(!child.matches('.header,.document-header,header,.footer,.page-footer'))child.remove();});
        const content=doc.createElement('div');
        content.className='mou-editor-blank-content';
        content.style.cssText='box-sizing:border-box;min-height:220mm;padding:12mm 15mm;';
        content.innerHTML='<p>Klik di sini untuk mulai menulis halaman baru.</p>';
        page.appendChild(content);
        source.parentNode.insertBefore(page,source.nextSibling);
        activePageIndex=getPages(doc).indexOf(page);
        endStructureChange(doc,'Halaman kosong ditambahkan');
        page.scrollIntoView({behavior:'smooth',block:'start'});
    });
    document.getElementById('duplicate-page').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        const pages=getPages(doc),page=activePage(doc);if(!page)return;
        if(!confirm('Duplikat halaman ini beserta seluruh isinya? Teks dan tabel akan ikut disalin.'))return;
        beginStructureChange(doc);
        const clone=page.cloneNode(true);clone.classList.remove('mou-editor-active-page');
        page.parentNode.insertBefore(clone,page.nextSibling);
        activePageIndex=getPages(doc).indexOf(clone);
        endStructureChange(doc,'Halaman berhasil diduplikasi');
        clone.scrollIntoView({behavior:'smooth',block:'start'});
    });
    document.getElementById('delete-page').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;
        const pages=getPages(doc),page=activePage(doc);if(!page)return;
        if(pages.length<=1){alert('Dokumen harus memiliki setidaknya satu halaman.');return;}
        if(!confirm('Hapus halaman '+(activePageIndex+1)+' dari '+pages.length+'? SEMUA teks hukum, tabel, header, dan footer di halaman ini akan dihapus. Operasi bisa dipulihkan dengan Undo sebelum meninggalkan editor.'))return;
        beginStructureChange(doc);
        const oldIndex=activePageIndex;page.remove();
        activePageIndex=Math.min(oldIndex,getPages(doc).length-1);
        endStructureChange(doc,'Halaman dihapus — gunakan Undo jika tidak sengaja');
        const newPage=getPages(doc)[activePageIndex];if(newPage)newPage.scrollIntoView({behavior:'smooth',block:'start'});
    });
    document.getElementById('move-page-up').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const pages=getPages(doc),page=activePage(doc);if(!page||activePageIndex<=0)return;
        if(!confirm('Pindahkan halaman ini sebelum halaman sebelumnya? Urutan isi dokumen akan berubah.'))return;
        beginStructureChange(doc);page.parentNode.insertBefore(page,pages[activePageIndex-1]);
        activePageIndex=activePageIndex-1;endStructureChange(doc,'Halaman dipindahkan ke atas');page.scrollIntoView({behavior:'smooth',block:'start'});
    });
    document.getElementById('move-page-down').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const pages=getPages(doc),page=activePage(doc);if(!page||activePageIndex>=pages.length-1)return;
        if(!confirm('Pindahkan halaman ini setelah halaman berikutnya? Urutan isi dokumen akan berubah.'))return;
        beginStructureChange(doc);page.parentNode.insertBefore(pages[activePageIndex+1],page);
        activePageIndex=activePageIndex+1;endStructureChange(doc,'Halaman dipindahkan ke bawah');page.scrollIntoView({behavior:'smooth',block:'start'});
    });
    document.getElementById('table-add-row').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const cell=activeCell(doc);if(!cell){alert('Klik salah satu sel tabel terlebih dahulu.');return;}
        const table=cell.closest('table');if(!maybeConfirmTemplateTable(table,'Menambah baris'))return;
        const row=cell.closest('tr');if(!row)return;
        beginStructureChange(doc);const clone=row.cloneNode(true);
        Array.from(clone.cells).forEach(c=>{c.innerHTML='&nbsp;';});
        row.parentNode.insertBefore(clone,row.nextSibling);endStructureChange(doc,'Baris tabel ditambahkan');
    });
    document.getElementById('table-delete-row').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const cell=activeCell(doc);if(!cell){alert('Klik salah satu sel tabel terlebih dahulu.');return;}
        const table=cell.closest('table');if(!maybeConfirmTemplateTable(table,'Menghapus baris'))return;
        const row=cell.closest('tr');if(!row)return;
        if(row.parentElement.querySelectorAll(':scope > tr').length<=1 && table.rows.length<=1){alert('Tabel harus menyisakan sedikitnya satu baris.');return;}
        if(!confirm('Hapus seluruh baris tabel yang sedang dipilih?'))return;
        beginStructureChange(doc);row.remove();endStructureChange(doc,'Baris tabel dihapus');
    });
    document.getElementById('table-add-column').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const cell=activeCell(doc);if(!cell){alert('Klik salah satu sel tabel terlebih dahulu.');return;}
        const table=cell.closest('table');if(!maybeConfirmTemplateTable(table,'Menambah kolom'))return;
        const rows=Array.from(table.rows);if(rows.some(row=>Array.from(row.cells).some(c=>c.colSpan>1||c.rowSpan>1))){alert('Tabel ini memakai sel gabungan (colspan/rowspan), jadi penambahan kolom otomatis dihentikan agar struktur tidak rusak.');return;}
        const columnIndex=cell.cellIndex+1;beginStructureChange(doc);
        rows.forEach(row=>{const tag=row.cells[0]&&row.cells[0].tagName==='TH'?'TH':'TD';const newCell=doc.createElement(tag);newCell.innerHTML='&nbsp;';newCell.style.cssText='border:1px solid #94a3b8;padding:6px;vertical-align:top';if(columnIndex>=row.cells.length)row.appendChild(newCell);else row.insertBefore(newCell,row.cells[columnIndex]);});
        endStructureChange(doc,'Kolom tabel ditambahkan');
    });
    document.getElementById('table-delete-column').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const cell=activeCell(doc);if(!cell){alert('Klik salah satu sel tabel terlebih dahulu.');return;}
        const table=cell.closest('table');if(!maybeConfirmTemplateTable(table,'Menghapus kolom'))return;
        const rows=Array.from(table.rows);if(rows.some(row=>Array.from(row.cells).some(c=>c.colSpan>1||c.rowSpan>1))){alert('Tabel ini memakai sel gabungan (colspan/rowspan), jadi penghapusan kolom otomatis dihentikan agar struktur tidak rusak.');return;}
        const columnIndex=cell.cellIndex;if(rows.some(row=>row.cells.length<=1)){alert('Tabel harus menyisakan sedikitnya satu kolom.');return;}
        if(!confirm('Hapus kolom '+(columnIndex+1)+' pada tabel ini?'))return;
        beginStructureChange(doc);rows.forEach(row=>{if(row.cells[columnIndex])row.deleteCell(columnIndex);});endStructureChange(doc,'Kolom tabel dihapus');
    });
    document.getElementById('table-delete').addEventListener('click',function(){
        const doc=currentDoc();if(!doc)return;const cell=activeCell(doc);if(!cell){alert('Klik salah satu sel tabel terlebih dahulu.');return;}
        const table=cell.closest('table');if(!maybeConfirmTemplateTable(table,'Menghapus tabel'))return;
        if(!confirm('Hapus seluruh tabel yang memuat sel terpilih?'))return;
        beginStructureChange(doc);table.remove();endStructureChange(doc,'Tabel dihapus');
    });
    document.getElementById('reload-legacy').addEventListener('click',function(){
        if(!confirm('Muat ulang isi template asli? Semua perubahan yang belum disimpan akan hilang.'))return;
        ready=false;status.textContent='Memuat template asli…';savedRange=null;historySnapshots=[];historyIndex=-1;
        if(savedBase64){frame.srcdoc=decodeBase64Utf8(savedBase64);}else{frame.src=sourceUrl;}
    });
    function serializeEditor(){
        const doc=currentDoc();if(!doc)return false;
        flushHistory();
        const clone=doc.documentElement.cloneNode(true);
        const editorOnlyStyle=clone.querySelector('#mou-editor-a4-preview-style');if(editorOnlyStyle)editorOnlyStyle.remove();
        clone.querySelectorAll('.mou-editor-active-page').forEach(page=>page.classList.remove('mou-editor-active-page'));
        clone.querySelectorAll('[data-mou-editor-ui]').forEach(node=>node.remove());
        let html='<!DOCTYPE html>\n'+clone.outerHTML;
        const baseUrl=new URL('/',new URL(sourceUrl,window.location.href));
        const safeBase=htmlEscape(baseUrl.href);
        if(!/<base\b/i.test(html)){html=html.replace(/<head\b[^>]*>/i,headTag=>headTag+'<base href="'+safeBase+'">');}
        document.getElementById('save-html').value=html;
        document.getElementById('preview-html').value=html;
        document.getElementById('save-note').value=document.getElementById('change-note').value;
        return true;
    }
    document.getElementById('save-form').addEventListener('submit',function(event){if(!serializeEditor())event.preventDefault();});
    document.getElementById('preview-form').addEventListener('submit',function(event){if(!serializeEditor())event.preventDefault();});
    function handleShortcuts(event){
        const platform=event.metaKey||event.ctrlKey;
        if(!platform)return;
        const key=event.key.toLowerCase();
        if(key==='z'&&!event.shiftKey){event.preventDefault();moveHistory(-1);}
        else if(key==='y'||(key==='z'&&event.shiftKey)){event.preventDefault();moveHistory(1);}
        else if(key==='s'){event.preventDefault();document.getElementById('save-form').requestSubmit();}
    }
    document.addEventListener('keydown',function(event){
        if(event.target && event.target.closest && event.target.closest('input,textarea,select,[contenteditable="true"]')) return;
        handleShortcuts(event);
    });
    document.querySelectorAll('.toolbar-select,.toolbar-color input').forEach(control=>control.addEventListener('mousedown',rememberSelection));
})();
</script>
</x-app-layout>
