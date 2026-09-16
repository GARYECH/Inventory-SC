<x-app-layout>

    @php

        /*
        |--------------------------------------------------------------------------
        | EXISTING SIZE DATA
        |--------------------------------------------------------------------------
        */

        $existingSizeData = [];

        if (!empty($prefillBreakdowns)) {

            foreach ($prefillBreakdowns as $breakdown) {

                $size = $breakdown['size'] ?? null;

                if (!$size) {
                    continue;
                }

                $existingSizeData[] = [
                    'size' => $size,
                    'division' => $breakdown['division'] ?? '',
                    'quantity' => (int) (
                        $breakdown['quantity'] ?? 0
                    ),
                ];

            }

        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION OLD INPUT
        |--------------------------------------------------------------------------
        */

        $oldSizes = old('sizes', []);

        if (
            is_array($oldSizes) &&
            !empty($oldSizes)
        ) {

            $existingSizeData = [];

            foreach ($oldSizes as $index => $data) {

                if (!is_array($data)) {
                    continue;
                }

                $size = $data['size'] ?? null;

                /*
                |--------------------------------------------------------------------------
                | BACKWARD COMPATIBILITY
                |--------------------------------------------------------------------------
                | Jika masih ada old input model lama:
                | sizes[M][division]
                | sizes[M][quantity]
                |--------------------------------------------------------------------------
                */

                if (!$size && is_string($index)) {
                    $size = $index;
                }

                if (!$size) {
                    continue;
                }

                $existingSizeData[] = [
                    'size' => $size,
                    'division' => $data['division'] ?? '',
                    'quantity' => (int) (
                        $data['quantity'] ?? 0
                    ),
                ];

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SIZE PRICE FOR JAVASCRIPT
        |--------------------------------------------------------------------------
        */

        $jsSizePrices = [];

        foreach ($sizeOptions as $size) {

            $jsSizePrices[$size] = (int) (
                $sizePrices[$size]
                ?? $item->price
            );

        }

    @endphp


    <div class="min-h-screen bg-[#F6F5F2] pb-16">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <header class="border-b border-[#E7E4DC] bg-[#FCFBF9]">

            <div class="mx-auto max-w-3xl px-5 py-6 sm:px-8">

                <div class="flex items-center gap-3">

                    <a
                        href="{{ route('student.dashboard') }}"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-[#E7E4DC] text-[#57534E] transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                    >

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />

                        </svg>

                    </a>


                    <div class="min-w-0 flex-1">

                        <p class="text-sm font-semibold text-indigo-700">
                            Merchandise Baju
                        </p>

                        <h1 class="mt-0.5 truncate text-2xl font-black leading-tight tracking-tight text-[#171412] sm:text-3xl">
                            {{ $editLineKey ? 'Edit detail pesanan' : 'Lengkapi detail pesanan' }}
                        </h1>

                    </div>

                </div>


                <!-- PROGRESS RAIL -->

                <div class="mt-6 flex items-center gap-2">

                    @foreach(['Produk', 'Jadwal', 'Ukuran', 'Desain'] as $stepLabel)

                        <div class="flex flex-1 items-center gap-2">

                            <span class="h-1.5 flex-1 rounded-full bg-indigo-600"></span>

                        </div>

                    @endforeach

                </div>

                <p class="mt-2 text-xs font-medium text-[#8A8478]">
                    Isi setiap bagian di bawah, lalu simpan ke keranjang.
                </p>

            </div>

        </header>



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <main class="mx-auto max-w-3xl px-5 pt-8 sm:px-8">


            <!-- ===================================================== -->
            <!-- FLASH -->
            <!-- ===================================================== -->

            @if(session('success'))

                <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500">

                        <svg
                            class="h-4 w-4 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />

                        </svg>

                    </div>


                    <p class="text-sm font-semibold text-emerald-900">
                        {{ session('success') }}
                    </p>

                </div>

            @endif


            @if(session('error'))

                <div class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500">

                        <svg
                            class="h-4 w-4 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c.98 0 1.54-1.06 1.05-1.91L13.05 4.91c-.47-.82-1.63-.82-2.1 0L3.89 16.09c-.49-.85.07-1.91 1.05-1.91z"
                            />

                        </svg>

                    </div>


                    <p class="text-sm font-semibold text-red-900">
                        {{ session('error') }}
                    </p>

                </div>

            @endif


            @if($errors->any())

                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-4">

                    <p class="text-sm font-bold text-red-700">
                        Periksa kembali data berikut
                    </p>


                    <div class="mt-2 space-y-1">

                        @foreach($errors->all() as $error)

                            <p class="text-sm font-medium text-red-700">
                                — {{ $error }}
                            </p>

                        @endforeach

                    </div>

                </div>

            @endif



            <form
                action="{{ route('student.cart.baju.store', $item->id) }}"
                method="POST"
            >

                @csrf


                @if($editLineKey)

                    <input
                        type="hidden"
                        name="edit_line_key"
                        value="{{ $editLineKey }}"
                    >

                @endif



                <!-- ===================================================== -->
                <!-- STEP 01 — PRODUCT -->
                <!-- ===================================================== -->

                <div class="flex gap-5">

                    <div class="flex flex-col items-center">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#171412] text-xs font-black text-white">
                            01
                        </span>

                        <span class="mt-2 w-px flex-1 bg-[#E7E4DC]"></span>

                    </div>


                    <section class="flex-1 pb-8">

                        <h2 class="text-lg font-black tracking-tight text-[#171412]">
                            Produk
                        </h2>

                        <p class="mt-1 text-sm text-[#8A8478]">
                            Item yang sedang kamu pesan.
                        </p>


                        <div class="mt-4 flex items-center gap-4 rounded-2xl border border-[#E7E4DC] bg-white p-4">

                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-[#F1EFEA] sm:h-20 sm:w-20">

                                @if($item->item_photo)

                                    <img
                                        src="{{ asset('storage/' . $item->item_photo) }}"
                                        alt="{{ $item->name }}"
                                        class="h-full w-full object-cover"
                                    >

                                @else

                                    <div class="flex h-full w-full items-center justify-center">

                                        <svg
                                            class="h-6 w-6 text-[#C4BFB2]"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            />

                                        </svg>

                                    </div>

                                @endif

                            </div>


                            <div class="min-w-0 flex-1">

                                <span class="inline-block rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">
                                    Baju
                                </span>


                                <h3 class="mt-2 truncate text-base font-black text-[#171412] sm:text-lg">
                                    {{ $item->name }}
                                </h3>


                                <p class="mt-0.5 text-sm text-[#8A8478]">

                                    Harga dasar

                                    <span class="font-bold text-indigo-700">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}/pcs
                                    </span>

                                </p>

                            </div>

                        </div>

                    </section>

                </div>



                <!-- ===================================================== -->
                <!-- STEP 02 — SCHEDULE -->
                <!-- ===================================================== -->

                <div class="flex gap-5">

                    <div class="flex flex-col items-center">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#171412] text-xs font-black text-white">
                            02
                        </span>

                        <span class="mt-2 w-px flex-1 bg-[#E7E4DC]"></span>

                    </div>


                    <section class="flex-1 pb-8">

                        <h2 class="text-lg font-black tracking-tight text-[#171412]">
                            Waktu transaksi
                        </h2>

                        <p class="mt-1 text-sm text-[#8A8478]">
                            Tentukan kapan pesanan ini diproses.
                        </p>


                        <div class="mt-4 grid grid-cols-1 gap-3 rounded-2xl border border-[#E7E4DC] bg-white p-4 sm:grid-cols-2">

                            <div>

                                <label
                                    for="start_date"
                                    class="mb-1.5 block text-sm font-semibold text-[#57534E]"
                                >
                                    Tanggal
                                </label>

                                <input
                                    id="start_date"
                                    type="date"
                                    name="start_date"
                                    value="{{ old('start_date', $prefillStartDate) }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    required
                                    class="w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3.5 py-3 text-sm font-semibold text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                            <div>

                                <label
                                    for="start_time"
                                    class="mb-1.5 block text-sm font-semibold text-[#57534E]"
                                >
                                    Jam
                                </label>

                                <select
                                    id="start_time"
                                    name="start_time"
                                    required
                                    class="w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3.5 py-3 text-sm font-semibold text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                >

                                    <option value="">
                                        Pilih jam
                                    </option>

                                    @foreach($timeOptions as $time)

                                        <option
                                            value="{{ $time }}"
                                            {{ old('start_time', $prefillStartTime) === $time ? 'selected' : '' }}
                                        >
                                            {{ $time }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </section>

                </div>



                <!-- ===================================================== -->
                <!-- STEP 03 — SIZE -->
                <!-- ===================================================== -->

                <div class="flex gap-5">

                    <div class="flex flex-col items-center">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#171412] text-xs font-black text-white">
                            03
                        </span>

                        <span class="mt-2 w-px flex-1 bg-[#E7E4DC]"></span>

                    </div>


                    <section class="flex-1 pb-8">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <h2 class="text-lg font-black tracking-tight text-[#171412]">
                                    Rincian ukuran
                                </h2>

                                <p class="mt-1 max-w-sm text-sm text-[#8A8478]">
                                    Tambahkan ukuran yang diperlukan. Ukuran yang sama boleh ditambahkan lebih dari satu kali.
                                </p>

                            </div>


                            <div class="hidden shrink-0 rounded-2xl bg-[#171412] px-4 py-3 text-right sm:block">

                                <p class="text-xs font-semibold text-[#A39D8F]">
                                    Total
                                </p>

                                <p
                                    id="totalQuantityTop"
                                    class="mt-0.5 text-base font-black text-white"
                                >
                                    0 pcs
                                </p>

                            </div>

                        </div>



                        <!-- ADD SIZE -->

                        <div class="mt-4 rounded-2xl border border-[#E7E4DC] bg-white p-4">

                            <div class="flex flex-col gap-2.5 sm:flex-row">

                                <div class="min-w-0 flex-1">

                                    <label
                                        for="sizeSelector"
                                        class="mb-1.5 block text-sm font-semibold text-[#57534E]"
                                    >
                                        Pilih ukuran
                                    </label>

                                    <select
                                        id="sizeSelector"
                                        class="w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3.5 py-3 text-sm font-semibold text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                    >

                                        <option value="">
                                            Pilih ukuran...
                                        </option>

                                        @foreach($sizeOptions as $size)

                                            <option value="{{ $size }}">

                                                {{ $size }}
                                                — Rp
                                                {{ number_format($sizePrices[$size], 0, ',', '.') }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <button
                                    type="button"
                                    id="addSizeButton"
                                    disabled
                                    class="inline-flex h-[48px] shrink-0 items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-[#E7E4DC] disabled:text-[#A39D8F] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 sm:mt-[26px]"
                                >

                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4"
                                        />

                                    </svg>

                                    Tambah ukuran

                                </button>

                            </div>

                        </div>



                        <!-- MOBILE SUMMARY -->

                        <div class="mt-3 flex items-center justify-between rounded-xl bg-white px-4 py-3 ring-1 ring-[#E7E4DC] sm:hidden">

                            <span class="text-sm font-semibold text-[#8A8478]">
                                Total quantity
                            </span>

                            <span
                                id="totalQuantityTopMobile"
                                class="text-sm font-black text-[#171412]"
                            >
                                0 pcs
                            </span>

                        </div>



                        <!-- EMPTY -->

                        <div
                            id="emptySizeState"
                            class="mt-4 rounded-2xl border border-dashed border-[#D9D5C9] px-4 py-10 text-center"
                        >

                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-[#F1EFEA]">

                                <svg
                                    class="h-4 w-4 text-[#A39D8F]"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />

                                </svg>

                            </div>


                            <p class="mt-3 text-sm font-bold text-[#57534E]">
                                Belum ada ukuran
                            </p>


                            <p class="mt-1 text-sm text-[#A39D8F]">
                                Pilih ukuran di atas untuk menambahkan baris.
                            </p>

                        </div>



                        <!-- ================================================= -->
                        <!-- SIZE LIST -->
                        <!-- ================================================= -->

                        <div
                            id="sizeRows"
                            class="mt-4 space-y-3"
                        >

                            @foreach($existingSizeData as $rowIndex => $existingData)

                                @php

                                    $existingSize =
                                        $existingData['size'];

                                    $existingUnitPrice =
                                        (int) (
                                            $sizePrices[
                                                $existingSize
                                            ]
                                            ??
                                            $item->price
                                        );

                                    $existingAdditionalPrice =
                                        $existingUnitPrice
                                        -
                                        (int) $item->price;

                                @endphp


                                <div
                                    class="size-row rounded-2xl border border-[#E7E4DC] bg-white p-4 transition hover:border-indigo-200"
                                    data-size="{{ $existingSize }}"
                                    data-unit-price="{{ $existingUnitPrice }}"
                                >

                                    <!-- HIDDEN SIZE -->

                                    <input
                                        type="hidden"
                                        class="size-value"
                                        name="sizes[{{ $rowIndex }}][size]"
                                        value="{{ $existingSize }}"
                                    >


                                    <!-- TOP: BADGE + PRICE INFO + REMOVE -->

                                    <div class="flex items-center justify-between gap-3">

                                        <div class="flex min-w-0 items-center gap-3">

                                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#171412] text-sm font-black text-white">
                                                {{ $existingSize }}
                                            </span>

                                            <div class="min-w-0">

                                                <p class="text-sm font-bold text-[#171412]">
                                                    Ukuran {{ $existingSize }}
                                                </p>

                                                <p class="mt-0.5 truncate text-xs font-medium text-[#8A8478]">

                                                    @if($existingAdditionalPrice > 0)
                                                        Harga dasar + Rp {{ number_format($existingAdditionalPrice, 0, ',', '.') }}
                                                    @else
                                                        Harga dasar
                                                    @endif

                                                    · Rp {{ number_format($existingUnitPrice, 0, ',', '.') }}/pcs

                                                </p>

                                            </div>

                                        </div>


                                        <button
                                            type="button"
                                            class="remove-size group flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-[#C4BFB2] transition hover:bg-red-50 hover:text-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-400"
                                            title="Hapus ukuran"
                                        >

                                            <svg
                                                class="h-4 w-4 transition group-hover:scale-110"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v-6m1-3V4a1 1 0 011-1h2a1 1 0 011 1v3M4 7h16"
                                                />

                                            </svg>

                                        </button>

                                    </div>


                                    <!-- BOTTOM: DIVISI / JUMLAH / SUBTOTAL -->

                                    <div class="mt-3 grid grid-cols-2 gap-3 border-t border-[#F1EFEA] pt-3 sm:grid-cols-[minmax(0,1fr)_110px_150px]">

                                        <div class="col-span-2 sm:col-span-1">

                                            <label class="mb-1.5 block text-xs font-semibold text-[#8A8478]">
                                                Divisi
                                            </label>

                                            <input
                                                type="text"
                                                name="sizes[{{ $rowIndex }}][division]"
                                                value="{{ $existingData['division'] }}"
                                                placeholder="Contoh: Event"
                                                required
                                                class="h-12 w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3.5 text-sm font-semibold text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                            >

                                        </div>


                                        <div>

                                            <label class="mb-1.5 block text-xs font-semibold text-[#8A8478]">
                                                Jumlah
                                            </label>

                                            <input
                                                type="number"
                                                name="sizes[{{ $rowIndex }}][quantity]"
                                                value="{{ $existingData['quantity'] }}"
                                                min="1"
                                                required
                                                class="size-quantity h-12 w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3 text-center text-sm font-black text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                            >

                                        </div>


                                        <div>

                                            <label class="mb-1.5 block text-xs font-semibold text-[#8A8478]">
                                                Subtotal
                                            </label>

                                            <div class="size-subtotal flex h-12 items-center justify-end rounded-xl bg-indigo-50 px-4 text-sm font-black tracking-tight text-indigo-700">
                                                Rp 0
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>



                        <!-- ================================================= -->
                        <!-- TOTAL -->
                        <!-- ================================================= -->

                        <div class="mt-4 grid grid-cols-2 gap-3">

                            <div class="rounded-2xl border border-[#E7E4DC] bg-white px-4 py-3.5">

                                <p class="text-xs font-semibold text-[#8A8478]">
                                    Total quantity
                                </p>

                                <p
                                    id="totalQuantity"
                                    class="mt-1 text-lg font-black text-[#171412]"
                                >
                                    0 pcs
                                </p>

                            </div>


                            <div class="rounded-2xl bg-[#171412] px-4 py-3.5">

                                <p class="text-xs font-semibold text-[#A39D8F]">
                                    Grand total
                                </p>

                                <p
                                    id="grandTotal"
                                    class="mt-1 text-lg font-black text-white"
                                >
                                    Rp 0
                                </p>

                            </div>

                        </div>

                    </section>

                </div>



                <!-- ===================================================== -->
                <!-- STEP 04 — DESIGN -->
                <!-- ===================================================== -->

                <div class="flex gap-5">

                    <div class="flex flex-col items-center">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#171412] text-xs font-black text-white">
                            04
                        </span>

                        <span class="mt-2 w-px flex-1 bg-[#E7E4DC]"></span>

                    </div>


                    <section class="flex-1 pb-8">

                        <h2 class="text-lg font-black tracking-tight text-[#171412]">
                            Link desain baju
                        </h2>

                        <p class="mt-1 text-sm text-[#8A8478]">
                            Tempel tautan Google Drive atau layanan serupa berisi file desain.
                        </p>


                        <div class="mt-4 rounded-2xl border border-[#E7E4DC] bg-white p-4">

                            <label
                                for="design_link"
                                class="mb-1.5 block text-sm font-semibold text-[#57534E]"
                            >
                                URL desain
                            </label>

                            <input
                                id="design_link"
                                type="url"
                                name="design_link"
                                value="{{ old('design_link', $prefillDesignLink) }}"
                                placeholder="https://drive.google.com/..."
                                required
                                class="w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3.5 py-3 text-sm font-semibold text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <div class="mt-3 flex items-start gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/60 px-4 py-3.5">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
                                />

                            </svg>


                            <p class="text-sm font-medium leading-relaxed text-indigo-900">
                                Warna baju dipilih setelah detail ini disimpan, di halaman keranjang.
                            </p>

                        </div>

                    </section>

                </div>



                <!-- ===================================================== -->
                <!-- STEP 05 — SUBMIT -->
                <!-- ===================================================== -->

                <div class="flex gap-5">

                    <div class="flex flex-col items-center">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white">
                            05
                        </span>

                    </div>


                    <section class="flex-1">

                        <h2 class="text-lg font-black tracking-tight text-[#171412]">
                            Simpan pesanan
                        </h2>

                        <p class="mt-1 text-sm text-[#8A8478]">
                            Periksa kembali total sebelum menyimpan ke keranjang.
                        </p>


                        <div class="mt-4 rounded-2xl border border-[#E7E4DC] bg-white p-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <p class="text-xs font-semibold text-[#8A8478]">
                                        Total pesanan
                                    </p>

                                    <p
                                        id="bottomGrandTotal"
                                        class="mt-1 text-3xl font-black tracking-tight text-[#171412]"
                                    >
                                        Rp 0
                                    </p>

                                </div>


                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 sm:w-auto sm:min-w-[220px]"
                                >

                                    {{ $editLineKey
                                        ? 'Simpan perubahan'
                                        : 'Simpan ke keranjang'
                                    }}


                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                                        />

                                    </svg>

                                </button>

                            </div>

                        </div>

                    </section>

                </div>

            </form>

        </main>

    </div>



    <!-- ============================================================= -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================= -->

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const sizePrices =
                    @json($jsSizePrices);


                const basePrice =
                    Number(
                        {{ (int) $item->price }}
                    );


                const sizeSelector =
                    document.getElementById(
                        'sizeSelector'
                    );


                const addSizeButton =
                    document.getElementById(
                        'addSizeButton'
                    );


                const sizeRows =
                    document.getElementById(
                        'sizeRows'
                    );


                const emptySizeState =
                    document.getElementById(
                        'emptySizeState'
                    );


                const totalQuantity =
                    document.getElementById(
                        'totalQuantity'
                    );


                const totalQuantityTop =
                    document.getElementById(
                        'totalQuantityTop'
                    );


                const totalQuantityTopMobile =
                    document.getElementById(
                        'totalQuantityTopMobile'
                    );


                const grandTotal =
                    document.getElementById(
                        'grandTotal'
                    );


                const bottomGrandTotal =
                    document.getElementById(
                        'bottomGrandTotal'
                    );


                /*
                |--------------------------------------------------------------------------
                | NEXT INDEX
                |--------------------------------------------------------------------------
                */

                let nextRowIndex =
                    sizeRows.querySelectorAll(
                        '.size-row'
                    ).length;



                /*
                |--------------------------------------------------------------------------
                | FORMAT RUPIAH
                |--------------------------------------------------------------------------
                */

                function formatRupiah(number) {

                    return new Intl.NumberFormat(
                        'id-ID'
                    ).format(
                        number
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | UPDATE ADD BUTTON
                |--------------------------------------------------------------------------
                */

                function updateAddButton() {

                    addSizeButton.disabled =
                        sizeSelector.value === '';

                }



                /*
                |--------------------------------------------------------------------------
                | CALCULATE TOTAL
                |--------------------------------------------------------------------------
                */

                function calculate() {

                    let quantityTotal = 0;

                    let priceTotal = 0;


                    const rows =
                        sizeRows.querySelectorAll(
                            '.size-row'
                        );


                    rows.forEach(
                        function (row) {

                            const quantityInput =
                                row.querySelector(
                                    '.size-quantity'
                                );


                            const subtotalElement =
                                row.querySelector(
                                    '.size-subtotal'
                                );


                            if (
                                !quantityInput ||
                                !subtotalElement
                            ) {
                                return;
                            }


                            const quantity =
                                Math.max(
                                    0,
                                    parseInt(
                                        quantityInput.value ||
                                        0,
                                        10
                                    )
                                );


                            const unitPrice =
                                Number(
                                    row.dataset.unitPrice ||
                                    0
                                );


                            const subtotal =
                                quantity *
                                unitPrice;


                            quantityTotal +=
                                quantity;


                            priceTotal +=
                                subtotal;


                            subtotalElement.textContent =
                                'Rp ' +
                                formatRupiah(
                                    subtotal
                                );

                        }
                    );


                    const quantityText =
                        quantityTotal +
                        ' pcs';


                    const priceText =
                        'Rp ' +
                        formatRupiah(
                            priceTotal
                        );


                    totalQuantity.textContent =
                        quantityText;


                    if (totalQuantityTop) {

                        totalQuantityTop.textContent =
                            quantityText;

                    }


                    if (totalQuantityTopMobile) {

                        totalQuantityTopMobile.textContent =
                            quantityText;

                    }


                    grandTotal.textContent =
                        priceText;


                    bottomGrandTotal.textContent =
                        priceText;


                    emptySizeState.classList.toggle(
                        'hidden',
                        rows.length > 0
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | ATTACH ROW EVENTS
                |--------------------------------------------------------------------------
                */

                function attachRow(row) {

                    const quantityInput =
                        row.querySelector(
                            '.size-quantity'
                        );


                    const removeButton =
                        row.querySelector(
                            '.remove-size'
                        );


                    if (quantityInput) {

                        quantityInput.addEventListener(
                            'input',
                            calculate
                        );


                        quantityInput.addEventListener(
                            'change',
                            calculate
                        );

                    }


                    if (removeButton) {

                        removeButton.addEventListener(
                            'click',
                            function () {

                                row.remove();

                                calculate();

                            }
                        );

                    }

                }



                /*
                |--------------------------------------------------------------------------
                | ADD SIZE
                |--------------------------------------------------------------------------
                */

                function addSize(size) {

                    if (!size) {
                        return;
                    }


                    const unitPrice =
                        Number(
                            sizePrices[size] ||
                            basePrice
                        );


                    const additionalPrice =
                        unitPrice -
                        basePrice;


                    const currentIndex =
                        nextRowIndex;


                    nextRowIndex++;


                    const row =
                        document.createElement(
                            'div'
                        );


                    row.className =
                        'size-row rounded-2xl border border-[#E7E4DC] bg-white p-4 transition hover:border-indigo-200';


                    row.dataset.size =
                        size;


                    row.dataset.unitPrice =
                        unitPrice;


                    row.innerHTML = `

                        <input
                            type="hidden"
                            class="size-value"
                            name="sizes[${currentIndex}][size]"
                            value="${size}"
                        >


                        <div class="flex items-center justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-3">

                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#171412] text-sm font-black text-white">
                                    ${size}
                                </span>

                                <div class="min-w-0">

                                    <p class="text-sm font-bold text-[#171412]">
                                        Ukuran ${size}
                                    </p>

                                    <p class="mt-0.5 truncate text-xs font-medium text-[#8A8478]">

                                        ${
                                            additionalPrice > 0
                                                ? 'Harga dasar + Rp ' +
                                                  formatRupiah(
                                                      additionalPrice
                                                  )
                                                : 'Harga dasar'
                                        }

                                        &middot; Rp ${formatRupiah(unitPrice)}/pcs

                                    </p>

                                </div>

                            </div>


                            <button
                                type="button"
                                class="remove-size group flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-[#C4BFB2] transition hover:bg-red-50 hover:text-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-400"
                                title="Hapus ukuran"
                            >

                                <svg
                                    class="h-4 w-4 transition group-hover:scale-110"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v-6m1-3V4a1 1 0 011-1h2a1 1 0 011 1v3M4 7h16"
                                    />

                                </svg>

                            </button>

                        </div>


                        <div class="mt-3 grid grid-cols-2 gap-3 border-t border-[#F1EFEA] pt-3 sm:grid-cols-[minmax(0,1fr)_110px_150px]">

                            <div class="col-span-2 sm:col-span-1">

                                <label class="mb-1.5 block text-xs font-semibold text-[#8A8478]">
                                    Divisi
                                </label>

                                <input
                                    type="text"
                                    name="sizes[${currentIndex}][division]"
                                    placeholder="Contoh: Event"
                                    required
                                    class="h-12 w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3.5 text-sm font-semibold text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-[#8A8478]">
                                    Jumlah
                                </label>

                                <input
                                    type="number"
                                    name="sizes[${currentIndex}][quantity]"
                                    value="1"
                                    min="1"
                                    required
                                    class="size-quantity h-12 w-full rounded-xl border border-[#E7E4DC] bg-[#FAF9F6] px-3 text-center text-sm font-black text-[#171412] outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-[#8A8478]">
                                    Subtotal
                                </label>

                                <div class="size-subtotal flex h-12 items-center justify-end rounded-xl bg-indigo-50 px-4 text-sm font-black tracking-tight text-indigo-700">
                                    Rp 0
                                </div>

                            </div>

                        </div>

                    `;


                    sizeRows.appendChild(
                        row
                    );


                    attachRow(
                        row
                    );


                    sizeSelector.value = '';

                    updateAddButton();

                    calculate();

                }



                /*
                |--------------------------------------------------------------------------
                | ADD BUTTON
                |--------------------------------------------------------------------------
                */

                addSizeButton.addEventListener(
                    'click',
                    function () {

                        addSize(
                            sizeSelector.value
                        );

                    }
                );



                /*
                |--------------------------------------------------------------------------
                | SIZE SELECTOR
                |--------------------------------------------------------------------------
                */

                sizeSelector.addEventListener(
                    'change',
                    function () {

                        updateAddButton();

                    }
                );



                /*
                |--------------------------------------------------------------------------
                | EXISTING ROWS
                |--------------------------------------------------------------------------
                */

                sizeRows
                    .querySelectorAll(
                        '.size-row'
                    )
                    .forEach(
                        function (row) {

                            attachRow(
                                row
                            );

                        }
                    );



                /*
                |--------------------------------------------------------------------------
                | INITIAL STATE
                |--------------------------------------------------------------------------
                */

                updateAddButton();

                calculate();

            }
        );

    </script>

</x-app-layout>
