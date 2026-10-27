<x-app-layout>
<div class="min-h-screen bg-slate-100 px-3 py-6 sm:px-6">
    <div class="mx-auto max-w-7xl space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <a href="{{ route('admin.mou-editor.index', ['order_id' => $order->id]) }}" class="text-sm font-bold text-indigo-600">← Daftar MoU</a>
            <h1 class="mt-2 text-2xl font-black text-slate-950">Edit View Signed MoU</h1>
            <p class="mt-1 text-sm text-slate-600">Order {{ $order->order_number }} · {{ strtoupper(str_replace(['_', '-'], ' ', $document->mou_type)) }} · Dokumen #{{ $document->id }}</p>
        </div>

        @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><strong>Proses belum berhasil:</strong><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm leading-6 text-amber-950">
            <strong>Editor koreksi PDF</strong>
            <p class="mt-1">PDF signed ini tidak mempunyai snapshot HTML yang dapat diedit. Gunakan alat berikut untuk menutup teks lama dengan kotak putih, menambahkan teks pengganti, atau menambahkan keterangan. Halaman, gambar, dan tanda tangan pada PDF asli tetap dipertahankan di bawah koreksi.</p>
            <p class="mt-2 text-xs">Ini merupakan koreksi visual pada PDF, bukan pengeditan teks sumber. Jika perubahan mengubah isi perjanjian, mintalah persetujuan dan tanda tangan ulang sesuai kebutuhan. Jangan menghapus atau memindahkan tanda tangan pihak lain tanpa persetujuan.</p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-black uppercase tracking-wide text-slate-500">Alat</span>
                <button type="button" class="pdf-tool active" data-tool="select">Pilih / Geser</button>
                <button type="button" class="pdf-tool" data-tool="text">+ Tambah Teks</button>
                <button type="button" class="pdf-tool" data-tool="whiteout">Tutup Teks Lama</button>
                <button type="button" id="pdf-undo" class="pdf-tool">Undo</button>
                <button type="button" id="pdf-delete" class="pdf-tool pdf-tool-danger">Hapus Pilihan</button>
                <button type="button" id="pdf-clear" class="pdf-tool pdf-tool-danger">Hapus Semua Koreksi</button>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-3">
                <label class="text-xs font-bold text-slate-600">Font
                    <select id="pdf-font-family" class="pdf-control"><option value="TimesRoman">Times New Roman</option><option value="Helvetica">Arial / Helvetica</option><option value="Courier">Courier</option></select>
                </label>
                <label class="text-xs font-bold text-slate-600">Ukuran
                    <select id="pdf-font-size" class="pdf-control"><option>8</option><option>9</option><option>10</option><option selected>12</option><option>14</option><option>16</option><option>18</option><option>20</option><option>24</option><option>28</option><option>36</option></select>
                </label>
                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600"><input id="pdf-bold" type="checkbox" class="rounded border-slate-300"> Bold</label>
                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600"><input id="pdf-italic" type="checkbox" class="rounded border-slate-300"> Italic</label>
                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600"><input id="pdf-underline" type="checkbox" class="rounded border-slate-300"> Garis bawah</label>
                <label class="text-xs font-bold text-slate-600">Warna <input id="pdf-text-color" type="color" value="#111111" class="ml-1 h-8 w-10 rounded border border-slate-300 bg-white p-1 align-middle"></label>
                <label class="text-xs font-bold text-slate-600">Rata
                    <select id="pdf-align" class="pdf-control"><option value="left">Kiri</option><option value="center">Tengah</option><option value="right">Kanan</option></select>
                </label>
                <label class="text-xs font-bold text-slate-600">Lebar kotak
                    <select id="pdf-box-width" class="pdf-control"><option value="120">Kecil</option><option value="180" selected>Sedang</option><option value="260">Besar</option><option value="360">Lebar</option></select>
                </label>
                <label class="text-xs font-bold text-slate-600">Tinggi kotak
                    <select id="pdf-box-height" class="pdf-control"><option value="24">Pendek</option><option value="36" selected>Normal</option><option value="54">Tinggi</option><option value="80">Sangat tinggi</option></select>
                </label>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-500">Cara pakai: pilih <strong>Tutup Teks Lama</strong>, lalu klik area teks yang ingin ditutup. Setelah itu pilih <strong>Tambah Teks</strong> dan klik lokasi teks pengganti. Klik teks koreksi dua kali untuk mengetik. Gunakan gagang kecil di atas koreksi untuk memindahkannya.</p>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-300 bg-slate-200 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-300 bg-white p-3">
                <p id="pdf-status" class="text-sm font-bold text-slate-700">Memuat PDF dan alat edit…</p>
                @if($signedPdfSourceUrl)<a href="{{ $signedPdfSourceUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700">Buka PDF Asli ↗</a>@endif
            </div>
            @if($signedPdfSourceUrl)
                <div id="pdf-editor-pages" class="space-y-5 overflow-x-auto p-4"></div>
                <div id="pdf-fallback" class="hidden m-4 rounded-xl border border-rose-200 bg-white p-4 text-sm leading-6 text-rose-800">Alat edit PDF gagal dimuat atau file tidak dapat diedit oleh browser. Kamu masih dapat membuka PDF asli dan menggunakan formulir unggah PDF koreksi di bawah.</div>
            @else
                <div class="p-8 text-center text-sm text-rose-700">Tidak ditemukan PDF aktif maupun PDF arsip di storage.</div>
            @endif
        </section>

        @if($signedPdfSourceUrl)
        <section class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Simpan hasil koreksi sebagai View Signed</h2>
            <p class="mt-1 text-sm leading-6 text-slate-600">Hasil edit dibuat menjadi PDF baru. Sistem mengarsipkan sumber sebelumnya dan mengubah tautan View Signed ke PDF baru. Periksa seluruh halaman sebelum menyimpan.</p>
            <form id="pdf-save-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.mou-editor.reissue-signed-upload', [$order->id, $document->id]) }}" class="mt-4 space-y-4">
                @csrf
                <input id="corrected-pdf" type="file" name="corrected_pdf" accept="application/pdf,.pdf" required class="hidden">
                <div><label for="change-note-upload" class="mb-1 block text-xs font-bold text-slate-700">Alasan koreksi</label><input id="change-note-upload" type="text" name="change_note_upload" maxlength="500" required placeholder="Contoh: Koreksi nomor dan format dokumen" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                <label class="flex items-start gap-2 text-xs leading-5 text-slate-600"><input type="checkbox" name="confirm_visual_reissue_upload" value="1" required class="mt-1 rounded border-slate-400"><span>Saya memastikan koreksi telah disetujui dan memahami bahwa perubahan isi substantif dapat memerlukan tanda tangan ulang.</span></label>
                <button id="pdf-save-button" type="submit" class="rounded-xl bg-amber-800 px-5 py-3 text-sm font-extrabold text-white hover:bg-amber-900">Buat PDF Koreksi & Perbarui View Signed</button>
            </form>
        </section>
        @endif

        @if($signedHistory->isNotEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black text-slate-950">Arsip PDF Signed Sebelumnya</h2><p class="mt-1 text-sm text-slate-500">PDF lama tetap disimpan. Tautan di bawah hanya membuka arsip.</p><div class="mt-4 divide-y divide-slate-100">@foreach($signedHistory as $signedVersion)<div class="flex flex-col justify-between gap-3 py-3 sm:flex-row sm:items-center"><div><p class="break-all text-sm font-bold text-slate-800">{{ basename($signedVersion->signed_file_path) }}</p><p class="mt-1 text-xs text-slate-500">{{ $signedVersion->mou_number ?: 'Nomor MoU tidak tercatat' }} · {{ $signedVersion->superseded_at }}</p><p class="mt-1 text-xs text-slate-500">{{ $signedVersion->reason ?: 'Tanpa catatan' }}</p></div>@if(\Illuminate\Support\Facades\Storage::disk('public')->exists($signedVersion->signed_file_path))<a target="_blank" rel="noopener noreferrer" href="{{ asset('storage/' . $signedVersion->signed_file_path) }}" class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">Buka PDF Arsip ↗</a>@else<span class="text-xs font-bold text-rose-700">File tidak ditemukan</span>@endif</div>@endforeach</div></section>
        @endif
    </div>
</div>

<style>
.pdf-tool{border:1px solid #cbd5e1;border-radius:.65rem;padding:.5rem .72rem;font-size:.75rem;font-weight:800;color:#334155;background:#fff;white-space:nowrap}.pdf-tool:hover{background:#f8fafc}.pdf-tool.active{border-color:#6366f1;background:#eef2ff;color:#3730a3}.pdf-tool-danger{border-color:#fecaca;color:#991b1b}.pdf-control{display:block;margin-top:.25rem;border:1px solid #cbd5e1;border-radius:.5rem;padding:.4rem .55rem;background:#fff;color:#334155;font-size:.75rem;font-weight:700}.pdf-page-shell{position:relative;width:max-content;max-width:100%;margin:0 auto;background:#fff;box-shadow:0 3px 18px rgba(15,23,42,.18)}.pdf-page-title{margin:0 auto 6px;max-width:850px;font-size:11px;font-weight:800;color:#475569}.pdf-page-canvas{display:block;max-width:none}.pdf-overlay-layer{position:absolute;inset:0;pointer-events:none}.pdf-edit-item{position:absolute;pointer-events:auto;box-sizing:border-box;border:1px dashed #6366f1;background:rgba(255,255,255,.7);color:#111;min-width:72px;min-height:24px;padding:0;white-space:pre-wrap;overflow:visible}.pdf-edit-item.selected{outline:2px solid #4f46e5;outline-offset:1px}.pdf-edit-handle{position:absolute;top:-18px;left:-1px;height:17px;min-width:24px;padding:1px 5px;background:#4f46e5;color:#fff;font:700 10px Arial,sans-serif;cursor:move;border-radius:4px 4px 0 0;white-space:nowrap}.pdf-edit-remove{position:absolute;top:-18px;right:-1px;height:17px;padding:0 5px;border:0;background:#b91c1c;color:#fff;font:700 11px Arial,sans-serif;cursor:pointer;border-radius:4px 4px 0 0}.pdf-edit-content{min-height:18px;outline:none;box-sizing:border-box;width:100%;height:100%;padding:2px;overflow-wrap:anywhere;white-space:pre-wrap}.pdf-whiteout{background:#fff;border:1px dashed #dc2626}.pdf-whiteout:after{content:'TUTUP TEKS';color:#dc2626;font:700 10px Arial,sans-serif;letter-spacing:.05em;position:absolute;left:3px;top:3px;opacity:.8;pointer-events:none}.pdf-whiteout.selected:after{content:'KOTAK PUTIH'}
</style>

@if($signedPdfSourceUrl)
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function(){
    const pdfUrl = @json($signedPdfSourceUrl);
    const status = document.getElementById('pdf-status');
    const pagesHost = document.getElementById('pdf-editor-pages');
    const fallback = document.getElementById('pdf-fallback');
    const saveForm = document.getElementById('pdf-save-form');
    const fileInput = document.getElementById('corrected-pdf');
    const saveButton = document.getElementById('pdf-save-button');
    const SIZE = 1.25;
    let pdfBytes = null;
    let currentTool = 'select';
    let pageStates = [];
    let selected = null;
    let isGenerating = false;
    let bypassSubmit = false;
    let dragInfo = null;

    function setStatus(message){ if(status) status.textContent = message; }
    function setting(id){ return document.getElementById(id); }
    function chosenNumber(id, fallbackValue){ const v = parseFloat(setting(id).value); return Number.isFinite(v) ? v : fallbackValue; }
    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

    document.querySelectorAll('[data-tool]').forEach(button=>button.addEventListener('click',()=>{
        currentTool = button.dataset.tool;
        document.querySelectorAll('[data-tool]').forEach(item=>item.classList.toggle('active', item===button));
        setStatus(currentTool==='text' ? 'Mode Tambah Teks: klik halaman untuk menaruh teks.' : currentTool==='whiteout' ? 'Mode Tutup Teks Lama: klik area yang ingin ditutup dengan kotak putih.' : 'Mode Pilih / Geser: klik koreksi yang ada untuk dipilih atau dipindahkan.');
    }));

    function deselect(){
        if(selected && selected.dom) selected.dom.classList.remove('selected');
        selected = null;
        syncToolbar();
    }
    function syncToolbar(){
        if(!selected || selected.type!=='text') return;
        setting('pdf-font-family').value = selected.fontFamily || 'TimesRoman';
        setting('pdf-font-size').value = String(selected.fontSize || 12);
        setting('pdf-text-color').value = selected.color || '#111111';
        setting('pdf-bold').checked = !!selected.bold;
        setting('pdf-italic').checked = !!selected.italic;
        setting('pdf-underline').checked = !!selected.underline;
        setting('pdf-align').value = selected.align || 'left';
    }
    function selectItem(item){
        if(selected && selected.dom) selected.dom.classList.remove('selected');
        selected = item;
        if(item && item.dom) item.dom.classList.add('selected');
        syncToolbar();
    }
    function updateItemVisual(item){
        const el = item.dom;
        el.style.left = item.x + 'px';
        el.style.top = item.y + 'px';
        el.style.width = item.width + 'px';
        el.style.height = item.height + 'px';
        if(item.type==='whiteout'){
            el.classList.add('pdf-whiteout');
            el.style.backgroundColor = '#fff';
            el.style.color = '#b91c1c';
        } else {
            el.classList.remove('pdf-whiteout');
            el.style.backgroundColor = 'rgba(255,255,255,.82)';
            el.style.color = item.color || '#111111';
            el.style.fontFamily = item.fontFamily==='Helvetica' ? 'Arial, sans-serif' : item.fontFamily==='Courier' ? 'Courier New, monospace' : 'Times New Roman, serif';
            el.style.fontSize = ((item.fontSize || 12) * SIZE) + 'px';
            el.style.fontWeight = item.bold ? '700' : '400';
            el.style.fontStyle = item.italic ? 'italic' : 'normal';
            el.style.textAlign = item.align || 'left';
            const content = el.querySelector('.pdf-edit-content');
            if(content){ content.style.textDecoration = item.underline ? 'underline' : 'none'; content.style.textAlign = item.align || 'left'; }
        }
    }
    function createItem(pageState, type, x, y){
        const item = {type, pageIndex:pageState.index, x:Math.max(0,x), y:Math.max(0,y), width:type==='whiteout'?chosenNumber('pdf-box-width',180):chosenNumber('pdf-box-width',180), height:type==='whiteout'?chosenNumber('pdf-box-height',36):chosenNumber('pdf-box-height',36), text:type==='text'?'Tulis teks di sini':'', fontFamily:setting('pdf-font-family').value, fontSize:chosenNumber('pdf-font-size',12), bold:setting('pdf-bold').checked, italic:setting('pdf-italic').checked, underline:setting('pdf-underline').checked, color:setting('pdf-text-color').value, align:setting('pdf-align').value};
        const el = document.createElement('div');
        el.className='pdf-edit-item' + (type==='whiteout'?' pdf-whiteout':'');
        el.tabIndex=0;
        const handle = document.createElement('button');
        handle.type='button'; handle.className='pdf-edit-handle'; handle.textContent=type==='whiteout'?'Kotak':'Geser';
        handle.addEventListener('pointerdown', event=>{
            event.preventDefault(); event.stopPropagation(); selectItem(item);
            const rect=pageState.layer.getBoundingClientRect();
            dragInfo={item, startX:event.clientX, startY:event.clientY, baseX:item.x, baseY:item.y, rect};
            try{handle.setPointerCapture(event.pointerId);}catch(e){}
        });
        const remove = document.createElement('button');
        remove.type='button'; remove.className='pdf-edit-remove'; remove.textContent='×'; remove.title='Hapus koreksi';
        remove.addEventListener('click',event=>{event.stopPropagation(); removeItem(item);});
        const content = document.createElement(type==='text'?'div':'div');
        content.className='pdf-edit-content';
        if(type==='text'){
            content.contentEditable='true'; content.spellcheck=false; content.textContent=item.text;
            content.addEventListener('input',()=>{item.text=content.innerText.replace(/\r/g,'');});
            content.addEventListener('focus',()=>selectItem(item));
            content.addEventListener('dblclick',()=>selectItem(item));
        }
        el.append(handle,remove,content);
        el.addEventListener('pointerdown',event=>{if(event.target===handle||event.target===remove)return;selectItem(item);});
        el.addEventListener('click',event=>{event.stopPropagation();selectItem(item);});
        item.dom=el; item.content=content;
        pageState.layer.appendChild(el);
        pageState.edits.push(item);
        updateItemVisual(item); selectItem(item);
        if(type==='text'){
            content.focus();
            if(document.createRange && window.getSelection){const range=document.createRange();range.selectNodeContents(content);range.collapse(false);const sel=window.getSelection();sel.removeAllRanges();sel.addRange(range);}
        }
        return item;
    }
    function removeItem(item){
        const pageState=pageStates[item.pageIndex];
        if(!pageState)return;
        pageState.edits=pageState.edits.filter(entry=>entry!==item);
        if(item.dom)item.dom.remove();
        if(selected===item)selected=null;
        syncToolbar();
        setStatus('Koreksi dihapus.');
    }

    document.addEventListener('pointermove',event=>{
        if(!dragInfo)return;
        const info=dragInfo;
        const scaleX=info.rect.width / pageStates[info.item.pageIndex].cssWidth;
        const scaleY=info.rect.height / pageStates[info.item.pageIndex].cssHeight;
        info.item.x=Math.max(0,info.baseX+(event.clientX-info.startX)/scaleX);
        info.item.y=Math.max(0,info.baseY+(event.clientY-info.startY)/scaleY);
        updateItemVisual(info.item);
    });
    document.addEventListener('pointerup',()=>{if(dragInfo){dragInfo=null;setStatus('Posisi koreksi diperbarui.');}});

    ['pdf-font-family','pdf-font-size','pdf-bold','pdf-italic','pdf-underline','pdf-text-color','pdf-align'].forEach(id=>{
        setting(id).addEventListener('change',()=>{
            if(!selected || selected.type!=='text')return;
            selected.fontFamily=setting('pdf-font-family').value;
            selected.fontSize=chosenNumber('pdf-font-size',12);
            selected.bold=setting('pdf-bold').checked;
            selected.italic=setting('pdf-italic').checked;
            selected.underline=setting('pdf-underline').checked;
            selected.color=setting('pdf-text-color').value;
            selected.align=setting('pdf-align').value;
            updateItemVisual(selected);
        });
    });
    setting('pdf-delete').addEventListener('click',()=>{if(selected)removeItem(selected);});
    setting('pdf-undo').addEventListener('click',()=>{
        for(let i=pageStates.length-1;i>=0;i--){const entries=pageStates[i].edits;if(entries.length){removeItem(entries[entries.length-1]);setStatus('Koreksi terakhir dihapus.');return;}}
        setStatus('Belum ada koreksi untuk di-undo.');
    });
    setting('pdf-clear').addEventListener('click',()=>{
        if(!confirm('Hapus semua koreksi yang belum disimpan? PDF asli tidak berubah.'))return;
        pageStates.forEach(page=>{page.edits.forEach(item=>item.dom.remove());page.edits=[];});deselect();setStatus('Semua koreksi lokal dihapus.');
    });

    async function renderPdf(){
        if(!window.pdfjsLib || !window.PDFLib) throw new Error('Library PDF tidak berhasil dimuat. Periksa koneksi internet/browser.');
        window.pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const response=await fetch(pdfUrl,{credentials:'same-origin',cache:'no-store'});
        if(!response.ok)throw new Error('PDF tidak dapat dibaca (HTTP '+response.status+').');
        pdfBytes=new Uint8Array(await response.arrayBuffer());
        const task=window.pdfjsLib.getDocument({data:pdfBytes.slice()});
        const pdf=await task.promise;
        pagesHost.innerHTML=''; pageStates=[];
        for(let pageNo=1;pageNo<=pdf.numPages;pageNo++){
            const page=await pdf.getPage(pageNo);const viewport=page.getViewport({scale:SIZE});
            const title=document.createElement('div');title.className='pdf-page-title';title.textContent='Halaman '+pageNo+' dari '+pdf.numPages;pagesHost.appendChild(title);
            const shell=document.createElement('div');shell.className='pdf-page-shell';shell.style.width=viewport.width+'px';shell.style.height=viewport.height+'px';
            const canvas=document.createElement('canvas');canvas.className='pdf-page-canvas';canvas.width=Math.ceil(viewport.width);canvas.height=Math.ceil(viewport.height);canvas.style.width=viewport.width+'px';canvas.style.height=viewport.height+'px';
            const layer=document.createElement('div');layer.className='pdf-overlay-layer';layer.style.width=viewport.width+'px';layer.style.height=viewport.height+'px';
            shell.append(canvas,layer);pagesHost.appendChild(shell);
            const pageState={index:pageNo-1,pageNo,canvas,layer,shell,cssWidth:viewport.width,cssHeight:viewport.height,scale:SIZE,pdfWidth:page.view[2]-page.view[0],pdfHeight:page.view[3]-page.view[1],edits:[]};pageStates.push(pageState);
            await page.render({canvasContext:canvas.getContext('2d'),viewport}).promise;
            canvas.addEventListener('click',event=>{
                if(currentTool==='select') { deselect(); return; }
                const rect=canvas.getBoundingClientRect();
                const x=(event.clientX-rect.left)*(viewport.width/rect.width);
                const y=(event.clientY-rect.top)*(viewport.height/rect.height);
                if(currentTool==='text')createItem(pageState,'text',x,y);
                else if(currentTool==='whiteout')createItem(pageState,'whiteout',x,y);
                setStatus('Koreksi ditambahkan ke halaman '+pageNo+'.');
            });
        }
        setStatus('PDF siap diedit · '+pdf.numPages+' halaman · '+(pageStates.reduce((n,p)=>n+p.edits.length,0))+' koreksi.');
    }

    function hexRgb(hex){const s=hex.replace('#','');return {r:parseInt(s.slice(0,2),16)/255,g:parseInt(s.slice(2,4),16)/255,b:parseInt(s.slice(4,6),16)/255};}
    async function makeCorrectedPdf(){
        if(!pdfBytes)throw new Error('PDF sumber belum siap.');
        const {PDFDocument,StandardFonts,rgb}=window.PDFLib;
        const doc=await PDFDocument.load(pdfBytes.slice());
        const pageList=doc.getPages();
        const fontCache={};
        function fontName(item){
            const family=item.fontFamily||'TimesRoman';
            if(family==='Helvetica')return item.bold?(item.italic?'HelveticaBoldOblique':'HelveticaBold'):(item.italic?'HelveticaOblique':'Helvetica');
            if(family==='Courier')return item.bold?(item.italic?'CourierBoldOblique':'CourierBold'):(item.italic?'CourierOblique':'Courier');
            return item.bold?(item.italic?'TimesRomanBoldItalic':'TimesRomanBold'):(item.italic?'TimesRomanItalic':'TimesRoman');
        }
        async function fontFor(item){const name=fontName(item);if(!fontCache[name])fontCache[name]=await doc.embedFont(StandardFonts[name]);return fontCache[name];}
        // Apply all opaque whiteouts first. Then draw all replacement/additional text over them.
        for(const pageState of pageStates){
            const page=pageList[pageState.index];if(!page)continue;
            const sx=page.getWidth()/pageState.cssWidth;const sy=page.getHeight()/pageState.cssHeight;
            for(const item of pageState.edits){if(item.type!=='whiteout')continue;
                page.drawRectangle({x:item.x*sx,y:page.getHeight()-(item.y+item.height)*sy,width:item.width*sx,height:item.height*sy,color:rgb(1,1,1),borderColor:rgb(1,1,1),borderWidth:0});
            }
        }
        for(const pageState of pageStates){
            const page=pageList[pageState.index];if(!page)continue;
            const sx=page.getWidth()/pageState.cssWidth;const sy=page.getHeight()/pageState.cssHeight;
            for(const item of pageState.edits){if(item.type!=='text')continue;
                const text=String(item.content?item.content.innerText:item.text||'').replace(/\r/g,'').trimEnd();if(!text)continue;
                const font=await fontFor(item);const size=Number(item.fontSize)||12;const c=hexRgb(item.color||'#111111');
                const lines=text.split('\n');let top=item.y*sy;const xBase=item.x*sx;const maxWidth=Math.max(20,item.width*sx-4);
                lines.forEach((line,index)=>{
                    const width=font.widthOfTextAtSize(line,size);let x=xBase+2*sx;
                    if(item.align==='center')x=xBase+Math.max(0,(maxWidth-width)/2);
                    if(item.align==='right')x=xBase+Math.max(0,maxWidth-width-2*sx);
                    const y=page.getHeight()-top-(size+(index*(size*1.18)));
                    page.drawText(line,{x,y,size,font,color:rgb(c.r,c.g,c.b),maxWidth});
                    if(item.underline && line){const underlineY=y-1.5;page.drawLine({start:{x,y:underlineY},end:{x:x+Math.min(width,maxWidth),y:underlineY},thickness:.6,color:rgb(c.r,c.g,c.b)});}
                });
            }
        }
        return await doc.save();
    }

    if(saveForm){
        saveForm.addEventListener('submit',async event=>{
            if(bypassSubmit)return;
            event.preventDefault();
            if(isGenerating)return;
            if(!pdfBytes){alert('PDF sumber belum selesai dimuat.');return;}
            const total=pageStates.reduce((n,p)=>n+p.edits.length,0);
            if(!total){alert('Belum ada koreksi. Pilih Tambah Teks atau Tutup Teks Lama terlebih dahulu.');return;}
            if(!confirm('Buat PDF baru dengan '+total+' koreksi dan jadikan View Signed terbaru? PDF sumber lama akan disimpan di arsip.'))return;
            isGenerating=true;saveButton.disabled=true;saveButton.textContent='Membuat PDF…';setStatus('Membuat PDF koreksi…');
            try{
                const out=await makeCorrectedPdf();
                const file=new File([out],'Corrected_Signed_MoU.pdf',{type:'application/pdf'});
                const transfer=new DataTransfer();transfer.items.add(file);fileInput.files=transfer.files;
                if(!fileInput.files.length)throw new Error('Browser tidak mengizinkan menyiapkan file PDF otomatis. Gunakan ekspor manual atau browser terbaru.');
                bypassSubmit=true;saveForm.requestSubmit();
            }catch(error){console.error(error);alert('PDF koreksi gagal dibuat: '+(error&&error.message?error.message:'Kesalahan tidak diketahui'));setStatus('Gagal membuat PDF. PDF sumber tidak berubah.');isGenerating=false;saveButton.disabled=false;saveButton.textContent='Buat PDF Koreksi & Perbarui View Signed';}
        });
    }

    renderPdf().catch(error=>{console.error(error);if(fallback)fallback.classList.remove('hidden');setStatus('PDF Editor tidak siap. '+(error.message||''));});
})();
</script>
@endif
</x-app-layout>
