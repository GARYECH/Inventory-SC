<x-app-layout>
<div class="min-h-screen bg-slate-100 px-3 py-6 sm:px-6">
    <div class="mx-auto max-w-7xl space-y-5">
        <header class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <a href="{{ route('admin.mou-editor.index', ['order_id' => $order->id]) }}" class="text-sm font-bold text-indigo-600">← Daftar MoU</a>
            <h1 class="mt-2 text-2xl font-black text-slate-950">Editor PDF View Signed</h1>
            <p class="mt-1 text-sm text-slate-600">Order {{ $order->order_number }} · {{ strtoupper(str_replace(['_', '-'], ' ', $document->mou_type)) }} · Dokumen #{{ $document->id }}</p>
        </header>

        @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><strong>Proses belum berhasil:</strong><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm leading-6 text-amber-950">
            <strong>Perbaiki teks langsung di halaman PDF.</strong>
            <p class="mt-1">Klik teks di PDF untuk memilihnya, tekan <strong>Edit Teks Terpilih</strong>, lalu ketik penggantinya. Kamu juga bisa menambah teks baru atau menutup area yang tidak diperlukan. Simpan akan membuat PDF baru; PDF sumber tetap diarsipkan.</p>
            <p class="mt-2 text-xs">Ini bekerja sebagai pengedit visual: teks yang diedit ditutup dan ditulis ulang di posisi yang sama. Layout di luar area yang dipilih tetap dipertahankan. Perubahan isi perjanjian harus disetujui dan ditandatangani ulang bila diperlukan. Pengeditan akan membatalkan validitas tanda tangan digital tersertifikasi; gunakan hanya untuk PDF dengan tanda tangan visual.</p>
        </section>

        @if($signedPdfSourceUrl)
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 text-xs font-black uppercase tracking-wide text-slate-500">Tools</span>
                <button type="button" id="pdf-select-tool" class="pdf-tool active">Pilih / Geser</button>
                <button type="button" id="pdf-edit-text" class="pdf-tool">Edit Teks Terpilih</button>
                <button type="button" id="pdf-add-text" class="pdf-tool">+ Tambah Teks</button>
                <button type="button" id="pdf-whiteout-tool" class="pdf-tool">Tutup Area</button>
                <button type="button" id="pdf-delete-text" class="pdf-tool pdf-tool-danger">Hapus Teks / Koreksi</button>
                <button type="button" id="pdf-undo" class="pdf-tool">Undo</button>
            </div>
            <div class="mt-3 flex flex-wrap items-end gap-3 border-t border-slate-100 pt-3">
                <label class="text-xs font-bold text-slate-600">Font
                    <select id="pdf-font-family" class="pdf-control"><option value="TimesRoman">Times New Roman</option><option value="Helvetica">Arial / Helvetica</option><option value="Courier">Courier</option></select>
                </label>
                <label class="text-xs font-bold text-slate-600">Ukuran
                    <select id="pdf-font-size" class="pdf-control"><option>8</option><option>9</option><option>10</option><option selected>12</option><option>14</option><option>16</option><option>18</option><option>20</option><option>24</option><option>28</option><option>36</option></select>
                </label>
                <label class="flex items-center gap-1.5 pb-2 text-xs font-bold text-slate-600"><input id="pdf-bold" type="checkbox" class="rounded border-slate-300"> Bold</label>
                <label class="flex items-center gap-1.5 pb-2 text-xs font-bold text-slate-600"><input id="pdf-italic" type="checkbox" class="rounded border-slate-300"> Italic</label>
                <label class="flex items-center gap-1.5 pb-2 text-xs font-bold text-slate-600"><input id="pdf-underline" type="checkbox" class="rounded border-slate-300"> Garis bawah</label>
                <label class="text-xs font-bold text-slate-600">Warna <input id="pdf-text-color" type="color" value="#111111" class="ml-1 h-8 w-10 rounded border border-slate-300 bg-white p-1 align-middle"></label>
                <label class="text-xs font-bold text-slate-600">Lebar kotak
                    <select id="pdf-box-width" class="pdf-control"><option value="80">Kecil</option><option value="140">Sedang</option><option value="220" selected>Besar</option><option value="340">Lebar</option><option value="500">Sangat lebar</option></select>
                </label>
                <label class="text-xs font-bold text-slate-600">Tinggi kotak
                    <select id="pdf-box-height" class="pdf-control"><option value="20">Pendek</option><option value="32" selected>Normal</option><option value="48">Tinggi</option><option value="72">Sangat tinggi</option></select>
                </label>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-500">Cara pakai: klik kata atau baris teks di PDF, lalu tekan <strong>Edit Teks Terpilih</strong>. Untuk koreksi teks lama, sistem otomatis menutup posisi teks lama lalu menulis teks baru. Untuk kotak putih atau teks tambahan, pilih alatnya kemudian klik halaman.</p>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-300 bg-slate-200 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-300 bg-white p-3">
                <p id="pdf-status" class="text-sm font-bold text-slate-700">Memuat dokumen dan alat edit…</p>
                <a href="{{ $signedPdfSourceUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700">Buka PDF Asli ↗</a>
            </div>
            <div id="pdf-editor-pages" class="space-y-6 overflow-x-auto p-4"></div>
            <div id="pdf-fallback" class="hidden m-4 rounded-xl border border-rose-200 bg-white p-4 text-sm leading-6 text-rose-800"></div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Simpan PDF hasil edit</h2>
            <p class="mt-1 text-sm leading-6 text-slate-600">Periksa hasil koreksi dan semua halaman sebelum menyimpan. PDF lama tetap diarsipkan dan tidak ditimpa.</p>
            <form id="pdf-save-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.mou-editor.reissue-signed-upload', [$order->id, $document->id]) }}" class="mt-4 space-y-4">
                @csrf
                <input id="corrected-pdf" type="file" name="corrected_pdf" accept="application/pdf,.pdf" class="hidden">
                <div><label for="change-note-upload" class="mb-1 block text-xs font-bold text-slate-700">Catatan perubahan</label><input id="change-note-upload" type="text" name="change_note_upload" maxlength="500" required placeholder="Contoh: Koreksi nomor MoU dan nama pihak" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                <label class="flex items-start gap-2 text-xs leading-5 text-slate-600"><input type="checkbox" name="confirm_visual_reissue_upload" value="1" required class="mt-1 rounded border-slate-400"><span>Saya memastikan koreksi telah disetujui dan memahami bahwa perubahan isi substantif bisa memerlukan tanda tangan ulang.</span></label>
                <button id="pdf-save-button" type="submit" class="rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white hover:bg-emerald-800">Simpan Koreksi & Perbarui View Signed</button>
            </form>
        </section>
        @else
        <section class="rounded-2xl border border-rose-200 bg-white p-8 text-center text-sm text-rose-700">PDF aktif maupun arsip tidak ditemukan. Tidak ada dokumen yang dapat diedit.</section>
        @endif

        @if($signedHistory->isNotEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-black text-slate-950">Arsip PDF Signed Sebelumnya</h2><p class="mt-1 text-sm text-slate-500">File lama disimpan sebagai arsip; tautan berikut tidak mengubah PDF aktif.</p><div class="mt-4 divide-y divide-slate-100">@foreach($signedHistory as $signedVersion)<div class="flex flex-col justify-between gap-3 py-3 sm:flex-row sm:items-center"><div><p class="break-all text-sm font-bold text-slate-800">{{ basename($signedVersion->signed_file_path) }}</p><p class="mt-1 text-xs text-slate-500">{{ $signedVersion->mou_number ?: 'Nomor MoU tidak tercatat' }} · {{ $signedVersion->superseded_at }}</p><p class="mt-1 text-xs text-slate-500">{{ $signedVersion->reason ?: 'Tanpa catatan' }}</p></div>@if(\Illuminate\Support\Facades\Storage::disk('public')->exists($signedVersion->signed_file_path))<a target="_blank" rel="noopener noreferrer" href="{{ asset('storage/' . $signedVersion->signed_file_path) }}" class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">Buka PDF Arsip ↗</a>@else<span class="text-xs font-bold text-rose-700">File tidak ditemukan</span>@endif</div>@endforeach</div></section>
        @endif
    </div>
</div>

<style>
.pdf-tool{border:1px solid #cbd5e1;border-radius:.65rem;padding:.5rem .72rem;font-size:.75rem;font-weight:800;color:#334155;background:#fff;white-space:nowrap}.pdf-tool:hover{background:#f8fafc}.pdf-tool.active{border-color:#6366f1;background:#eef2ff;color:#3730a3}.pdf-tool-danger{border-color:#fecaca;color:#991b1b}.pdf-control{display:block;margin-top:.25rem;border:1px solid #cbd5e1;border-radius:.5rem;padding:.4rem .55rem;background:#fff;color:#334155;font-size:.75rem;font-weight:700}.pdf-page-title{max-width:850px;margin:0 auto 6px;font-size:11px;font-weight:800;color:#475569}.pdf-page-shell{position:relative;width:max-content;max-width:100%;margin:0 auto;background:#fff;box-shadow:0 3px 18px rgba(15,23,42,.18)}.pdf-page-canvas{display:block}.pdf-overlay-layer{position:absolute;inset:0;overflow:hidden}.pdf-target{position:absolute;box-sizing:border-box;white-space:pre;overflow:visible;transform-origin:left top;color:transparent;background:transparent;border:1px solid transparent;cursor:text;line-height:1.2;z-index:1}.pdf-target.selected{outline:1px solid #4f46e5;outline-offset:1px;z-index:20}.pdf-source-cover{position:absolute;background:#fff;pointer-events:none;z-index:5}.pdf-target.editing{background:#fff;color:#111;border:1px dashed #4f46e5;z-index:20;white-space:pre-wrap;overflow:visible;min-height:1.2em}.pdf-new-text{background:rgba(255,255,255,.92);color:#111;border:1px dashed #4f46e5;white-space:pre-wrap;overflow:visible;z-index:10;min-height:20px}.pdf-whiteout{position:absolute;box-sizing:border-box;background:#fff;border:1px dashed #dc2626;color:#b91c1c;z-index:9;cursor:move}.pdf-whiteout:after{content:'AREA DITUTUP';position:absolute;left:3px;top:2px;color:#b91c1c;font:700 9px Arial,sans-serif;pointer-events:none}.pdf-hidden{display:none!important}
</style>

@if($signedPdfSourceUrl)
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
(function(){
    'use strict';
    const pdfUrl = @json($signedPdfSourceUrl);
    const statusNode = document.getElementById('pdf-status');
    const pagesHost = document.getElementById('pdf-editor-pages');
    const fallbackNode = document.getElementById('pdf-fallback');
    const saveForm = document.getElementById('pdf-save-form');
    const fileInput = document.getElementById('corrected-pdf');
    const saveButton = document.getElementById('pdf-save-button');
    const SCALE = 1.35;
    const pages = [];
    let sourceBytes = null;
    let tool = 'select';
    let selected = null;
    let drag = null;
    let bypassSubmit = false;
    let saving = false;
    let changeHistory = [];

    const el = id => document.getElementById(id);
    const value = (id, fallback) => { const n = Number(el(id).value); return Number.isFinite(n) ? n : fallback; };
    const setStatus = message => { statusNode.textContent = message; };
    const clamp = (n, low, high) => Math.max(low, Math.min(n, high));

    function activateTool(nextTool){
        tool = nextTool;
        const map = {'select':'pdf-select-tool','edit':'pdf-edit-text','text':'pdf-add-text','whiteout':'pdf-whiteout-tool'};
        Object.values(map).forEach(id=>el(id).classList.toggle('active',map[tool]===id));
        const messages = {
            select:'Klik teks untuk memilihnya. Geser teks terpilih jika perlu.',
            edit:'Pilih teks di halaman, lalu klik Edit Teks Terpilih.',
            text:'Mode Tambah Teks: klik lokasi pada halaman untuk menaruh teks baru.',
            whiteout:'Mode Tutup Area: klik lokasi teks/objek yang ingin ditutup dengan putih.'
        };
        setStatus(messages[tool]);
    }
    el('pdf-select-tool').addEventListener('click',()=>activateTool('select'));
    el('pdf-edit-text').addEventListener('click',beginEditingSelected);
    el('pdf-add-text').addEventListener('click',()=>activateTool('text'));
    el('pdf-whiteout-tool').addEventListener('click',()=>activateTool('whiteout'));

    function remember(){
        if(!selected)return;
        changeHistory.push(JSON.stringify({kind:selected.kind,pageIndex:selected.pageIndex,x:selected.x,y:selected.y,width:selected.width,height:selected.height,text:selected.text,fontFamily:selected.fontFamily,fontSize:selected.fontSize,bold:selected.bold,italic:selected.italic,underline:selected.underline,color:selected.color,align:selected.align,deleted:selected.deleted,changed:selected.changed,moved:selected.moved,editing:selected.editing}));
        if(changeHistory.length>60)changeHistory.shift();
    }
    function syncToolbar(){
        if(!selected || selected.kind==='whiteout')return;
        el('pdf-font-family').value=selected.fontFamily||'TimesRoman';
        const size=String(Math.round(selected.fontSize||12));
        if(!Array.from(el('pdf-font-size').options).some(o=>o.value===size)){const option=document.createElement('option');option.value=size;option.textContent=size;el('pdf-font-size').appendChild(option);}
        el('pdf-font-size').value=size;
        el('pdf-bold').checked=!!selected.bold;
        el('pdf-italic').checked=!!selected.italic;
        el('pdf-underline').checked=!!selected.underline;
        el('pdf-text-color').value=selected.color||'#111111';
        const width=String(Math.round(selected.width||220));
        if(!Array.from(el('pdf-box-width').options).some(o=>o.value===width)){const option=document.createElement('option');option.value=width;option.textContent='Saat ini ('+width+' px)';el('pdf-box-width').appendChild(option);}
        el('pdf-box-width').value=width;
        const height=String(Math.round(selected.height||32));
        if(!Array.from(el('pdf-box-height').options).some(o=>o.value===height)){const option=document.createElement('option');option.value=height;option.textContent='Saat ini ('+height+' px)';el('pdf-box-height').appendChild(option);}
        el('pdf-box-height').value=height;
    }
    function selectItem(item){
        if(selected && selected.dom)selected.dom.classList.remove('selected');
        selected=item;
        if(item && item.dom)item.dom.classList.add('selected');
        syncToolbar();
    }
    function fontCss(item){
        const family=item.fontFamily||'TimesRoman';
        return family==='Helvetica'?'Arial,Helvetica,sans-serif':family==='Courier'?'Courier New,monospace':'Times New Roman,serif';
    }
    function setItemVisual(item){
        const node=item.dom;
        node.style.left=item.x+'px'; node.style.top=item.y+'px'; node.style.width=Math.max(6,item.width)+'px'; node.style.height=Math.max(8,item.height)+'px';
        if(item.kind==='whiteout'){
            node.classList.add('pdf-whiteout');node.style.background='#fff';node.style.color='#b91c1c';
            return;
        }
        node.style.fontFamily=fontCss(item);node.style.fontSize=(item.fontSize*SCALE)+'px';node.style.fontWeight=item.bold?'700':'400';node.style.fontStyle=item.italic?'italic':'normal';node.style.textDecoration=item.underline?'underline':'none';node.style.textAlign=item.align||'left';node.style.color='#111';
        if(item.kind==='source' && item.cover){
            item.cover.style.display=item.moved?'block':'none';
            item.cover.style.left=item.origX+'px';item.cover.style.top=item.origY+'px';item.cover.style.width=(item.origWidth+3)+'px';item.cover.style.height=item.origHeight+'px';
        }
        if(item.kind==='source' && !item.editing && !item.changed && !item.deleted && !item.moved){
            node.contentEditable='false';node.classList.remove('editing');node.style.color='transparent';node.style.background='transparent';node.style.borderColor='transparent';node.textContent=item.originalText;
        } else if(item.deleted){
            node.contentEditable='false';node.classList.remove('editing');node.style.color='transparent';node.style.background='#fff';node.style.borderColor='#dc2626';node.textContent='';
        } else {
            node.classList.add(item.kind==='source'?'editing':'pdf-new-text');node.style.background='#fff';node.style.borderColor='#4f46e5';node.style.color=item.color||'#111';node.contentEditable=item.editing?'true':'false';node.spellcheck=false;
            const content=item.text||'';
            if(node.textContent!==content)node.textContent=content;
        }
    }
    function pointerMoveStart(item,event,handleOnly){
        if(item.editing)return;
        event.preventDefault();event.stopPropagation();selectItem(item);
        const page=pages[item.pageIndex];
        remember();
        drag={item,startClientX:event.clientX,startClientY:event.clientY,startX:item.x,startY:item.y,layerRect:page.layer.getBoundingClientRect(),moved:false};
        if(handleOnly)activateTool('select');
    }
    function makeSourceTarget(page,item){
        const cover=document.createElement('div');cover.className='pdf-source-cover';cover.style.display='none';page.layer.appendChild(cover);item.cover=cover;
        const node=document.createElement('span');node.className='pdf-target';node.textContent=item.originalText;node.style.left=item.x+'px';node.style.top=item.y+'px';node.style.width=Math.max(4,item.origWidth+3)+'px';node.style.height=Math.max(8,item.origHeight)+'px';node.style.fontSize=(item.fontSize*SCALE)+'px';node.style.fontFamily=fontCss(item);
        node.addEventListener('click',event=>{event.stopPropagation();selectItem(item);if(tool==='edit')beginEditingSelected();});
        node.addEventListener('pointerdown',event=>{if(item.editing)return;pointerMoveStart(item,event,false);});
        node.addEventListener('input',()=>{item.text=node.innerText.replace(/\r/g,'');item.changed=item.text!==item.originalText;});
        node.addEventListener('blur',()=>{if(item.editing){item.text=node.innerText.replace(/\r/g,'');item.editing=false;setItemVisual(item);}},true);
        node.addEventListener('dblclick',event=>{event.preventDefault();selectItem(item);beginEditingSelected();});
        item.dom=node;page.layer.appendChild(node);setItemVisual(item);
    }
    function createOperation(page,type,x,y){
        const boxWidth=value('pdf-box-width',220),boxHeight=value('pdf-box-height',32);
        const item={kind:type,pageIndex:page.index,x:clamp(x,0,page.cssWidth-8),y:clamp(y,0,page.cssHeight-8),width:type==='whiteout'?boxWidth:boxWidth,height:type==='whiteout'?boxHeight:boxHeight,text:type==='text'?'Tulis teks di sini':'',fontFamily:el('pdf-font-family').value,fontSize:value('pdf-font-size',12),bold:el('pdf-bold').checked,italic:el('pdf-italic').checked,underline:el('pdf-underline').checked,color:el('pdf-text-color').value,align:'left',changed:true,editing:type==='text',deleted:false};
        const node=document.createElement('div');node.className='pdf-target';item.dom=node;page.layer.appendChild(node);page.operations.push(item);setItemVisual(item);addMoveHandle(item);selectItem(item);
        node.addEventListener('input',()=>{item.text=node.innerText.replace(/\r/g,'');item.changed=true;});
        node.addEventListener('click',event=>{event.stopPropagation();selectItem(item);});
        node.addEventListener('pointerdown',event=>{if(event.target===item.moveHandle)return;if(item.editing)return;pointerMoveStart(item,event,false);});
        if(type==='text'){node.focus();try{const range=document.createRange();range.selectNodeContents(node);range.collapse(false);const sel=window.getSelection();sel.removeAllRanges();sel.addRange(range);}catch(e){}}
        setStatus(type==='whiteout'?'Kotak putih ditambahkan. Geser/atur ukuran, lalu simpan.':'Teks baru ditambahkan. Ketik langsung pada kotak teks.');
        return item;
    }
    function beginEditingSelected(){
        if(!selected || !['source','text'].includes(selected.kind)){setStatus('Klik teks yang ingin diedit terlebih dahulu.');return;}
        remember();selected.editing=true;selected.changed=true;
        selected.dom.contentEditable='true';selected.dom.classList.add('editing');selected.dom.style.color=selected.color||'#111';selected.dom.style.background='#fff';selected.dom.style.borderColor='#4f46e5';
        selected.dom.focus();
        try{const range=document.createRange();range.selectNodeContents(selected.dom);range.collapse(false);const sel=window.getSelection();sel.removeAllRanges();sel.addRange(range);}catch(e){}
        setStatus('Teks dipilih. Ketik perubahan langsung pada kotak di halaman.');
    }
    function deleteSelected(){
        if(!selected){setStatus('Pilih teks atau koreksi terlebih dahulu.');return;}
        remember();
        if(selected.kind==='source'){
            selected.deleted=true;selected.changed=true;selected.editing=false;setItemVisual(selected);setStatus('Teks sumber akan ditutup putih ketika PDF disimpan.');
        } else {
            const page=pages[selected.pageIndex];page.operations=page.operations.filter(item=>item!==selected);selected.dom.remove();selected=null;setStatus('Koreksi dihapus.');
        }
        syncToolbar();
    }
    el('pdf-delete-text').addEventListener('click',deleteSelected);
    el('pdf-undo').addEventListener('click',()=>{
        if(!selected || !changeHistory.length){setStatus('Undo memilih satu koreksi terakhir yang masih dipilih; belum ada perubahan yang dapat dipulihkan.');return;}
        const previous=JSON.parse(changeHistory.pop());Object.assign(selected,previous);setItemVisual(selected);syncToolbar();setStatus('Perubahan pada koreksi terpilih dibatalkan.');
    });
    ['pdf-font-family','pdf-font-size','pdf-bold','pdf-italic','pdf-underline','pdf-text-color','pdf-box-width','pdf-box-height'].forEach(id=>el(id).addEventListener('change',()=>{
        if(!selected || selected.kind==='whiteout')return;
        remember();selected.fontFamily=el('pdf-font-family').value;selected.fontSize=value('pdf-font-size',12);selected.bold=el('pdf-bold').checked;selected.italic=el('pdf-italic').checked;selected.underline=el('pdf-underline').checked;selected.color=el('pdf-text-color').value;selected.width=value('pdf-box-width',220);selected.height=value('pdf-box-height',32);selected.changed=true;setItemVisual(selected);syncToolbar();
    }));
    ['pdf-box-width','pdf-box-height'].forEach(id=>el(id).addEventListener('change',()=>{
        if(!selected || selected.kind!=='whiteout')return;remember();selected.width=value('pdf-box-width',220);selected.height=value('pdf-box-height',32);setItemVisual(selected);
    }));

    document.addEventListener('pointermove',event=>{
        if(!drag)return;
        const page=pages[drag.item.pageIndex];const rect=page.layer.getBoundingClientRect();
        const sx=page.cssWidth/(rect.width||page.cssWidth),sy=page.cssHeight/(rect.height||page.cssHeight);
        const dx=(event.clientX-drag.startClientX)*sx,dy=(event.clientY-drag.startClientY)*sy;
        if(Math.abs(dx)+Math.abs(dy)>1)drag.moved=true;
        if(drag.moved){drag.item.x=clamp(drag.startX+dx,0,page.cssWidth-6);drag.item.y=clamp(drag.startY+dy,0,page.cssHeight-6);drag.item.moved=true;drag.item.changed=true;if(drag.item.kind==='source'){drag.item.baseline=drag.item.origBaseline+(drag.item.y-drag.item.origY);}setItemVisual(drag.item);}
    });
    document.addEventListener('pointerup',()=>{if(drag){if(drag.moved){drag.item.moved=true;drag.item.changed=true;setItemVisual(drag.item);setStatus('Posisi koreksi berubah. Perubahan akan dimasukkan saat disimpan.');}drag=null;}});

    async function renderPdf(){
        if(!window.pdfjsLib || !window.PDFLib)throw new Error('Library editor PDF tidak berhasil dimuat. Periksa koneksi internet atau pemblokir CDN.');
        window.pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        const response=await fetch(pdfUrl,{credentials:'same-origin',cache:'no-store'});if(!response.ok)throw new Error('PDF sumber gagal dibaca (HTTP '+response.status+').');
        sourceBytes=new Uint8Array(await response.arrayBuffer());
        const pdf=await window.pdfjsLib.getDocument({data:sourceBytes.slice()}).promise;
        pagesHost.innerHTML='';
        for(let number=1;number<=pdf.numPages;number++){
            const page=await pdf.getPage(number);const viewport=page.getViewport({scale:SCALE});
            const title=document.createElement('p');title.className='pdf-page-title';title.textContent='Halaman '+number+' dari '+pdf.numPages;pagesHost.appendChild(title);
            const shell=document.createElement('div');shell.className='pdf-page-shell';shell.style.width=viewport.width+'px';shell.style.height=viewport.height+'px';
            const canvas=document.createElement('canvas');canvas.className='pdf-page-canvas';canvas.width=Math.ceil(viewport.width);canvas.height=Math.ceil(viewport.height);canvas.style.width=viewport.width+'px';canvas.style.height=viewport.height+'px';
            const layer=document.createElement('div');layer.className='pdf-overlay-layer';layer.style.width=viewport.width+'px';layer.style.height=viewport.height+'px';shell.append(canvas,layer);pagesHost.appendChild(shell);
            const pageState={index:number-1,pageNo:number,canvas,layer,shell,cssWidth:viewport.width,cssHeight:viewport.height,pdfWidth:page.view[2]-page.view[0],pdfHeight:page.view[3]-page.view[1],textItems:[],operations:[],viewport};pages.push(pageState);
            await page.render({canvasContext:canvas.getContext('2d'),viewport}).promise;
            const textContent=await page.getTextContent();
            textContent.items.forEach((textItem,index)=>{
                if(!textItem.str || !textItem.str.trim())return;
                const transform=window.pdfjsLib.Util.transform(viewport.transform,textItem.transform);
                const fontHeight=Math.max(6,Math.hypot(transform[2],transform[3]));
                const style=textContent.styles[textItem.fontName]||{};
                const ascent=typeof style.ascent==='number'?style.ascent:0.82;
                const item={kind:'source',pageIndex:number-1,sourceIndex:index,originalText:textItem.str,text:textItem.str,x:transform[4],y:transform[5]-fontHeight*ascent,origX:transform[4],origY:transform[5]-fontHeight*ascent,width:Math.max(4,textItem.width*SCALE),origWidth:Math.max(4,textItem.width*SCALE),height:Math.max(8,fontHeight*1.25),origHeight:Math.max(8,fontHeight*1.25),baseline:transform[5],origBaseline:transform[5],fontSize:fontHeight/SCALE,fontFamily:'TimesRoman',bold:/bold|black|demi/i.test(textItem.fontName||''),italic:/italic|oblique/i.test(textItem.fontName||''),underline:false,color:'#111111',align:'left',changed:false,moved:false,deleted:false,editing:false};
                const family=(style.fontFamily||'').toLowerCase();if(family.includes('sans'))item.fontFamily='Helvetica';else if(family.includes('mono'))item.fontFamily='Courier';
                pageState.textItems.push(item);makeSourceTarget(pageState,item);
            });
            layer.addEventListener('click',event=>{
                if(event.target!==layer)return;
                if(tool==='text'||tool==='whiteout'){
                    const rect=layer.getBoundingClientRect();const x=(event.clientX-rect.left)*(pageState.cssWidth/(rect.width||pageState.cssWidth));const y=(event.clientY-rect.top)*(pageState.cssHeight/(rect.height||pageState.cssHeight));createOperation(pageState,tool,x,y);if(tool==='text')activateTool('select');
                } else if(tool==='select'){selectItem(null);}
            });
        }
        setStatus('PDF siap diedit · '+pdf.numPages+' halaman. Klik teks yang ingin kamu koreksi.');
    }

    function rgbFromHex(hex){const s=(hex||'#111111').replace('#','');return {r:parseInt(s.slice(0,2),16)/255,g:parseInt(s.slice(2,4),16)/255,b:parseInt(s.slice(4,6),16)/255};}
    function fontName(item){if(item.fontFamily==='Helvetica')return item.bold?(item.italic?'HelveticaBoldOblique':'HelveticaBold'):(item.italic?'HelveticaOblique':'Helvetica');if(item.fontFamily==='Courier')return item.bold?(item.italic?'CourierBoldOblique':'CourierBold'):(item.italic?'CourierOblique':'Courier');return item.bold?(item.italic?'TimesRomanBoldItalic':'TimesRomanBold'):(item.italic?'TimesRomanItalic':'TimesRoman');}
    function getOperations(){
        const result=[];
        pages.forEach(page=>{
            page.textItems.forEach(item=>{if(item.deleted||item.changed||item.moved){if(item.deleted||item.text!==item.originalText||item.moved||item.changed){result.push({...item,kind:'source-edit'});}}});
            page.operations.forEach(item=>result.push({...item}));
        });
        return result;
    }
    async function buildPdf(){
        const {PDFDocument,StandardFonts,rgb}=window.PDFLib;const doc=await PDFDocument.load(sourceBytes.slice());const pdfPages=doc.getPages();const operations=getOperations();if(!operations.length)throw new Error('Belum ada perubahan. Klik teks untuk diedit, tambah teks, atau tutup area terlebih dahulu.');
        const fontCache={};
        async function getFont(item){const key=fontName(item);if(!fontCache[key])fontCache[key]=await doc.embedFont(StandardFonts[key]);return fontCache[key];}
        for(const op of operations){
            const page=pdfPages[op.pageIndex];if(!page)continue;const sx=page.getWidth()/pages[op.pageIndex].cssWidth,sy=page.getHeight()/pages[op.pageIndex].cssHeight;
            if(op.kind==='whiteout'){page.drawRectangle({x:op.x*sx,y:page.getHeight()-(op.y+op.height)*sy,width:op.width*sx,height:op.height*sy,color:rgb(1,1,1),borderColor:rgb(1,1,1),borderWidth:0});}
            if(op.kind==='source-edit'){const rectX=op.origX-1,rectY=op.origY-1,rectW=op.origWidth+3,rectH=op.origHeight+2;page.drawRectangle({x:Math.max(0,rectX*sx),y:Math.max(0,page.getHeight()-(rectY+rectH)*sy),width:Math.min(page.getWidth(),rectW*sx),height:rectH*sy,color:rgb(1,1,1),borderColor:rgb(1,1,1),borderWidth:0});}
        }
        for(const op of operations){
            if(op.kind==='whiteout'||(op.kind==='source-edit'&&op.deleted))continue;
            const text=String(op.text||'').replace(/\r/g,'');if(!text)continue;
            const page=pdfPages[op.pageIndex];if(!page)continue;const sx=page.getWidth()/pages[op.pageIndex].cssWidth,sy=page.getHeight()/pages[op.pageIndex].cssHeight;const font=await getFont(op);const fontSize=Math.max(4,Number(op.fontSize)||12);const color=rgbFromHex(op.color);const lines=text.split('\n');const startX=op.x*sx;const maxWidth=Math.max(8,(op.width||200)*sx-2);const lineGap=fontSize*1.25;
            lines.forEach((line,index)=>{
                const lineWidth=font.widthOfTextAtSize(line,fontSize);let x=startX+1;
                if(op.align==='center')x=startX+Math.max(0,(maxWidth-lineWidth)/2);
                if(op.align==='right')x=startX+Math.max(0,maxWidth-lineWidth-1);
                let baselineCss=typeof op.baseline==='number'?op.baseline:op.y+fontSize*SCALE*.9;
                let y=page.getHeight()-(baselineCss+index*lineGap*SCALE)*sy;
                page.drawText(line,{x,y,size:fontSize,font,color, maxWidth:Math.max(8,maxWidth),lineHeight:lineGap});
                if(op.underline && line.length){page.drawLine({start:{x,y:y-1.4},end:{x:x+lineWidth,y:y-1.4},thickness:0.6,color});}
            });
        }
        return await doc.save();
    }
    saveForm.addEventListener('submit',async event=>{
        if(bypassSubmit){bypassSubmit=false;return;}
        event.preventDefault();if(saving)return;saving=true;saveButton.disabled=true;saveButton.textContent='Membuat PDF hasil koreksi…';
        try{
            const bytes=await buildPdf();const blob=new Blob([bytes],{type:'application/pdf'});if(blob.size>10*1024*1024)throw new Error('PDF hasil koreksi melebihi batas upload 10 MB. Kurangi ukuran/perubahan lalu coba lagi.');
            const file=new File([blob],'Signed_MoU_Corrected.pdf',{type:'application/pdf'});const transfer=new DataTransfer();transfer.items.add(file);fileInput.files=transfer.files;bypassSubmit=true;saveForm.requestSubmit();
        }catch(error){setStatus('Gagal membuat PDF: '+error.message);alert('PDF tidak berhasil dibuat. '+error.message);saving=false;saveButton.disabled=false;saveButton.textContent='Simpan Koreksi & Perbarui View Signed';}
    });
    renderPdf().catch(error=>{fallbackNode.classList.remove('hidden');fallbackNode.textContent='Editor tidak dapat memuat PDF: '+error.message;setStatus('Gagal memuat editor PDF.');});
})();
</script>
@endif
</x-app-layout>
