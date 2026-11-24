<x-app-layout>
    <div class="min-h-screen bg-slate-100 px-3 py-6 sm:px-6">
        <div class="mx-auto max-w-[1600px] space-y-4">
            <div class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center">
                <div>
                    <a href="{{ route('admin.orders') }}" class="text-sm font-bold text-indigo-600">← Kembali ke Orders</a>
                    <h1 class="mt-2 text-2xl font-black text-slate-950">Berita Acara Editor</h1>
                    <p class="mt-1 text-sm text-slate-600">Order {{ $order->order_number }} · {{ $order->proker_name ?: '-' }}</p>
                    <p class="mt-1 text-xs text-slate-500">Edit teks langsung di dokumen, lalu unggah tanda tangan pengurus acara dan bukti transfer denda.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.document.berita-acara', $order->id) }}" target="_blank" rel="noopener noreferrer" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-800">Preview / Download PDF ↗</a>
                    <a href="{{ route('admin.ba-editor.edit', ['order' => $order->id, 'reset' => 1]) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Muat Template Awal</a>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <strong>Perlu diperbaiki:</strong>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-sky-950">
                <strong>Petunjuk:</strong> Klik bagian teks pada kertas untuk mengeditnya. Template sudah menampilkan nama Bendahara Proker dan Ketua Acara dari data order. Slot tanda tangan kedua pengurus acara akan terisi dari file yang diunggah di bawah. Tanda tangan Bendahara Student Council pada bagian “Mengetahui” tetap memakai tanda tangan yang sudah disimpan di Pengaturan.
            </div>

            <form id="ba-save-form" method="POST" action="{{ route('admin.ba-editor.save', $order->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="rendered_html" id="rendered-html">

                <section class="sticky top-0 z-30 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" class="toolbar" data-command="undo" title="Undo">↶ Undo</button>
                        <button type="button" class="toolbar" data-command="redo" title="Redo">↷ Redo</button>
                        <span class="mx-1 hidden h-7 border-l border-slate-200 sm:inline-block"></span>
                        <select id="format-block" class="toolbar-select" aria-label="Format paragraf">
                            <option value="P">Paragraf</option><option value="H1">Heading 1</option><option value="H2">Heading 2</option><option value="H3">Heading 3</option><option value="BLOCKQUOTE">Kutipan</option>
                        </select>
                        <select id="font-family" class="toolbar-select" aria-label="Jenis font">
                            <option value="Times New Roman">Times New Roman</option><option value="Arial">Arial</option><option value="Georgia">Georgia</option><option value="Calibri">Calibri</option><option value="Verdana">Verdana</option>
                        </select>
                        <select id="font-size" class="toolbar-select" aria-label="Ukuran font">
                            <option value="2">10 pt</option><option value="3" selected>12 pt</option><option value="4">14 pt</option><option value="5">18 pt</option><option value="6">24 pt</option><option value="7">36 pt</option>
                        </select>
                        <button type="button" class="toolbar" data-command="bold"><strong>B</strong></button>
                        <button type="button" class="toolbar" data-command="italic"><em>I</em></button>
                        <button type="button" class="toolbar" data-command="underline"><u>U</u></button>
                        <button type="button" class="toolbar" data-command="strikeThrough">S̶</button>
                        <button type="button" class="toolbar" data-command="removeFormat">Hapus Format</button>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <button type="button" class="toolbar" data-command="justifyLeft">Rata Kiri</button>
                        <button type="button" class="toolbar" data-command="justifyCenter">Tengah</button>
                        <button type="button" class="toolbar" data-command="justifyRight">Rata Kanan</button>
                        <button type="button" class="toolbar" data-command="justifyFull">Justify</button>
                        <button type="button" class="toolbar" data-command="insertUnorderedList">• Daftar</button>
                        <button type="button" class="toolbar" data-command="insertOrderedList">1. Daftar</button>
                        <label class="toolbar-color">Warna Teks <input id="text-color" type="color" value="#222222"></label>
                        <label class="toolbar-color">Sorot <input id="highlight-color" type="color" value="#fff2a8"></label>
                        <button type="button" id="insert-link" class="toolbar">Tambah Link</button>
                        <button type="button" class="toolbar" data-command="unlink">Lepas Link</button>
                        <button type="button" class="toolbar" data-command="insertHorizontalRule">Garis</button>
                        <button type="button" id="insert-table" class="toolbar">+ Tabel</button>
                        <button type="button" id="insert-page-break" class="toolbar">Pemisah Halaman</button>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-2">
                        <span id="editor-status" class="text-xs font-bold text-slate-500">Menyiapkan dokumen…</span>
                        <span class="text-xs text-slate-400">Gunakan Undo bila salah mengedit.</span>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-300 p-3 shadow-sm sm:p-5">
                    <iframe id="ba-frame" title="Editor Berita Acara" sandbox="allow-same-origin" class="block h-[1050px] w-full rounded-lg border-0 bg-white shadow-lg"></iframe>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-lg font-black text-slate-950">Upload Tanda Tangan & Lampiran</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Upload file PNG/JPG untuk tanda tangan. Bukti transfer dapat berupa PNG/JPG atau PDF. File PDF bukti transfer akan ditampilkan sebagai tautan pada halaman lampiran Berita Acara.</p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 p-4">
                            <label for="ba-treasurer-signature" class="mb-2 block text-sm font-extrabold text-slate-800">Tanda Tangan Bendahara Proker</label>
                            <p class="mb-3 text-xs text-slate-500">Nama pada dokumen: {{ $order->treasurer_name ?: '-' }}</p>
                            @if($order->ba_treasurer_signature_path)
                                <a href="{{ asset('storage/' . $order->ba_treasurer_signature_path) }}" target="_blank" class="mb-3 inline-block text-xs font-bold text-emerald-700">Lihat tanda tangan yang tersimpan ↗</a>
                            @endif
                            <input id="ba-treasurer-signature" type="file" name="ba_treasurer_signature" accept=".png,.jpg,.jpeg,image/png,image/jpeg" class="block w-full rounded-lg border border-slate-300 p-2 text-sm">
                            <p class="mt-2 text-xs text-slate-400">Maksimal 2 MB. Upload ulang untuk mengganti tanda tangan sebelumnya.</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4">
                            <label for="ba-ketua-signature" class="mb-2 block text-sm font-extrabold text-slate-800">Tanda Tangan Ketua Acara</label>
                            <p class="mb-3 text-xs text-slate-500">Nama pada dokumen: {{ $order->ketua_acara ?: '-' }}</p>
                            @if($order->ba_ketua_signature_path)
                                <a href="{{ asset('storage/' . $order->ba_ketua_signature_path) }}" target="_blank" class="mb-3 inline-block text-xs font-bold text-emerald-700">Lihat tanda tangan yang tersimpan ↗</a>
                            @endif
                            <input id="ba-ketua-signature" type="file" name="ba_ketua_signature" accept=".png,.jpg,.jpeg,image/png,image/jpeg" class="block w-full rounded-lg border border-slate-300 p-2 text-sm">
                            <p class="mt-2 text-xs text-slate-400">Maksimal 2 MB. Upload ulang untuk mengganti tanda tangan sebelumnya.</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4 md:col-span-2">
                            <label for="ba-payment-proof" class="mb-2 block text-sm font-extrabold text-slate-800">Bukti Transfer Denda (Lampiran)</label>
                            @if($order->ba_payment_proof_path)
                                <a href="{{ asset('storage/' . $order->ba_payment_proof_path) }}" target="_blank" class="mb-3 inline-block text-xs font-bold text-emerald-700">Buka bukti transfer yang tersimpan ↗</a>
                            @else
                                <p class="mb-3 text-xs text-slate-500">Belum ada bukti transfer yang diunggah.</p>
                            @endif
                            <input id="ba-payment-proof" type="file" name="ba_payment_proof" accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf" class="block w-full rounded-lg border border-slate-300 p-2 text-sm">
                            <p class="mt-2 text-xs text-slate-400">Maksimal 10 MB. Gambar akan langsung ditampilkan pada halaman lampiran. File PDF disimpan dan dapat dibuka melalui tautan pada PDF Berita Acara.</p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-col-reverse justify-between gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                        <p class="text-xs leading-5 text-slate-500">Klik Simpan untuk menyimpan perubahan dokumen dan seluruh file yang dipilih.</p>
                        <button id="save-ba-button" type="submit" class="rounded-xl bg-slate-950 px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700">Simpan Berita Acara</button>
                    </div>
                </section>
            </form>
        </div>
    </div>

    <style>
        .toolbar{border:1px solid #cbd5e1;border-radius:.65rem;padding:.48rem .68rem;font-size:.76rem;font-weight:800;color:#334155;background:#fff;white-space:nowrap}
        .toolbar:hover{background:#f8fafc}.toolbar:focus,.toolbar-select:focus{outline:2px solid #a5b4fc;outline-offset:1px}
        .toolbar-select{max-width:210px;border:1px solid #cbd5e1;border-radius:.65rem;padding:.48rem .6rem;background:#fff;color:#334155;font-size:.76rem;font-weight:700}
        .toolbar-color{display:inline-flex;align-items:center;gap:.35rem;border:1px solid #cbd5e1;border-radius:.65rem;padding:.25rem .45rem;background:#fff;color:#334155;font-size:.74rem;font-weight:800}
        .toolbar-color input{width:24px;height:23px;padding:0;border:0;background:transparent;cursor:pointer}
        @media(max-width:640px){.toolbar-select{max-width:140px}.toolbar{padding:.42rem .55rem}}
    </style>

    <script>
        (function () {
            const frame = document.getElementById('ba-frame');
            const status = document.getElementById('editor-status');
            const form = document.getElementById('ba-save-form');
            const htmlInput = document.getElementById('rendered-html');
            const initialHtmlBase64 = @json($editorHtmlBase64);
            let editorReady = false;

            function decodeBase64Utf8(value) {
                const binary = atob(value);
                const bytes = Uint8Array.from(binary, character => character.charCodeAt(0));
                return new TextDecoder('utf-8').decode(bytes);
            }

            function editorDocument() {
                try { return frame.contentDocument || frame.contentWindow.document; }
                catch (error) { return null; }
            }

            function focusEditor() {
                const doc = editorDocument();
                if (!doc || !editorReady) return null;
                frame.contentWindow.focus();
                doc.body.focus();
                return doc;
            }

            function runCommand(command, value = null) {
                const doc = focusEditor();
                if (!doc) return;
                doc.execCommand(command, false, value);
                status.textContent = 'Perubahan pada dokumen belum disimpan';
            }

            frame.addEventListener('load', function () {
                const doc = editorDocument();
                if (!doc) {
                    status.textContent = 'Dokumen gagal dimuat. Muat ulang halaman.';
                    return;
                }
                doc.designMode = 'on';
                doc.body.contentEditable = 'true';
                doc.body.spellcheck = true;
                editorReady = true;
                status.textContent = 'Template siap diedit · klik teks pada dokumen';
                doc.addEventListener('input', () => status.textContent = 'Ada perubahan yang belum disimpan');
                doc.addEventListener('keyup', () => status.textContent = 'Ada perubahan yang belum disimpan');
                doc.addEventListener('mouseup', () => status.textContent = 'Template siap diedit');
            });

            frame.srcdoc = decodeBase64Utf8(initialHtmlBase64);

            document.querySelectorAll('[data-command]').forEach(button => {
                button.addEventListener('click', () => runCommand(button.dataset.command));
            });

            document.getElementById('format-block').addEventListener('change', event => runCommand('formatBlock', event.target.value));
            document.getElementById('font-family').addEventListener('change', event => runCommand('fontName', event.target.value));
            document.getElementById('font-size').addEventListener('change', event => runCommand('fontSize', event.target.value));
            document.getElementById('text-color').addEventListener('input', event => runCommand('foreColor', event.target.value));
            document.getElementById('highlight-color').addEventListener('input', event => runCommand('hiliteColor', event.target.value));

            document.getElementById('insert-link').addEventListener('click', function () {
                const url = prompt('Masukkan alamat link (https://...)');
                if (url) runCommand('createLink', url);
            });

            document.getElementById('insert-table').addEventListener('click', function () {
                const rows = Math.max(1, Math.min(12, parseInt(prompt('Jumlah baris tabel?', '3'), 10) || 3));
                const columns = Math.max(1, Math.min(8, parseInt(prompt('Jumlah kolom tabel?', '2'), 10) || 2));
                let table = '<table style="width:100%;border-collapse:collapse;margin:10px 0;">';
                for (let row = 0; row < rows; row++) {
                    table += '<tr>';
                    for (let column = 0; column < columns; column++) {
                        table += '<td style="border:1px solid #777;padding:6px;min-width:35px;">&nbsp;</td>';
                    }
                    table += '</tr>';
                }
                table += '</table><p></p>';
                runCommand('insertHTML', table);
            });

            document.getElementById('insert-page-break').addEventListener('click', function () {
                runCommand('insertHTML', '<div style="page-break-after:always;break-after:page;height:1px;"></div><p></p>');
            });

            form.addEventListener('submit', function (event) {
                const doc = editorDocument();
                if (!doc || !editorReady) {
                    event.preventDefault();
                    alert('Editor belum siap. Tunggu sampai template selesai dimuat.');
                    return;
                }

                const savedBody = doc.body.cloneNode(true);
                savedBody.removeAttribute('contenteditable');
                savedBody.removeAttribute('spellcheck');
                savedBody.querySelectorAll('[contenteditable]').forEach(element => element.removeAttribute('contenteditable'));
                htmlInput.value = '<!DOCTYPE html>\n' + doc.documentElement.outerHTML.replace(doc.body.outerHTML, savedBody.outerHTML);

                if (!htmlInput.value.trim()) {
                    event.preventDefault();
                    alert('Isi dokumen tidak boleh kosong.');
                }
            });
        })();
    </script>
</x-app-layout>
