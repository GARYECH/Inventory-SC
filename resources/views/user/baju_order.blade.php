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

                $size =
                    $breakdown['size'] ?? null;

                if (!$size) {
                    continue;
                }

                $existingSizeData[$size] = [
                    'division' =>
                        $breakdown['division'] ?? '',

                    'quantity' =>
                        (int) (
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

        $oldSizes =
            old(
                'sizes',
                []
            );

        if (
            is_array($oldSizes) &&
            !empty($oldSizes)
        ) {

            $existingSizeData = [];

            foreach (
                $oldSizes as $size => $data
            ) {

                $existingSizeData[$size] = [
                    'division' =>
                        $data['division'] ?? '',

                    'quantity' =>
                        (int) (
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

            $jsSizePrices[$size] =
                (int) (
                    $sizePrices[$size]
                    ??
                    $item->price
                );

        }

    @endphp


    <div class="min-h-screen bg-[#f7f8fc] pb-12">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <header class="border-b border-gray-200/70 bg-white">

            <div class="mx-auto max-w-4xl px-4 py-5 sm:px-6 lg:px-8">

                <div class="flex items-center gap-3">

                    <a
                        href="{{ route('student.dashboard') }}"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 text-gray-500 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
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

                        <p class="text-[8px] font-black uppercase tracking-[0.22em] text-indigo-600">
                            Merchandise · Baju
                        </p>

                        <h1 class="mt-0.5 truncate text-xl font-black tracking-tight text-gray-950 sm:text-2xl">
                            {{ $editLineKey ? 'Edit Detail Baju' : 'Detail Pesanan Baju' }}
                        </h1>

                    </div>

                </div>

            </div>

        </header>



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <main class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-8">


            <!-- ===================================================== -->
            <!-- FLASH -->
            <!-- ===================================================== -->

            @if(session('success'))

                <div class="mb-4 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3.5">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500">

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

                    <p class="text-xs font-bold text-emerald-800">
                        {{ session('success') }}
                    </p>

                </div>

            @endif


            @if(session('error'))

                <div class="mb-4 flex items-center gap-3 rounded-2xl border border-red-100 bg-red-50 px-4 py-3.5">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-500">

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

                    <p class="text-xs font-bold text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            @endif


            @if($errors->any())

                <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-4">

                    <p class="text-[8px] font-black uppercase tracking-widest text-red-600">
                        Periksa kembali data
                    </p>

                    <div class="mt-2 space-y-1">

                        @foreach($errors->all() as $error)

                            <p class="text-xs font-bold text-red-700">
                                • {{ $error }}
                            </p>

                        @endforeach

                    </div>

                </div>

            @endif



            <form
                action="{{ route('student.cart.baju.store', $item->id) }}"
                method="POST"
                class="space-y-4"
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
                <!-- PRODUCT -->
                <!-- ===================================================== -->

                <section class="rounded-3xl border border-gray-200/80 bg-white p-4 shadow-sm sm:p-5">

                    <div class="flex items-center gap-4">

                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-gray-100 sm:h-20 sm:w-20">

                            @if($item->item_photo)

                                <img
                                    src="{{ asset('storage/' . $item->item_photo) }}"
                                    alt="{{ $item->name }}"
                                    class="h-full w-full object-cover"
                                >

                            @else

                                <div class="flex h-full w-full items-center justify-center">

                                    <svg
                                        class="h-6 w-6 text-gray-300"
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

                            <div class="flex flex-wrap gap-1.5">

                                <span class="rounded-md bg-indigo-50 px-2 py-1 text-[7px] font-black uppercase tracking-widest text-indigo-600">
                                    Baju
                                </span>

                                <span class="rounded-md bg-gray-100 px-2 py-1 text-[7px] font-black uppercase tracking-widest text-gray-500">
                                    Merchandise
                                </span>

                            </div>


                            <h2 class="mt-2 truncate text-base font-black text-gray-950 sm:text-lg">
                                {{ $item->name }}
                            </h2>


                            <p class="mt-0.5 text-[10px] font-bold text-gray-400">

                                Harga dasar

                                <span class="font-black text-indigo-600">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}/pcs
                                </span>

                            </p>

                        </div>

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- SCHEDULE -->
                <!-- ===================================================== -->

                <section class="rounded-3xl border border-gray-200/80 bg-white p-4 shadow-sm sm:p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-[8px] font-black uppercase tracking-[0.2em] text-indigo-600">
                                Jadwal
                            </p>

                            <h2 class="mt-1 text-base font-black tracking-tight text-gray-950 sm:text-lg">
                                Waktu transaksi
                            </h2>

                        </div>


                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-50">

                            <svg
                                class="h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12"
                                />

                            </svg>

                        </div>

                    </div>


                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">

                        <div>

                            <label
                                for="start_date"
                                class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400"
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
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-3 text-xs font-black text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <div>

                            <label
                                for="start_time"
                                class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400"
                            >
                                Jam
                            </label>

                            <select
                                id="start_time"
                                name="start_time"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-3 text-xs font-black text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
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



                <!-- ===================================================== -->
                <!-- SIZE -->
                <!-- ===================================================== -->

                <section class="rounded-3xl border border-gray-200/80 bg-white p-4 shadow-sm sm:p-5">

                    <!-- HEADER -->

                    <div class="flex items-start justify-between gap-4">

                        <div>

                            <p class="text-[8px] font-black uppercase tracking-[0.2em] text-indigo-600">
                                Ukuran
                            </p>

                            <h2 class="mt-1 text-base font-black tracking-tight text-gray-950 sm:text-lg">
                                Rincian ukuran
                            </h2>

                            <p class="mt-1 text-[10px] font-medium leading-relaxed text-gray-400 sm:text-xs">
                                Tambahkan hanya ukuran yang diperlukan.
                            </p>

                        </div>


                        <div class="hidden rounded-xl bg-gray-950 px-3 py-2.5 text-right sm:block">

                            <p class="text-[7px] font-black uppercase tracking-widest text-gray-500">
                                Total
                            </p>

                            <p
                                id="totalQuantityTop"
                                class="mt-0.5 text-sm font-black text-white"
                            >
                                0 pcs
                            </p>

                        </div>

                    </div>



                    <!-- ADD SIZE -->

                    <div class="mt-4 rounded-2xl bg-gray-50 p-3">

                        <div class="flex flex-col gap-2 sm:flex-row">

                            <div class="min-w-0 flex-1">

                                <label
                                    for="sizeSelector"
                                    class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400"
                                >
                                    Pilih ukuran
                                </label>

                                <select
                                    id="sizeSelector"
                                    class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-3 text-xs font-black text-gray-800 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                                >

                                    <option value="">
                                        Pilih ukuran...
                                    </option>

                                    @foreach($sizeOptions as $size)

                                        <option value="{{ $size }}">

                                            {{ $size }}

                                            · Rp
                                            {{ number_format($sizePrices[$size], 0, ',', '.') }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <button
                                type="button"
                                id="addSizeButton"
                                disabled
                                class="inline-flex h-[45px] shrink-0 items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-5 text-[8px] font-black uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300 sm:mt-[21px]"
                            >

                                <svg
                                    class="h-3.5 w-3.5"
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

                                Tambah

                            </button>

                        </div>

                    </div>



                    <!-- MOBILE SUMMARY -->

                    <div class="mt-3 flex items-center justify-between rounded-xl bg-gray-50 px-3 py-2.5 sm:hidden">

                        <span class="text-[7px] font-black uppercase tracking-widest text-gray-400">
                            Total Quantity
                        </span>

                        <span
                            id="totalQuantityTopMobile"
                            class="text-xs font-black text-gray-900"
                        >
                            0 pcs
                        </span>

                    </div>



                    <!-- EMPTY -->

                    <div
                        id="emptySizeState"
                        class="mt-4 rounded-2xl border border-dashed border-gray-200 px-4 py-8 text-center"
                    >

                        <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-gray-100">

                            <svg
                                class="h-4 w-4 text-gray-400"
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


                        <p class="mt-2 text-[10px] font-black text-gray-500">
                            Belum ada ukuran
                        </p>


                        <p class="mt-0.5 text-[9px] font-medium text-gray-400">
                            Pilih ukuran di atas untuk menambahkan.
                        </p>

                    </div>



                    <!-- ================================================= -->
                    <!-- SIZE LIST -->
                    <!-- ================================================= -->

                    <div
                        id="sizeRows"
                        class="mt-4 space-y-2.5"
                    >

                        @foreach($existingSizeData as $existingSize => $existingData)

                            @php

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
                                class="size-row rounded-2xl border border-gray-200 bg-white px-3 py-3 transition hover:border-indigo-200 hover:bg-indigo-50/20 sm:px-4"
                                data-size="{{ $existingSize }}"
                                data-unit-price="{{ $existingUnitPrice }}"
                            >

                                <!-- DESKTOP / TOP ROW -->

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[58px_minmax(0,1fr)_92px_125px_32px] sm:items-end">

                                    <!-- SIZE -->

                                    <div>

                                        <p class="mb-1.5 text-[7px] font-black uppercase tracking-widest text-gray-300 sm:hidden">
                                            Ukuran
                                        </p>

                                        <div class="flex items-center gap-2">

                                            <span class="flex h-10 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-950 text-[9px] font-black text-white">
                                                {{ $existingSize }}
                                            </span>

                                        </div>

                                    </div>



                                    <!-- DIVISION -->

                                    <div>

                                        <label class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400">
                                            Divisi
                                        </label>

                                        <input
                                            type="text"
                                            name="sizes[{{ $existingSize }}][division]"
                                            value="{{ $existingData['division'] }}"
                                            placeholder="Contoh: Event"
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-bold text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                        >

                                    </div>



                                    <!-- QUANTITY -->

                                    <div>

                                        <label class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400">
                                            Jumlah
                                        </label>

                                        <input
                                            type="number"
                                            name="sizes[{{ $existingSize }}][quantity]"
                                            value="{{ $existingData['quantity'] }}"
                                            min="0"
                                            class="size-quantity w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-xs font-black text-gray-900 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                        >

                                    </div>



                                    <!-- SUBTOTAL -->

                                    <div>

                                        <label class="mb-3 block text-[7px] font-black uppercase tracking-widest text-gray-400">
                                            Subtotal
                                        </label>

                                       <div class="size-subtotal flex h-[50px] items-center justify-end rounded-xl bg-indigo-50 px-4 text-sm font-black tracking-tight text-indigo-600">
    Rp 0
</div>

                                    </div>



                                    <!-- REMOVE -->

                                    <div class="flex items-end">

                                        <button
                                            type="button"
                                            class="remove-size flex h-[42px] w-full items-center justify-center rounded-xl border border-gray-200 text-gray-300 transition hover:border-red-100 hover:bg-red-50 hover:text-red-500 sm:w-8"
                                            title="Hapus ukuran"
                                        >

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 01-1 1v3M4 7h16"
                                                />

                                            </svg>

                                        </button>

                                    </div>

                                </div>


                                <!-- PRICE NOTE -->

                                <div class="mt-2 flex items-center justify-between gap-3 pl-0 sm:pl-[58px]">

                                    <span class="text-[7px] font-bold text-gray-400">

                                        @if($existingAdditionalPrice > 0)

                                            Harga dasar
                                            +
                                            Rp {{ number_format($existingAdditionalPrice, 0, ',', '.') }}

                                        @else

                                            Harga dasar

                                        @endif

                                    </span>


                                    <span class="text-[7px] font-black text-gray-400">

                                        Rp
                                        {{ number_format($existingUnitPrice, 0, ',', '.') }}/pcs

                                    </span>

                                </div>

                            </div>

                        @endforeach

                    </div>



                    <!-- ================================================= -->
                    <!-- TOTAL -->
                    <!-- ================================================= -->

                    <div class="mt-4 grid grid-cols-2 gap-2.5">

                        <div class="rounded-2xl bg-gray-50 px-4 py-3.5">

                            <p class="text-[7px] font-black uppercase tracking-widest text-gray-400">
                                Total Quantity
                            </p>

                            <p
                                id="totalQuantity"
                                class="mt-1 text-lg font-black text-gray-950"
                            >
                                0 pcs
                            </p>

                        </div>


                        <div class="rounded-2xl bg-gray-950 px-4 py-3.5">

                            <p class="text-[7px] font-black uppercase tracking-widest text-gray-500">
                                Grand Total
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



                <!-- ===================================================== -->
                <!-- DESIGN -->
                <!-- ===================================================== -->

                <section class="rounded-3xl border border-gray-200/80 bg-white p-4 shadow-sm sm:p-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

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
                                    d="M13.828 10.172a4 4 0 010 5.656l-1.414 1.414a4 4 0 01-5.656-5.656l1.414-1.414m4.242-4.242a4 4 0 015.656 5.656l-1.414 1.414a4 4 0 01-5.656 0"
                                />

                            </svg>

                        </div>


                        <div>

                            <p class="text-[7px] font-black uppercase tracking-[0.2em] text-indigo-600">
                                Design
                            </p>

                            <h2 class="mt-0.5 text-base font-black text-gray-950">
                                Link Design Baju
                            </h2>

                        </div>

                    </div>


                    <input
                        id="design_link"
                        type="url"
                        name="design_link"
                        value="{{ old('design_link', $prefillDesignLink) }}"
                        placeholder="https://drive.google.com/..."
                        required
                        class="mt-4 w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-3 text-xs font-bold text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                    >

                </section>



                <!-- ===================================================== -->
                <!-- INFORMATION -->
                <!-- ===================================================== -->

                <div class="flex items-start gap-3 rounded-2xl border border-purple-100 bg-purple-50 px-4 py-3.5">

                    <svg
                        class="mt-0.5 h-4 w-4 shrink-0 text-purple-500"
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


                    <p class="text-[10px] font-bold leading-relaxed text-purple-800">

                        Warna Baju dipilih setelah detail ini disimpan di halaman
                        <span class="font-black">
                            Cart
                        </span>.

                    </p>

                </div>



                <!-- ===================================================== -->
                <!-- SUBMIT -->
                <!-- ===================================================== -->

                <div class="rounded-3xl border border-gray-200/80 bg-white p-4 shadow-sm sm:p-5">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-[7px] font-black uppercase tracking-widest text-gray-400">
                                Total Pesanan
                            </p>

                            <p
                                id="bottomGrandTotal"
                                class="mt-1 text-2xl font-black tracking-tight text-gray-950"
                            >
                                Rp 0
                            </p>

                        </div>


                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gray-950 px-6 py-4 text-[9px] font-black uppercase tracking-[0.14em] text-white shadow-lg transition hover:bg-indigo-600 sm:w-auto sm:min-w-[220px]"
                        >

                            {{ $editLineKey
                                ? 'Simpan Perubahan'
                                : 'Simpan ke Keranjang'
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


                function formatRupiah(
                    number
                ) {

                    return new Intl.NumberFormat(
                        'id-ID'
                    ).format(
                        number
                    );

                }


                function getSelectedSizes() {

                    const rows =
                        sizeRows.querySelectorAll(
                            '.size-row'
                        );


                    const sizes = [];


                    rows.forEach(
                        function (row) {

                            if (
                                row.dataset.size
                            ) {

                                sizes.push(
                                    row.dataset.size
                                );

                            }

                        }
                    );


                    return sizes;

                }


                function updateSelector() {

                    const selectedSizes =
                        getSelectedSizes();


                    Array.from(
                        sizeSelector.options
                    ).forEach(
                        function (option) {

                            if (!option.value) {
                                return;
                            }


                            option.disabled =
                                selectedSizes.includes(
                                    option.value
                                );

                        }
                    );


                    if (
                        selectedSizes.includes(
                            sizeSelector.value
                        )
                    ) {

                        sizeSelector.value = '';

                    }


                    addSizeButton.disabled =
                        sizeSelector.value === '';

                }


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
                                        quantityInput.value
                                        ||
                                        0,
                                        10
                                    )
                                );


                            const unitPrice =
                                Number(
                                    row.dataset.unitPrice
                                    ||
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


                function attachRow(
                    row
                ) {

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

                                updateSelector();

                                calculate();

                            }
                        );

                    }

                }


                function addSize(
                    size
                ) {

                    if (!size) {
                        return;
                    }


                    if (
                        getSelectedSizes()
                            .includes(size)
                    ) {

                        return;

                    }


                    const unitPrice =
                        Number(
                            sizePrices[size]
                            ||
                            0
                        );


                    const basePrice =
                        Number(
                            {{ (int) $item->price }}
                        );


                    const additionalPrice =
                        unitPrice -
                        basePrice;


                    const row =
                        document.createElement(
                            'div'
                        );


                    row.className =
                        'size-row rounded-2xl border border-gray-200 bg-white px-3 py-3 transition hover:border-indigo-200 hover:bg-indigo-50/20 sm:px-4';


                    row.dataset.size =
                        size;


                    row.dataset.unitPrice =
                        unitPrice;


                    row.innerHTML = `

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-[58px_minmax(0,1fr)_92px_125px_32px] sm:items-end">

                            <div>

                                <p class="mb-1.5 text-[7px] font-black uppercase tracking-widest text-gray-300 sm:hidden">
                                    Ukuran
                                </p>

                                <span class="flex h-10 w-12 items-center justify-center rounded-xl bg-gray-950 text-[9px] font-black text-white">
                                    ${size}
                                </span>

                            </div>


                            <div>

                                <label class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400">
                                    Divisi
                                </label>

                                <input
                                    type="text"
                                    name="sizes[${size}][division]"
                                    placeholder="Contoh: Event"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-bold text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                            <div>

                                <label class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400">
                                    Jumlah
                                </label>

                                <input
                                    type="number"
                                    name="sizes[${size}][quantity]"
                                    value="0"
                                    min="0"
                                    class="size-quantity w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-xs font-black text-gray-900 outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                            <div>

                                <label class="mb-1.5 block text-[7px] font-black uppercase tracking-widest text-gray-400">
                                    Subtotal
                                </label>

                                <div class="size-subtotal flex h-[42px] items-center justify-end rounded-xl bg-indigo-50 px-3 text-[10px] font-black text-indigo-600">
                                    Rp 0
                                </div>

                            </div>


                            <div class="flex items-end">

                                <button
                                    type="button"
                                    class="remove-size flex h-[42px] w-full items-center justify-center rounded-xl border border-gray-200 text-gray-300 transition hover:border-red-100 hover:bg-red-50 hover:text-red-500 sm:w-8"
                                    title="Hapus ukuran"
                                >

                                    <svg
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 01-1 1v3M4 7h16"
                                        />

                                    </svg>

                                </button>

                            </div>

                        </div>


                        <div class="mt-2 flex items-center justify-between gap-3 sm:pl-[58px]">

                            <span class="text-[7px] font-bold text-gray-400">

                                ${
                                    additionalPrice > 0
                                        ? '+Rp ' +
                                          formatRupiah(
                                              additionalPrice
                                          )
                                        : 'Harga dasar'
                                }

                            </span>


                            <span class="text-[7px] font-black text-gray-400">

                                Rp
                                ${formatRupiah(unitPrice)}/pcs

                            </span>

                        </div>

                    `;


                    sizeRows.appendChild(
                        row
                    );


                    attachRow(
                        row
                    );


                    updateSelector();

                    calculate();

                }


                addSizeButton.addEventListener(
                    'click',
                    function () {

                        addSize(
                            sizeSelector.value
                        );

                    }
                );


                sizeSelector.addEventListener(
                    'change',
                    function () {

                        addSizeButton.disabled =
                            sizeSelector.value === '';

                    }
                );


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


                updateSelector();

                calculate();

            }
        );

    </script>

</x-app-layout>