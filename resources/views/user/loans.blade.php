<x-app-layout>

    <div class="min-h-screen bg-[#f8f9fa] pb-12">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 shadow-sm backdrop-blur-xl">

            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">


                    <!-- BRAND -->

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg shadow-indigo-200">

                            <svg
                                class="h-6 w-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />

                            </svg>

                        </div>


                        <div>

                            <h1 class="text-xl font-black tracking-tight text-gray-950 sm:text-2xl">
                                Checkout Request
                            </h1>

                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 sm:text-[10px]">
                                Review & Confirm Your Items
                            </p>

                        </div>

                    </div>



                    <!-- BACK -->

                    <div>

                        <a
                            href="{{ route('student.dashboard') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-[9px] font-black uppercase tracking-widest text-gray-700 transition-all hover:bg-gray-50 hover:text-indigo-600 sm:w-auto sm:px-6"
                        >

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                />

                            </svg>

                            Kembali ke Katalog

                        </a>

                    </div>

                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">


            <!-- ===================================================== -->
            <!-- FLASH ERROR -->
            <!-- ===================================================== -->

            @if(session('error'))

                <div class="mb-6 flex items-center gap-4 rounded-2xl border border-red-100 bg-red-50 px-5 py-4 shadow-sm">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-500">

                        <svg
                            class="h-4 w-4 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c.98 0 1.54-1.06 1.05-1.91L13.05 4.91c-.47-.82-1.63-.82-2.1 0L3.89 16.09c-.49.85.07 1.91 1.05 1.91z"
                            />

                        </svg>

                    </div>


                    <p class="text-sm font-bold text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- FLASH SUCCESS -->
            <!-- ===================================================== -->

            @if(session('success'))

                <div class="mb-6 flex items-center gap-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 shadow-sm">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-500">

                        <svg
                            class="h-4 w-4 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />

                        </svg>

                    </div>


                    <p class="text-sm font-bold text-emerald-800">
                        {{ session('success') }}
                    </p>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- EMPTY CART -->
            <!-- ===================================================== -->

            @if(empty($cart))

                <div class="rounded-[2.5rem] border border-gray-100 bg-white px-6 py-28 text-center shadow-sm">

                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-gray-100 bg-gray-50 text-gray-300 shadow-inner">

                        <svg
                            class="h-12 w-12"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                            />

                        </svg>

                    </div>


                    <h3 class="text-2xl font-black tracking-tight text-gray-900">
                        Keranjangmu Masih Kosong!
                    </h3>


                    <p class="mx-auto mt-2 max-w-md text-sm font-bold text-gray-400">
                        Ayo pilih barang untuk proker atau kebutuhanmu terlebih dahulu.
                    </p>


                    <a
                        href="{{ route('student.dashboard') }}"
                        class="mt-8 inline-flex items-center rounded-2xl bg-indigo-600 px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-indigo-200 transition-all hover:bg-indigo-700"
                    >

                        Jelajahi Katalog

                    </a>

                </div>

            @else


                @php

                    /*
                    |--------------------------------------------------------------------------
                    | FINAL TRANSACTION TYPES
                    |--------------------------------------------------------------------------
                    */

                    $allowedTransactionTypes = [
                        'Peralatan',
                        'Handy Talkie',
                        'Habis Pakai',
                        'Merchandise',
                    ];


                    /*
                    |--------------------------------------------------------------------------
                    | SUMMARY
                    |--------------------------------------------------------------------------
                    */

                    $totalPrice = 0;

                    $hasEquipment = false;

                    $hasHandyTalkie = false;

                    $hasConsumable = false;

                    $hasMerchandise = false;

                    $transactionTypes = [];

                    $mouTypes = [];


                    /*
                    |--------------------------------------------------------------------------
                    | BAJU COLOR
                    |--------------------------------------------------------------------------
                    */

                    $bajuColorsByItem = [];


                    foreach ($cart as $details) {

                        $transactionType =
                            $details['transaction_type'] ?? '';

                        $transactionDetail =
                            $details['transaction_detail'] ?? null;

                        $subcategory =
                            $details['subcategory'] ?? null;


                        /*
                        |--------------------------------------------------------------------------
                        | BAJU FLAG
                        |--------------------------------------------------------------------------
                        */

                        $isBaju =
                            $transactionType ===
                            'Merchandise' &&
                            $subcategory ===
                            'Baju';


                        /*
                        |--------------------------------------------------------------------------
                        | TYPES
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $transactionType ===
                            'Peralatan'
                        ) {

                            $hasEquipment = true;

                            $transactionTypes['Peralatan'] =
                                true;

                        }


                        if (
                            $transactionType ===
                            'Handy Talkie'
                        ) {

                            $hasHandyTalkie = true;

                            $transactionTypes['Handy Talkie'] =
                                true;

                        }


                        if (
                            $transactionType ===
                            'Habis Pakai'
                        ) {

                            $hasConsumable = true;

                            $transactionTypes['Habis Pakai'] =
                                true;

                        }


                        if (
                            $transactionType ===
                            'Merchandise'
                        ) {

                            $hasMerchandise = true;

                            $transactionTypes['Merchandise'] =
                                true;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | BAJU COLOR
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $isBaju
                        ) {

                            $itemId =
                                (int) (
                                    $details['id'] ?? 0
                                );

                            $colorNumber =
                                trim(
                                    (string) (
                                        $details[
                                            'color_number'
                                        ] ?? ''
                                    )
                                );

                            if (
                                $itemId > 0 &&
                                $colorNumber !== '' &&
                                !isset(
                                    $bajuColorsByItem[
                                        $itemId
                                    ]
                                )
                            ) {

                                $bajuColorsByItem[
                                    $itemId
                                ] =
                                    $colorNumber;

                            }

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | PRICE
                        |--------------------------------------------------------------------------
                        */

                        $basePrice =
                            (int) (
                                $details['price'] ?? 0
                            );

                        $sizeExtra =
                            (int) (
                                $details[
                                    'size_additional_price'
                                ] ?? 0
                            );

                        $quantity =
                            (int) (
                                $details['quantity'] ?? 1
                            );

                        $sizeBreakdowns =
                            $details['size_breakdowns'] ?? [];


                        if (
                            $isBaju &&
                            is_array($sizeBreakdowns) &&
                            count($sizeBreakdowns) > 0
                        ) {

                            $subtotal = 0;

                            foreach (
                                $sizeBreakdowns
                                as $breakdown
                            ) {

                                $subtotal +=
                                    (int) (
                                        $breakdown[
                                            'subtotal_price'
                                        ] ?? 0
                                    );

                            }

                        } else {

                            $unitPrice =
                                $basePrice +
                                $sizeExtra;

                            $subtotal =
                                $unitPrice *
                                $quantity;

                        }


                        $totalPrice +=
                            $subtotal;


                        /*
                        |--------------------------------------------------------------------------
                        | MOU
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $transactionType ===
                            'Peralatan'
                        ) {

                            if (
                                $transactionDetail ===
                                'Vendor Rental'
                            ) {

                                $mouTypes['vendor'] =
                                    'MoU Vendor Rental';

                            } else {

                                $mouTypes['internal'] =
                                    'MoU Internal Rental';

                            }

                        }


                        if (
                            $transactionType ===
                            'Handy Talkie'
                        ) {

                            $mouTypes['ht'] =
                                'MoU Handy Talkie';

                        }


                        if (
                            $transactionType ===
                            'Merchandise'
                        ) {

                            if (
                                $subcategory ===
                                'Baju'
                            ) {

                                $mouTypes['baju'] =
                                    'MoU Baju';

                            }


                            if (
                                $subcategory ===
                                'ID Card'
                            ) {

                                $mouTypes['id_card'] =
                                    'MoU ID Card';

                            }

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CART TYPE COUNT
                    |--------------------------------------------------------------------------
                    */

                    $transactionTypeCount =
                        count(
                            $transactionTypes
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | TRACK BAJU ITEMS ALREADY SHOWN
                    |--------------------------------------------------------------------------
                    */

                    $shownBajuColorItems = [];

                @endphp



                <!-- ===================================================== -->
                <!-- MAIN GRID -->
                <!-- ===================================================== -->

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">


                    <!-- ================================================= -->
                    <!-- LEFT -->
                    <!-- ================================================= -->

                    <div class="lg:col-span-7">


                        <div class="overflow-hidden rounded-[2.5rem] border border-gray-100 bg-white shadow-sm">


                            <!-- HEADER -->

                            <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-6 sm:px-8">

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-500">
                                            {{ count($cart) }} Item
                                        </p>

                                        <h2 class="mt-1 text-xl font-black tracking-tight text-gray-900">
                                            Rincian Barang
                                        </h2>

                                    </div>


                                    <form
                                        action="{{ route('student.cart.clear') }}"
                                        method="POST"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[9px] font-black uppercase tracking-widest text-red-500 transition-all hover:bg-red-50"
                                        >

                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1 1v3M4 7h16"
                                                />

                                            </svg>

                                            Kosongkan

                                        </button>

                                    </form>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- TRANSACTION TYPE RULE -->
                            <!-- ================================================= -->

                            <div class="px-6 pt-6 sm:px-8">

                                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">

                                    <div class="flex items-start gap-3">

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">

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
                                                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
                                                />

                                            </svg>

                                        </div>


                                        <div>

                                            <p class="text-[9px] font-black uppercase tracking-[0.15em] text-indigo-600">
                                                Aturan Transaction Type
                                            </p>


                                            <p class="mt-1 text-xs font-bold leading-relaxed text-indigo-900">

                                                Dalam satu transaksi,
                                                <span class="font-black">
                                                    semua barang harus memiliki Transaction Type yang sama.
                                                </span>

                                            </p>


                                            <p class="mt-2 text-[9px] font-bold leading-relaxed text-indigo-600">

                                                Peralatan + Peralatan ✅

                                                <span class="mx-1">
                                                    •
                                                </span>

                                                Peralatan + Habis Pakai ❌

                                            </p>


                                            @if($transactionTypeCount > 1)

                                                <div class="mt-3 rounded-xl border border-red-100 bg-red-50 px-3 py-2">

                                                    <p class="text-[9px] font-black uppercase tracking-widest text-red-600">
                                                        Perhatian
                                                    </p>

                                                    <p class="mt-1 text-[9px] font-bold text-red-700">

                                                        Keranjang saat ini memiliki lebih dari satu Transaction Type.
                                                        Silakan hapus barang dari type lain sebelum checkout.

                                                    </p>

                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- TYPE SUMMARY -->
                            <!-- ================================================= -->

                            <div class="px-6 pt-4 sm:px-8">

                                <div class="flex flex-wrap gap-2">

                                    @foreach($transactionTypes as $type => $enabled)

                                        @if($type === 'Peralatan')

                                            <span class="rounded-lg bg-indigo-100 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-indigo-700">
                                                Peralatan
                                            </span>

                                        @elseif($type === 'Handy Talkie')

                                            <span class="rounded-lg bg-amber-100 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-amber-700">
                                                Handy Talkie
                                            </span>

                                        @elseif($type === 'Habis Pakai')

                                            <span class="rounded-lg bg-rose-100 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-rose-700">
                                                Habis Pakai
                                            </span>

                                        @elseif($type === 'Merchandise')

                                            <span class="rounded-lg bg-emerald-100 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest text-emerald-700">
                                                Merchandise
                                            </span>

                                        @endif

                                    @endforeach

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- ADDITIONAL TYPE NOTICE -->
                            <!-- ================================================= -->

                            <div class="space-y-3 px-6 pt-4 sm:px-8">


                                @if($hasEquipment)

                                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">
                                            Peralatan
                                        </p>

                                        <p class="mt-1 text-[10px] font-bold leading-relaxed text-indigo-800">

                                            Barang wajib dikembalikan sesuai jadwal yang telah dipilih.

                                        </p>

                                    </div>

                                @endif



                                @if($hasHandyTalkie)

                                    <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-amber-600">
                                            Handy Talkie
                                        </p>

                                        <p class="mt-1 text-[10px] font-bold leading-relaxed text-amber-800">

                                            HT wajib dikembalikan dan menggunakan MoU Handy Talkie.

                                        </p>

                                    </div>

                                @endif



                                @if($hasConsumable)

                                    <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-rose-600">
                                            Habis Pakai
                                        </p>

                                        <p class="mt-1 text-[10px] font-bold leading-relaxed text-rose-800">

                                            Barang tidak perlu dikembalikan.
                                            Stok akan berkurang permanen setelah transaksi.

                                        </p>

                                    </div>

                                @endif



                                @if($hasMerchandise)

                                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600">
                                            Merchandise
                                        </p>

                                        <p class="mt-1 text-[10px] font-bold leading-relaxed text-emerald-800">

                                            Merchandise merupakan pembelian putus.
                                            Baju dan ID Card dapat membutuhkan detail desain dan MoU.

                                        </p>

                                    </div>

                                @endif

                            </div>



                            <!-- ================================================= -->
                            <!-- CART ITEMS -->
                            <!-- ================================================= -->

                            <div class="space-y-4 p-6 sm:px-8">


                                @foreach($cart as $id => $details)

                                    @php

                                        $transactionType =
                                            $details[
                                                'transaction_type'
                                            ] ?? '';

                                        $transactionDetail =
                                            $details[
                                                'transaction_detail'
                                            ] ?? null;

                                        $subcategory =
                                            $details[
                                                'subcategory'
                                            ] ?? null;


                                        $size =
                                            $details[
                                                'size'
                                            ] ?? null;


                                        $sizeExtra =
                                            (int) (
                                                $details[
                                                    'size_additional_price'
                                                ] ?? 0
                                            );


                                        $basePrice =
                                            (int) (
                                                $details[
                                                    'price'
                                                ] ?? 0
                                            );


                                        $quantity =
                                            (int) (
                                                $details[
                                                    'quantity'
                                                ] ?? 1
                                            );


                                        $sizeBreakdowns =
                                            $details[
                                                'size_breakdowns'
                                            ] ?? [];


                                        $startDate =
                                            $details[
                                                'start_date'
                                            ] ?? null;


                                        $startTime =
                                            $details[
                                                'start_time'
                                            ] ?? null;


                                        $endDate =
                                            $details[
                                                'end_date'
                                            ] ?? null;


                                        $endTime =
                                            $details[
                                                'end_time'
                                            ] ?? null;


                                        $hasStartSchedule =
                                            !empty($startDate) &&
                                            !empty($startTime);


                                        $hasEndSchedule =
                                            !empty($endDate) &&
                                            !empty($endTime);


                                        $isRental =
                                            in_array(
                                                $transactionType,
                                                [
                                                    'Peralatan',
                                                    'Handy Talkie'
                                                ],
                                                true
                                            );


                                        $isBaju =
                                            $transactionType ===
                                            'Merchandise' &&
                                            $subcategory ===
                                            'Baju';


                                        $itemId =
                                            (int) (
                                                $details[
                                                    'id'
                                                ] ?? 0
                                            );


                                        /*
                                        |--------------------------------------------------------------------------
                                        | BAJU QUANTITY + SUBTOTAL
                                        |--------------------------------------------------------------------------
                                        */

                                        if (
                                            $isBaju &&
                                            is_array(
                                                $sizeBreakdowns
                                            ) &&
                                            count(
                                                $sizeBreakdowns
                                            ) > 0
                                        ) {

                                            $quantity = 0;

                                            $subtotal = 0;


                                            foreach (
                                                $sizeBreakdowns
                                                as $breakdown
                                            ) {

                                                $breakdownQuantity =
                                                    (int) (
                                                        $breakdown[
                                                            'quantity'
                                                        ] ?? 0
                                                    );


                                                $breakdownSubtotal =
                                                    (int) (
                                                        $breakdown[
                                                            'subtotal_price'
                                                        ] ?? 0
                                                    );


                                                $quantity +=
                                                    $breakdownQuantity;


                                                $subtotal +=
                                                    $breakdownSubtotal;

                                            }

                                        } else {

                                            $unitPrice =
                                                $basePrice +
                                                $sizeExtra;

                                            $subtotal =
                                                $unitPrice *
                                                $quantity;

                                        }


                                        /*
                                        |--------------------------------------------------------------------------
                                        | BAJU COLOR
                                        |--------------------------------------------------------------------------
                                        */

                                        $showBajuColor =
                                            $isBaju &&
                                            !in_array(
                                                $itemId,
                                                $shownBajuColorItems,
                                                true
                                            );


                                        if (
                                            $showBajuColor
                                        ) {

                                            $shownBajuColorItems[] =
                                                $itemId;

                                        }


                                        $currentBajuColor =
                                            $itemId > 0
                                                ? (
                                                    $bajuColorsByItem[
                                                        $itemId
                                                    ] ?? ''
                                                )
                                                : '';

                                    @endphp



                                    <!-- ITEM -->

                                    <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-indigo-100 hover:shadow-md sm:p-5">


                                        <!-- ================================================= -->
                                        <!-- TOP -->
                                        <!-- ================================================= -->

                                        <div class="flex items-start gap-4">


                                            <!-- ICON -->

                                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-gray-100 bg-gray-50">

                                                <svg
                                                    class="h-7 w-7 text-gray-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                                    />

                                                </svg>

                                            </div>



                                            <!-- INFO -->

                                            <div class="min-w-0 flex-1">


                                                <div class="flex items-start justify-between gap-3">


                                                    <div class="min-w-0">

                                                        <h3 class="text-base font-black leading-tight text-gray-950 sm:text-lg">
                                                            {{ $details['name'] ?? 'Unnamed Item' }}
                                                        </h3>



                                                        <!-- TRANSACTION TYPE -->

                                                        @if($transactionType === 'Peralatan')

                                                            <span class="mt-2 inline-flex rounded-lg bg-indigo-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-indigo-700">
                                                                Peralatan
                                                            </span>

                                                        @elseif($transactionType === 'Handy Talkie')

                                                            <span class="mt-2 inline-flex rounded-lg bg-amber-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-amber-700">
                                                                Handy Talkie
                                                            </span>

                                                        @elseif($transactionType === 'Habis Pakai')

                                                            <span class="mt-2 inline-flex rounded-lg bg-rose-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-rose-700">
                                                                Habis Pakai
                                                            </span>

                                                        @elseif($transactionType === 'Merchandise')

                                                            <span class="mt-2 inline-flex rounded-lg bg-emerald-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-emerald-700">
                                                                Merchandise
                                                            </span>

                                                        @endif



                                                        <!-- DETAIL -->

                                                        @if($transactionDetail)

                                                            <span class="ml-1 mt-2 inline-flex rounded-lg bg-gray-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-gray-600">
                                                                {{ $transactionDetail }}
                                                            </span>

                                                        @endif



                                                        <!-- SUBCATEGORY -->

                                                        @if($subcategory)

                                                            <span class="ml-1 mt-2 inline-flex rounded-lg bg-gray-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-gray-600">
                                                                {{ $subcategory }}
                                                            </span>

                                                        @endif

                                                    </div>



                                                    <!-- REMOVE -->

                                                    <form
                                                        action="{{ route('student.cart.remove', $id) }}"
                                                        method="POST"
                                                        class="shrink-0"
                                                    >

                                                        @csrf

                                                        @method('DELETE')


                                                        <button
                                                            type="submit"
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-300 transition-all hover:bg-red-50 hover:text-red-500"
                                                            title="Hapus Barang"
                                                        >

                                                            <svg
                                                                class="h-4 w-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 01-1 1v3M4 7h16"
                                                                />

                                                            </svg>

                                                        </button>

                                                    </form>

                                                </div>



                                                <!-- PRICE -->

                                                <div class="mt-2 flex flex-wrap items-center gap-2">

                                                    @if($isBaju)

                                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">

                                                            Harga dasar:
                                                            Rp
                                                            {{ number_format($basePrice, 0, ',', '.') }}/pcs

                                                        </span>

                                                    @else

                                                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">

                                                            Rp
                                                            {{ number_format($basePrice, 0, ',', '.') }}

                                                        </span>


                                                        @if($sizeExtra > 0)

                                                            <span class="rounded-md bg-amber-100 px-2 py-0.5 text-[8px] font-black uppercase tracking-wider text-amber-700">

                                                                +Rp
                                                                {{ number_format($sizeExtra, 0, ',', '.') }}

                                                            </span>

                                                        @endif

                                                    @endif

                                                </div>

                                            </div>

                                        </div>



                                        <!-- ================================================= -->
                                        <!-- MERCHANDISE -->
                                        <!-- ================================================= -->

                                        @if(
                                            $transactionType ===
                                            'Merchandise'
                                        )

                                            @if($subcategory === 'Baju')

                                                <div class="mt-4 rounded-xl border border-indigo-100 bg-indigo-50/60 p-3">

                                                    <div class="flex flex-wrap gap-2">


                                                        @if(

                                                            $isBaju &&

                                                            isset($details['size_breakdowns']) &&

                                                            is_array($details['size_breakdowns']) &&

                                                            count($details['size_breakdowns']) > 0

                                                        )

                                                            <div class="mt-3 rounded-xl border border-indigo-100 bg-white p-3">


                                                                <p class="mb-2 text-[8px] font-black uppercase tracking-widest text-gray-400">

                                                                    Size Breakdown

                                                                </p>


                                                                <div class="space-y-2">


                                                                    @foreach($details['size_breakdowns'] as $breakdown)


                                                                        @php

                                                                            $breakdownSize =
                                                                                $breakdown[
                                                                                    'size'
                                                                                ] ?? '-';


                                                                            $breakdownDivision =
                                                                                $breakdown[
                                                                                    'division'
                                                                                ] ?? '-';


                                                                            $breakdownQuantity =
                                                                                (int) (
                                                                                    $breakdown[
                                                                                        'quantity'
                                                                                    ] ?? 0
                                                                                );


                                                                            $breakdownUnitPrice =
                                                                                (int) (
                                                                                    $breakdown[
                                                                                        'unit_price'
                                                                                    ] ?? 0
                                                                                );


                                                                            $breakdownSubtotal =
                                                                                (int) (
                                                                                    $breakdown[
                                                                                        'subtotal_price'
                                                                                    ] ??
                                                                                    (
                                                                                        $breakdownQuantity *
                                                                                        $breakdownUnitPrice
                                                                                    )
                                                                                );

                                                                        @endphp


                                                                        <div class="rounded-lg bg-gray-50 px-3 py-2">


                                                                            <div class="flex items-center justify-between gap-3">


                                                                                <div class="min-w-0">

                                                                                    <p class="text-[9px] font-black text-gray-900">

                                                                                        {{ $breakdownSize }}

                                                                                    </p>


                                                                                    <p class="mt-0.5 truncate text-[8px] font-bold text-gray-400">

                                                                                        Divisi:
                                                                                        {{ $breakdownDivision }}

                                                                                    </p>

                                                                                </div>


                                                                                <div class="shrink-0 text-right">

                                                                                    <p class="text-[9px] font-black text-gray-900">

                                                                                        {{ $breakdownQuantity }}
                                                                                        pcs

                                                                                    </p>


                                                                                    <p class="mt-0.5 text-[8px] font-bold text-indigo-500">

                                                                                        Rp
                                                                                        {{ number_format($breakdownSubtotal, 0, ',', '.') }}

                                                                                    </p>

                                                                                </div>

                                                                            </div>

                                                                        </div>


                                                                    @endforeach


                                                                </div>


                                                            </div>


                                                        @elseif($size)

                                                            <p class="mt-1 text-[9px] font-bold text-gray-500">

                                                                Size:
                                                                {{ $size }}

                                                            </p>

                                                        @endif


                                                        @if($sizeExtra > 0)

                                                            <span class="inline-flex items-center rounded-lg bg-amber-100 px-3 py-2 text-[9px] font-black text-amber-700">

                                                                +Rp
                                                                {{ number_format($sizeExtra, 0, ',', '.') }}

                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>


                                                @if($showBajuColor)

                                                    <!-- ================================================= -->
                                                    <!-- BAJU COLOR -->
                                                    <!-- ================================================= -->

                                                    <div class="mt-4 rounded-2xl border border-purple-100 bg-purple-50/60 p-4">

                                                        <div class="flex items-start gap-3">

                                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-600 text-white">

                                                                <svg
                                                                    class="h-5 w-5"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke="currentColor"
                                                                >

                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 21a4 4 0 01-4-4c0-1.657 1-3 2.5-3.5L10 12l1-5 3 2 4-3 2 5-2 3.5A4 4 0 0115 21H7z"
                                                                    />

                                                                </svg>

                                                            </div>


                                                            <div class="min-w-0 flex-1">

                                                                <p class="text-[9px] font-black uppercase tracking-[0.16em] text-purple-600">
                                                                    Warna Baju
                                                                </p>


                                                                <p class="mt-1 text-[10px] font-bold leading-relaxed text-purple-800">

                                                                    Pilih warna berdasarkan Color Chart.
                                                                    Warna ini akan digunakan untuk semua size
                                                                    <span class="font-black">
                                                                        {{ $details['name'] ?? 'Baju' }}
                                                                    </span>
                                                                    di transaksi ini.

                                                                </p>


                                                                <!-- COLOR FORM -->

                                                                <form
                                                                    action="{{ route('student.cart.color', $itemId) }}"
                                                                    method="POST"
                                                                    class="mt-4"
                                                                >

                                                                    @csrf

                                                                    @method('PATCH')


                                                                    <div class="flex flex-col gap-3 sm:flex-row">

                                                                        <div class="min-w-0 flex-1">

                                                                            <label class="mb-2 ml-1 block text-[8px] font-black uppercase tracking-[0.18em] text-purple-500">
                                                                                Nomor / Nama Warna
                                                                            </label>


                                                                            <input
                                                                                type="text"
                                                                                name="color_number"
                                                                                value="{{ old('color_number', $currentBajuColor) }}"
                                                                                placeholder="Contoh: 47 / Navy / 47 Navy"
                                                                                maxlength="100"
                                                                                required
                                                                                class="w-full rounded-xl border border-purple-100 bg-white px-4 py-3 text-xs font-black text-gray-900 shadow-sm outline-none transition-all placeholder:text-gray-300 focus:border-purple-400 focus:ring-2 focus:ring-purple-400"
                                                                            >

                                                                        </div>


                                                                        <div class="sm:self-end">

                                                                            <button
                                                                                type="submit"
                                                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-purple-600 px-5 py-3 text-[9px] font-black uppercase tracking-widest text-white shadow-lg shadow-purple-100 transition-all hover:bg-purple-700 sm:w-auto"
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
                                                                                        d="M5 13l4 4L19 7"
                                                                                    />

                                                                                </svg>

                                                                                Simpan Warna

                                                                            </button>

                                                                        </div>

                                                                    </div>

                                                                </form>


                                                                <!-- COLOR CHART -->

                                                                @if(!empty($colorCharts))

                                                                    <div class="mt-5 border-t border-purple-100 pt-4">

                                                                        <div class="flex items-center justify-between gap-3">

                                                                            <div>

                                                                                <p class="text-[8px] font-black uppercase tracking-[0.18em] text-gray-500">
                                                                                    Referensi
                                                                                </p>

                                                                                <p class="mt-1 text-[10px] font-black text-gray-800">
                                                                                    Color Chart Baju
                                                                                </p>

                                                                            </div>


                                                                            <span class="rounded-lg bg-white px-2.5 py-1.5 text-[8px] font-black uppercase tracking-widest text-purple-600 shadow-sm">
                                                                                {{ count($colorCharts) }} Chart
                                                                            </span>

                                                                        </div>


                                                                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">

                                                                            @foreach($colorCharts as $chart)

                                                                                <a
                                                                                    href="{{ asset('storage/' . $chart) }}"
                                                                                    target="_blank"
                                                                                    rel="noopener noreferrer"
                                                                                    class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:border-purple-300 hover:shadow-md"
                                                                                >

                                                                                    <img
                                                                                        src="{{ asset('storage/' . $chart) }}"
                                                                                        alt="Baju Color Chart"
                                                                                        class="h-28 w-full object-cover transition-transform duration-300 group-hover:scale-105 sm:h-32"
                                                                                    >

                                                                                </a>

                                                                            @endforeach

                                                                        </div>

                                                                    </div>

                                                                @else

                                                                    <div class="mt-4 rounded-xl border border-yellow-100 bg-yellow-50 px-3 py-2.5">

                                                                        <p class="text-[9px] font-bold text-yellow-700">
                                                                            Color Chart belum tersedia. Silakan hubungi admin.
                                                                        </p>

                                                                    </div>

                                                                @endif

                                                            </div>

                                                        </div>

                                                    </div>

                                                @endif

                                            @endif



                                            @if(
                                                in_array(
                                                    $subcategory,
                                                    [
                                                        'Baju',
                                                        'ID Card'
                                                    ],
                                                    true
                                                )
                                            )

                                                @if(!empty($details['design_link']))

                                                    <a
                                                        href="{{ $details['design_link'] }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="mt-3 flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-3 transition-all hover:border-indigo-200 hover:bg-indigo-50"
                                                    >

                                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">

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
                                                                    d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"
                                                                />

                                                            </svg>

                                                        </div>


                                                        <div class="min-w-0">

                                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                                Link Drive Desain
                                                            </p>

                                                            <p class="mt-0.5 truncate text-[10px] font-bold text-indigo-600">
                                                                {{ $details['design_link'] }}
                                                            </p>

                                                        </div>

                                                    </a>

                                                @endif

                                            @endif

                                        @endif



                                        <!-- ================================================= -->
                                        <!-- SCHEDULE -->
                                        <!-- ================================================= -->

                                        @if($hasStartSchedule)

                                            <div class="mt-4 grid grid-cols-1 gap-2 {{ $hasEndSchedule ? 'sm:grid-cols-2' : '' }}">


                                                <!-- START -->

                                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-3">

                                                    <div class="flex items-start gap-2.5">

                                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">

                                                            <svg
                                                                class="h-4 w-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2h14"
                                                                />

                                                            </svg>

                                                        </div>


                                                        <div>

                                                            <p class="text-[8px] font-black uppercase tracking-widest text-indigo-400">

                                                                {{ $isRental ? 'Pengambilan' : 'Transaksi' }}

                                                            </p>


                                                            <p class="mt-0.5 text-[10px] font-black text-indigo-800">

                                                                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}

                                                            </p>


                                                            <p class="mt-0.5 text-[9px] font-bold text-indigo-500">

                                                                {{ $startTime }}

                                                            </p>

                                                        </div>

                                                    </div>

                                                </div>



                                                <!-- END -->

                                                @if($hasEndSchedule)

                                                    <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-3">

                                                        <div class="flex items-start gap-2.5">

                                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-amber-600 shadow-sm">

                                                                <svg
                                                                    class="h-4 w-4"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                >

                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9M20 20v-5h-.581m-15.357-2a8.001 8.001 0 01-15.356-2"
                                                                    />

                                                                </svg>

                                                            </div>


                                                            <div>

                                                                <p class="text-[8px] font-black uppercase tracking-widest text-amber-500">
                                                                    Pengembalian
                                                                </p>


                                                                <p class="mt-0.5 text-[10px] font-black text-amber-800">

                                                                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}

                                                                </p>


                                                                <p class="mt-0.5 text-[9px] font-bold text-amber-500">

                                                                    {{ $endTime }}

                                                                </p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                @endif

                                            </div>

                                        @endif



                                        <!-- ================================================= -->
                                        <!-- QUANTITY -->
                                        <!-- ================================================= -->

                                        <div class="mt-4 flex flex-col gap-4 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-between">


                                            <form
                                                action="{{ route('student.cart.update', $id) }}"
                                                method="POST"
                                                class="flex h-10 items-center overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                                            >

                                                @csrf

                                                @method('PATCH')


                                                <div class="flex h-full items-center border-r border-gray-200 bg-gray-50 px-3">

                                                    <span class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                        QTY
                                                    </span>

                                                </div>


                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    value="{{ $quantity }}"
                                                    min="1"
                                                    required
                                                    class="h-full w-16 border-none bg-transparent px-2 text-center text-xs font-black text-gray-900 focus:ring-0"
                                                >


                                                <button
                                                    type="submit"
                                                    class="h-full border-l border-gray-200 bg-indigo-50 px-4 text-[9px] font-black uppercase tracking-widest text-indigo-600 transition-all hover:bg-indigo-600 hover:text-white"
                                                >

                                                    Update

                                                </button>

                                            </form>



                                            <!-- SUBTOTAL -->

                                            <div class="text-left sm:text-right">

                                                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                    Subtotal
                                                </p>


                                                <p class="mt-1 text-lg font-black text-indigo-600 sm:text-xl">

                                                    Rp
                                                    {{ number_format($subtotal, 0, ',', '.') }}

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>



                            <!-- ================================================= -->
                            <!-- TOTAL -->
                            <!-- ================================================= -->

                            <div class="rounded-b-[2.5rem] bg-gray-950 px-6 py-7 sm:px-8">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">
                                            Total
                                        </p>

                                        <p class="mt-1 text-[10px] font-bold text-gray-500">
                                            Total dihitung berdasarkan harga barang dan tambahan ukuran.
                                        </p>

                                    </div>


                                    <p class="text-2xl font-black text-white sm:text-3xl">

                                        Rp
                                        {{ number_format($totalPrice, 0, ',', '.') }}

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- ================================================= -->
                    <!-- RIGHT: CHECKOUT FORM -->
                    <!-- ================================================= -->

                    <div class="lg:col-span-5">


                        <div class="rounded-[2.5rem] border border-gray-100 bg-white p-6 shadow-xl shadow-gray-100/50 sm:p-8 lg:sticky lg:top-28">


                            <!-- HEADER -->

                            <div class="mb-8 flex items-center">

                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">

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
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        />

                                    </svg>

                                </div>


                                <div class="ml-3">

                                    <h3 class="text-xl font-black tracking-tight text-gray-900">
                                        Formulir Pengajuan
                                    </h3>

                                    <p class="mt-0.5 text-[9px] font-bold uppercase tracking-widest text-gray-400">
                                        Lengkapi Data Pemohon
                                    </p>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- CHECKOUT -->
                            <!-- ================================================= -->

                            <form
                                action="{{ route('student.cart.checkout') }}"
                                method="POST"
                                class="space-y-5"
                            >

                                @csrf



                                <!-- NAME -->

                                <div>

                                    <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                        Nama Lengkap PIC
                                    </label>

                                    <input
                                        type="text"
                                        name="full_name"
                                        value="{{ old('full_name') }}"
                                        placeholder="Masukkan nama lengkap..."
                                        required
                                        class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >

                                </div>



                                <!-- ORGANIZATION + POSITION -->

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">


                                    <div>

                                        <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                            Organisasi
                                        </label>


                                        <select
                                            name="organization"
                                            required
                                            class="w-full appearance-none rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                        >

                                            <option
                                                value=""
                                                disabled
                                                {{ old('organization') ? '' : 'selected' }}
                                            >
                                                Pilih Organisasi...
                                            </option>


                                            <option
                                                value="Student Council"
                                                {{ old('organization') === 'Student Council' ? 'selected' : '' }}
                                            >
                                                Student Council
                                            </option>


                                            <option
                                                value="Student Union"
                                                {{ old('organization') === 'Student Union' ? 'selected' : '' }}
                                            >
                                                Student Union
                                            </option>


                                            <option
                                                value="Mentoring Department"
                                                {{ old('organization') === 'Mentoring Department' ? 'selected' : '' }}
                                            >
                                                Mentoring Department
                                            </option>


                                            <option
                                                value="Student Representative Board"
                                                {{ old('organization') === 'Student Representative Board' ? 'selected' : '' }}
                                            >
                                                Student Representative Board
                                            </option>


                                            <option
                                                value="Unit Kegiatan Mahasiswa (UKM)"
                                                {{ old('organization') === 'Unit Kegiatan Mahasiswa (UKM)' ? 'selected' : '' }}
                                            >
                                                Unit Kegiatan Mahasiswa (UKM)
                                            </option>


                                            <option
                                                value="Organisasi External"
                                                {{ old('organization') === 'Organisasi External' ? 'selected' : '' }}
                                            >
                                                Organisasi External
                                            </option>

                                        </select>

                                    </div>



                                    <div>

                                        <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                            Jabatan
                                        </label>


                                        <input
                                            type="text"
                                            name="position"
                                            value="{{ old('position') }}"
                                            placeholder="Contoh: Koordinator"
                                            required
                                            class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                        >

                                    </div>

                                </div>



                                <!-- PROKER + KETUA -->

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">


                                    <div>

                                        <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                            Nama Proker / Event
                                        </label>


                                        <input
                                            type="text"
                                            name="proker_name"
                                            value="{{ old('proker_name') }}"
                                            placeholder="Contoh: Rector Cup 2026"
                                            required
                                            class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                        >

                                    </div>



                                    <div>

                                        <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                            Nama Ketua Acara
                                        </label>


                                        <input
                                            type="text"
                                            name="ketua_acara"
                                            value="{{ old('ketua_acara') }}"
                                            placeholder="Nama lengkap ketua..."
                                            required
                                            class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                        >

                                    </div>

                                </div>



                                <!-- ADDRESS -->

                                <div>

                                    <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                        Alamat Lengkap
                                    </label>


                                    <textarea
                                        name="address"
                                        rows="2"
                                        placeholder="Contoh: Universitas Ciputra Surabaya..."
                                        required
                                        class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >{{ old('address') }}</textarea>

                                </div>



                                <!-- PHONE + TREASURER -->

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">


                                    <div>

                                        <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                            No. WhatsApp
                                        </label>


                                        <input
                                            type="text"
                                            name="phone_number"
                                            value="{{ old('phone_number') }}"
                                            placeholder="0812..."
                                            required
                                            class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                        >

                                    </div>



                                    <div>

                                        <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                            Nama Bendahara
                                        </label>


                                        <input
                                            type="text"
                                            name="treasurer_name"
                                            value="{{ old('treasurer_name') }}"
                                            placeholder="Nama lengkap..."
                                            required
                                            class="w-full rounded-2xl border-none bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-300 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                        >

                                    </div>

                                </div>



                                <!-- NOTES -->

                                <div>

                                    <label class="mb-2 ml-1 block text-[9px] font-black uppercase tracking-[0.18em] text-gray-400">
                                        Catatan Peminjam
                                    </label>


                                    <textarea
                                        name="notes"
                                        rows="3"
                                        placeholder="Contoh: Barang akan digunakan di Gedung A..."
                                        class="w-full rounded-2xl border border-yellow-100 bg-yellow-50/50 px-5 py-4 text-sm font-bold text-gray-800 shadow-inner outline-none placeholder:text-gray-400 focus:ring-2 focus:ring-yellow-400"
                                    >{{ old('notes') }}</textarea>

                                </div>



                                <!-- ================================================= -->
                                <!-- HABIS PAKAI NOTICE -->
                                <!-- ================================================= -->

                                @if($hasConsumable)

                                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-xs font-black text-white">
                                                SC
                                            </div>


                                            <div>

                                                <p class="text-[9px] font-black uppercase tracking-[0.16em] text-emerald-600">
                                                    Fasilitas Student Council
                                                </p>


                                                <p class="mt-1 text-xs font-bold leading-relaxed text-emerald-800">

                                                    Barang
                                                    <span class="font-black">
                                                        Habis Pakai
                                                    </span>
                                                    tidak perlu dikembalikan dan stok akan berkurang setelah transaksi.

                                                </p>


                                                <p class="mt-2 text-[8px] font-bold leading-relaxed text-emerald-600">

                                                    Untuk fasilitas gratis, pilih organisasi
                                                    <span class="font-black">
                                                        Student Council
                                                    </span>.

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @endif



                                <!-- ================================================= -->
                                <!-- MOU NOTICE -->
                                <!-- ================================================= -->

                                @if(!empty($mouTypes))

                                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">

                                        <div class="flex items-start gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-white">

                                                <svg
                                                    class="h-5 w-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A2 2 0 0119 5.414V19a2 2 0 01-2 2z"
                                                    />

                                                </svg>

                                            </div>


                                            <div class="min-w-0 flex-1">

                                                <p class="text-[9px] font-black uppercase tracking-[0.16em] text-indigo-600">
                                                    Dokumen MoU
                                                </p>


                                                <p class="mt-1 text-xs font-bold leading-relaxed text-indigo-800">

                                                    Pengajuan ini membutuhkan dokumen MoU berikut:

                                                </p>


                                                <div class="mt-3 flex flex-wrap gap-2">

                                                    @foreach($mouTypes as $mouName)

                                                        <span class="rounded-lg bg-white px-2.5 py-1.5 text-[8px] font-black uppercase tracking-widest text-indigo-600 shadow-sm">

                                                            {{ $mouName }}

                                                        </span>

                                                    @endforeach

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endif



                                <!-- ================================================= -->
                                <!-- SCHEDULE -->
                                <!-- ================================================= -->

                                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">

                                    <div class="flex items-start gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gray-700 shadow-sm">

                                            <svg
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />

                                            </svg>

                                        </div>


                                        <div>

                                            <p class="text-[9px] font-black uppercase tracking-[0.16em] text-gray-600">
                                                Ketentuan Waktu
                                            </p>


                                            <p class="mt-1 text-xs font-bold leading-relaxed text-gray-600">

                                                Tanggal dan jam transaksi mengikuti pilihan dari katalog.

                                                Jam yang tersedia:
                                                <span class="font-black text-gray-900">
                                                    17:00–19:00
                                                </span>.

                                            </p>


                                            @if($hasEquipment || $hasHandyTalkie)

                                                <p class="mt-2 text-[9px] font-bold leading-relaxed text-gray-500">

                                                    Karena barang merupakan barang yang wajib dikembalikan,
                                                    tanggal dan jam pengembalian juga digunakan.

                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </div>



                                <!-- ================================================= -->
                                <!-- SUBMIT -->
                                <!-- ================================================= -->

                                <button
                                    type="submit"
                                    @if($transactionTypeCount > 1)
                                        disabled
                                    @endif
                                    class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-gray-950 py-5 text-[10px] font-black uppercase tracking-[0.25em] text-white shadow-xl transition-all hover:bg-indigo-600 disabled:cursor-not-allowed disabled:bg-gray-300"
                                >

                                    @if($transactionTypeCount > 1)

                                        Pisahkan Transaction Type Terlebih Dahulu

                                    @else

                                        Submit Order Request

                                        <svg
                                            class="h-4 w-4 transition-transform group-hover:translate-x-1"
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

                                    @endif

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>
