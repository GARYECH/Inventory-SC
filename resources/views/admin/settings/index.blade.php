<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">

        <!-- HEADER -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl flex items-center justify-center shadow-lg shadow-gray-200 rotate-3 hover:rotate-0 transition-all duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31.826-2.37 2.37.996.608 2.296.07 2.572-1.065z"
                            ></path>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            ></path>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">
                            System Settings
                        </h1>

                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mt-1.5">
                            SOP & General Configuration
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

            <!-- NOTIFIKASI SUKSES -->
            @if(session('success'))
                <div class="mb-8 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <p class="text-sm font-bold text-emerald-800">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <!-- NOTIFIKASI ERROR -->
            @if($errors->any())
                <div class="mb-8 px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333.192-3 1.732-3z"
                            ></path>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-red-800">
                            Gagal menyimpan:
                        </p>

                        <ul class="text-[10px] font-bold text-red-600 mt-1 list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form
                action="{{ route('admin.settings.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-8"
            >
                @csrf

                <!-- ========================================================= -->
                <!-- SOP -->
                <!-- ========================================================= -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">

                    <h3 class="text-lg font-black text-gray-900 tracking-tight mb-2">
                        Dokumen SOP Peminjaman
                    </h3>

                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        File PDF ini akan menjadi "Syarat & Ketentuan" yang wajib dibaca dan disetujui mahasiswa sebelum Checkout.
                    </p>

                    @if(isset($settings['sop_pdf_path']))
                        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-2xl flex justify-between items-center">

                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    ></path>
                                </svg>

                                <div>
                                    <p class="text-xs font-black text-indigo-900">
                                        SOP Ter-Upload
                                    </p>

                                    <p class="text-[9px] text-indigo-600">
                                        Mahasiswa dapat mengakses file ini.
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ asset('storage/' . $settings['sop_pdf_path']) }}"
                                target="_blank"
                                class="px-4 py-2 bg-white text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-200 hover:bg-indigo-600 hover:text-white transition"
                            >
                                Cek File
                            </a>
                        </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-200 rounded-3xl p-6 text-center hover:bg-gray-50 transition relative">

                        <input
                            type="file"
                            id="sop_input"
                            name="sop_pdf"
                            accept=".pdf"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            title="Klik untuk pilih file"
                        >

                        <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                ></path>
                            </svg>
                        </div>

                        <h4
                            id="sop_filename"
                            class="text-sm font-black text-gray-800 mb-1"
                        >
                            Upload SOP Baru
                        </h4>

                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Hanya PDF, Maksimal 5MB
                        </p>
                    </div>
                </div>


                <!-- ========================================================= -->
                <!-- MOU -->
                <!-- ========================================================= -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">

                    <h3 class="text-lg font-black text-gray-900 tracking-tight mb-2">
                        Teks Templat MoU
                    </h3>

                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Ubah isi teks perjanjian yang akan otomatis tercetak di PDF MoU mahasiswa.
                    </p>

                    <div class="space-y-4">

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                                MoU Internal Rental
                            </label>

                            <textarea
                                name="mou_internal"
                                rows="4"
                                class="w-full rounded-2xl border-gray-200 text-xs focus:ring-indigo-500 bg-gray-50 p-4"
                            >{{ $settings['mou_internal'] ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                                MoU Vendor Rental
                            </label>

                            <textarea
                                name="mou_vendor"
                                rows="4"
                                class="w-full rounded-2xl border-gray-200 text-xs focus:ring-indigo-500 bg-gray-50 p-4"
                            >{{ $settings['mou_vendor'] ?? '' }}</textarea>
                        </div>

                    </div>
                </div>


                <!-- ========================================================= -->
                <!-- COLOR CHART BAJU -->
                <!-- ========================================================= -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">

                    <div class="flex items-start justify-between gap-4 mb-2">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 tracking-tight">
                                Color Chart Baju
                            </h3>

                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Upload foto referensi warna yang akan digunakan mahasiswa saat memilih warna Baju.
                                Color Chart ini bersifat global dan hanya digunakan untuk Merchandise → Baju.
                            </p>
                        </div>

                        @php
                            $colorCharts = [];

                            if (isset($settings['baju_color_charts'])) {
                                $decodedColorCharts = json_decode(
                                    $settings['baju_color_charts'],
                                    true
                                );

                                if (is_array($decodedColorCharts)) {
                                    $colorCharts = $decodedColorCharts;
                                }
                            }
                        @endphp

                        <div class="shrink-0 px-3 py-2 bg-gray-100 rounded-xl">
                            <p class="text-[10px] font-black text-gray-600">
                                {{ count($colorCharts) }} File
                            </p>
                        </div>
                    </div>


                    <!-- FILE COLOR CHART YANG SUDAH ADA -->
                    @if(count($colorCharts) > 0)

                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                    Color Chart Tersimpan
                                </p>

                                <p class="text-[10px] font-bold text-gray-400">
                                    {{ count($colorCharts) }} gambar
                                </p>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">

                                @foreach($colorCharts as $index => $colorChart)

                                    <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">

                                        <div class="aspect-[4/3] bg-white">
                                            <img
                                                src="{{ asset('storage/' . $colorChart) }}"
                                                alt="Color Chart {{ $index + 1 }}"
                                                class="w-full h-full object-contain"
                                            >
                                        </div>

                                        <div class="px-3 py-2 border-t border-gray-200">
                                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-wider">
                                                Color Chart {{ $index + 1 }}
                                            </p>
                                        </div>

                                    </div>

                                @endforeach

                            </div>
                        </div>

                    @else

                        <div class="mt-6 p-5 bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                            <p class="text-xs font-bold text-gray-500 text-center">
                                Belum ada Color Chart yang di-upload.
                            </p>
                        </div>

                    @endif


                    <!-- UPLOAD COLOR CHART BARU -->
                    <div class="mt-6">

                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                            Upload Color Chart Baru
                        </label>

                        <div class="border-2 border-dashed border-gray-200 rounded-3xl p-6 text-center hover:bg-gray-50 transition">

                            <input
                                type="file"
                                id="color_charts_input"
                                name="color_charts[]"
                                accept="image/png,image/jpeg,image/webp"
                                multiple
                                class="w-full text-xs text-gray-500
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded-xl file:border-0
                                       file:text-[10px] file:font-black
                                       file:uppercase file:tracking-widest
                                       file:bg-gray-900 file:text-white
                                       hover:file:bg-indigo-600
                                       cursor-pointer"
                            >

                            <div class="mt-4">
                                <p
                                    id="color_charts_filename"
                                    class="text-xs font-black text-gray-700"
                                >
                                    Belum ada file dipilih
                                </p>

                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                    Bisa pilih banyak foto sekaligus • Maksimal 20 file • Maksimal 5MB per file
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- ========================================================= -->
                <!-- INVOICE -->
                <!-- ========================================================= -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">

                    <h3 class="text-lg font-black text-gray-900 tracking-tight mb-2">
                        Aset Gambar Invoice & PDF
                    </h3>

                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Upload Logo Student Council dan Tanda Tangan Bendahara yang akan dicetak di Invoice.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- LOGO -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                                Logo SC (Wajib PNG Transparan)
                            </label>

                            <input
                                type="file"
                                name="logo_sc"
                                accept="image/*"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-indigo-500 cursor-pointer"
                            >

                            @if(isset($settings['logo_sc']))
                                <p class="text-[10px] text-emerald-600 font-bold mt-2">
                                    ✓ Logo sudah tersimpan
                                </p>
                            @endif
                        </div>


                        <!-- TTD -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">
                                Tanda Tangan Bendahara (PNG)
                            </label>

                            <input
                                type="file"
                                name="ttd_bendahara"
                                accept="image/*"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-indigo-500 cursor-pointer"
                            >

                            @if(isset($settings['ttd_bendahara']))
                                <p class="text-[10px] text-emerald-600 font-bold mt-2">
                                    ✓ TTD sudah tersimpan
                                </p>
                            @endif
                        </div>

                    </div>
                </div>


                <!-- ========================================================= -->
                <!-- SAVE -->
                <!-- ========================================================= -->
                <div class="flex justify-end pb-8">

                    <button
                        type="submit"
                        class="px-10 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-xl shadow-gray-200"
                    >
                        Simpan Semua Pengaturan
                    </button>

                </div>

            </form>

        </div>
    </div>


    <!-- ========================================================= -->
    <!-- SCRIPT -->
    <!-- ========================================================= -->
    <script>
        // SOP file name
        const sopInput = document.getElementById('sop_input');
        const sopFilename = document.getElementById('sop_filename');

        if (sopInput) {
            sopInput.addEventListener('change', function (event) {
                if (event.target.files.length > 0) {
                    sopFilename.innerText =
                        "File Dipilih: " + event.target.files[0].name;

                    sopFilename.classList.add('text-indigo-600');
                }
            });
        }


        // Color Chart file names
        const colorChartsInput = document.getElementById('color_charts_input');
        const colorChartsFilename = document.getElementById('color_charts_filename');

        if (colorChartsInput) {
            colorChartsInput.addEventListener('change', function (event) {

                const files = event.target.files;

                if (files.length === 0) {
                    colorChartsFilename.innerText = 'Belum ada file dipilih';
                    return;
                }

                if (files.length === 1) {
                    colorChartsFilename.innerText =
                        '1 file dipilih: ' + files[0].name;
                } else {
                    colorChartsFilename.innerText =
                        files.length + ' file dipilih';
                }

                colorChartsFilename.classList.add('text-indigo-600');
            });
        }
    </script>

</x-app-layout>