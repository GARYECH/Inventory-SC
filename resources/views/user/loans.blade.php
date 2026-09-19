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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />

                            </svg>

                        </div>


                        <div>

                            <h1 class="text-xl font-black tracking-tight text-gray-950 sm:text-2xl">
                                My Active Loans
                            </h1>

                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 sm:text-[10px]">
                                Transaction History & Documents
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
            <!-- VALIDATION ERRORS -->
            <!-- ===================================================== -->

            @if($errors->any())

                <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-5 py-4 shadow-sm">

                    <p class="text-[9px] font-black uppercase tracking-widest text-red-600">
                        Ada kesalahan
                    </p>

                    <ul class="mt-2 space-y-1 text-xs font-bold text-red-700">

                        @foreach($errors->all() as $error)

                            <li>
                                • {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <!-- ===================================================== -->
            <!-- PAGE INTRO -->
            <!-- ===================================================== -->

            <div class="mb-8 rounded-[2rem] border border-indigo-100 bg-indigo-50/70 p-5 sm:p-6">

                <div class="flex items-start gap-4">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">

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
                                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
                            />

                        </svg>

                    </div>


                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-600">
                            Informasi Transaksi
                        </p>

                        <p class="mt-1 text-sm font-bold leading-relaxed text-indigo-900">
                            Dokumen yang perlu kamu buka mengikuti status transaksi saat ini.
                            Dokumen yang sudah pernah kamu upload tetap tersedia sebagai riwayat.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- ACTIVE TRANSACTIONS -->
            <!-- ===================================================== -->

            <section>

                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-500">
                            Currently Processing
                        </p>

                        <h2 class="mt-1 text-2xl font-black tracking-tight text-gray-950">
                            Active Transactions
                        </h2>

                    </div>


                    <span class="inline-flex w-fit rounded-full border border-indigo-100 bg-white px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-indigo-600 shadow-sm">
                        {{ count($activeLoans) }} Active
                    </span>

                </div>


                @if($activeLoans->isEmpty())

                    <div class="rounded-[2rem] border border-gray-100 bg-white px-6 py-20 text-center shadow-sm">

                        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full border border-gray-100 bg-gray-50 text-gray-300">

                            <svg
                                class="h-9 w-9"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />

                            </svg>

                        </div>


                        <h3 class="text-xl font-black tracking-tight text-gray-900">
                            Belum Ada Transaksi Aktif
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm font-bold text-gray-400">
                            Pengajuan transaksi yang masih diproses akan muncul di sini.
                        </p>


                        <a
                            href="{{ route('student.dashboard') }}"
                            class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-gray-950 px-6 py-3.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-indigo-600"
                        >
                            Mulai Pengajuan

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

                        </a>

                    </div>

                @else

                    <div class="space-y-6">


                        @foreach($activeLoans as $order)

                            @php

                                $statusClasses = [

                                    'Pending' =>
                                        'border-amber-200 bg-amber-50 text-amber-700',

                                    'Approved' =>
                                        'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    'Waiting for MoU' =>
                                        'border-purple-200 bg-purple-50 text-purple-700',

                                    'Pending Review MoU' =>
                                        'border-fuchsia-200 bg-fuchsia-50 text-fuchsia-700',

                                    'Waiting for Payment' =>
                                        'border-orange-200 bg-orange-50 text-orange-700',

                                    'Pending Review Payment' =>
                                        'border-yellow-200 bg-yellow-50 text-yellow-700',

                                    'Waiting for Kwitansi' =>
                                        'border-pink-200 bg-pink-50 text-pink-700',

                                    'Pending Review Kwitansi' =>
                                        'border-rose-200 bg-rose-50 text-rose-700',

                                    'Handed Over' =>
                                        'border-teal-200 bg-teal-50 text-teal-700',

                                    'Pending Return Review' =>
                                        'border-cyan-200 bg-cyan-50 text-cyan-700',

                                    'Returned' =>
                                        'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    'Returned (Damaged)' =>
                                        'border-red-200 bg-red-50 text-red-700',

                                    'Pending Review BA' =>
                                        'border-orange-200 bg-orange-50 text-orange-700',

                                    'Resolved (Fine Paid)' =>
                                        'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    'Rejected' =>
                                        'border-red-200 bg-red-50 text-red-700',

                                    'Cancelled' =>
                                        'border-gray-200 bg-gray-50 text-gray-600',

                                ];


                                $statusClass =
                                    $statusClasses[$order->status]
                                    ?? 'border-gray-200 bg-gray-50 text-gray-600';


                                $requiresMou =
                                    $order->mouDocuments->count() > 0;


                                /*
                                |--------------------------------------------------------------------------
                                | CURRENT STEP
                                |--------------------------------------------------------------------------
                                */

                                $currentStep = match($order->status) {

                                    'Waiting for MoU' =>
                                        'mou',

                                    'Pending Review MoU' =>
                                        'mou_review',

                                    'Waiting for Payment' =>
                                        'payment',

                                    'Pending Review Payment' =>
                                        'payment_review',

                                    'Waiting for Kwitansi' =>
                                        'kwitansi',

                                    'Pending Review Kwitansi' =>
                                        'kwitansi_review',

                                    'Handed Over' =>
                                        'return',

                                    'Pending Return Review' =>
                                        'return_review',

                                    'Returned',
                                    'Returned (Damaged)' =>
                                        'ba',

                                    'Pending Review BA' =>
                                        'ba_review',

                                    default =>
                                        'waiting',

                                };


                                /*
                                |--------------------------------------------------------------------------
                                | MOU LABELS
                                |--------------------------------------------------------------------------
                                */

                                $mouLabels = [

                                    'ht' =>
                                        'MoU Handy Talkie',

                                    'internal' =>
                                        'MoU Internal Rental',

                                    'vendor' =>
                                        'MoU Vendor Rental',

                                    'merch_baju' =>
                                        'MoU Baju',

                                    'merch_idcard' =>
                                        'MoU ID Card',

                                ];

                            @endphp


                            <!-- ================================================= -->
                            <!-- ORDER CARD -->
                            <!-- ================================================= -->

                            <div class="overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">


                                <!-- ================================================= -->
                                <!-- ORDER HEADER -->
                                <!-- ================================================= -->

                                <div class="border-b border-gray-100 bg-gray-50/70 px-5 py-5 sm:px-7">

                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                                        <div>

                                            <div class="flex flex-wrap items-center gap-3">

                                                <h3 class="text-lg font-black text-gray-950">
                                                    {{ $order->order_number }}
                                                </h3>


                                                <span class="rounded-full border px-3 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                                    {{ $order->status }}
                                                </span>

                                            </div>


                                            <p class="mt-1 text-[10px] font-bold text-gray-400">
                                                Diajukan
                                                {{ $order->created_at?->format('d M Y H:i') ?? '-' }}
                                            </p>

                                        </div>


                                        <div class="lg:text-right">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                Grand Total
                                            </p>

                                            <p class="mt-1 text-2xl font-black text-gray-950">
                                                Rp
                                                {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- ORDER BODY -->
                                <!-- ================================================= -->

                                <div class="p-5 sm:p-7">


                                    <!-- ================================================= -->
                                    <!-- BASIC INFORMATION -->
                                    <!-- ================================================= -->

                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

                                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Organization
                                            </p>

                                            <p class="mt-1 text-sm font-black text-gray-900">
                                                {{ $order->organization ?? '-' }}
                                            </p>

                                        </div>


                                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Position
                                            </p>

                                            <p class="mt-1 text-sm font-black text-gray-900">
                                                {{ $order->position ?? '-' }}
                                            </p>

                                        </div>


                                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Proker / Event
                                            </p>

                                            <p class="mt-1 text-sm font-black text-gray-900">
                                                {{ $order->proker_name ?? '-' }}
                                            </p>

                                        </div>


                                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Transaction Type
                                            </p>

                                            <span class="mt-1 inline-flex rounded-lg bg-indigo-100 px-2.5 py-1.5 text-[8px] font-black uppercase tracking-widest text-indigo-700">
                                                {{ $order->order_type ?? '-' }}
                                            </span>

                                        </div>

                                    </div>


                                    <!-- ================================================= -->
                                    <!-- SCHEDULE -->
                                    <!-- ================================================= -->

                                    @if(
                                        $order->start_date ||
                                        $order->start_time ||
                                        $order->end_date ||
                                        $order->end_time
                                    )

                                        <div class="mt-5 rounded-2xl border border-gray-100 bg-white">

                                            <div class="border-b border-gray-100 px-4 py-3">

                                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                    Transaction Schedule
                                                </p>

                                            </div>


                                            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">


                                                <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-3">

                                                    <p class="text-[8px] font-black uppercase tracking-widest text-indigo-400">
                                                        Pengambilan / Transaksi
                                                    </p>

                                                    <p class="mt-1 text-sm font-black text-indigo-900">
                                                        {{ $order->start_date?->format('d M Y') ?? '-' }}
                                                    </p>

                                                    <p class="mt-0.5 text-[10px] font-bold text-indigo-500">
                                                        {{ $order->start_time?->format('H:i') ?? '-' }}
                                                    </p>

                                                </div>


                                                @if(
                                                    $order->end_date ||
                                                    $order->end_time
                                                )

                                                    <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-3">

                                                        <p class="text-[8px] font-black uppercase tracking-widest text-amber-500">
                                                            Pengembalian
                                                        </p>

                                                        <p class="mt-1 text-sm font-black text-amber-900">
                                                            {{ $order->end_date?->format('d M Y') ?? '-' }}
                                                        </p>

                                                        <p class="mt-0.5 text-[10px] font-bold text-amber-500">
                                                            {{ $order->end_time?->format('H:i') ?? '-' }}
                                                        </p>

                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    @endif


                                    <!-- ================================================= -->
                                    <!-- ITEM DETAILS -->
                                    <!-- ================================================= -->

                                    <div class="mt-5 rounded-2xl border border-gray-100 bg-white">

                                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                Item Details
                                            </p>

                                            <span class="text-[9px] font-bold text-gray-400">
                                                {{ $order->orderItems->count() }} item
                                            </span>

                                        </div>


                                        <div class="space-y-3 p-4">

                                            @foreach($order->orderItems as $detail)

                                                @php
                                                    $item = $detail->item;
                                                @endphp


                                                @if($item)

                                                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                                        <div class="flex items-start gap-3">


                                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">

                                                                @if($item->item_photo)

                                                                    <img
                                                                        src="{{ asset('storage/' . $item->item_photo) }}"
                                                                        alt="{{ $item->name }}"
                                                                        class="h-full w-full object-cover"
                                                                    >

                                                                @else

                                                                    <div class="flex h-full w-full items-center justify-center text-gray-300">

                                                                        <svg
                                                                            class="h-6 w-6"
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

                                                                <div class="flex items-start justify-between gap-3">

                                                                    <div class="min-w-0">

                                                                        <p class="text-sm font-black text-gray-900">
                                                                            {{ $item->name }}
                                                                        </p>


                                                                        <p class="mt-1 text-[9px] font-bold text-gray-400">
                                                                            Category:
                                                                            {{ optional($item->category)->name ?? '-' }}
                                                                        </p>

                                                                    </div>


                                                                    <span class="shrink-0 rounded-lg bg-white px-2.5 py-1 text-[9px] font-black text-gray-700">
                                                                        {{ $detail->quantity }}x
                                                                    </span>

                                                                </div>


                                                                @if($item->transaction_detail)

                                                                    <p class="mt-2 text-[9px] font-black uppercase tracking-widest text-indigo-600">
                                                                        Detail:
                                                                        {{ $item->transaction_detail }}
                                                                    </p>

                                                                @endif


                                                                @if($item->subcategory)

                                                                    <p class="mt-1 text-[9px] font-bold text-gray-500">
                                                                        Subcategory:
                                                                        {{ $item->subcategory }}
                                                                    </p>

                                                                @endif


                                                                @if($detail->size)

                                                                    <p class="mt-1 text-[9px] font-bold text-gray-500">
                                                                        Size:
                                                                        {{ $detail->size }}
                                                                    </p>

                                                                @endif


                                                                @if(
                                                                    $detail->color_number &&
                                                                    $item->transaction_type === 'Merchandise' &&
                                                                    $item->subcategory === 'Baju'
                                                                )

                                                                    <p class="mt-1 text-[9px] font-bold text-gray-500">

                                                                        Warna:
                                                                        <span class="font-black text-purple-600">
                                                                            {{ $detail->color_number }}
                                                                        </span>

                                                                    </p>

                                                                @endif


                                                                @if($detail->design_link)

                                                                    <a
                                                                        href="{{ $detail->design_link }}"
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        class="mt-1 inline-block text-[9px] font-black text-indigo-600 hover:underline"
                                                                    >
                                                                        Open Design Link
                                                                    </a>

                                                                @endif


                                                                <div class="mt-3 flex items-center justify-between border-t border-gray-200 pt-3">

                                                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                                        Subtotal
                                                                    </span>

                                                                    <span class="text-sm font-black text-gray-900">
                                                                        Rp
                                                                        {{ number_format($detail->subtotal_price ?? 0, 0, ',', '.') }}
                                                                    </span>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                @endif

                                            @endforeach

                                        </div>

                                    </div>


                                    <!-- ================================================= -->
                                    <!-- GENERATED DOCUMENTS - STATUS BASED -->
                                    <!-- ================================================= -->

                                    @php

                                        $showGeneratedDocuments = in_array(
                                            $currentStep,
                                            [
                                                'mou',
                                                'mou_review',
                                                'payment',
                                                'payment_review',
                                                'kwitansi',
                                                'kwitansi_review',
                                                'ba',
                                                'ba_review',
                                            ],
                                            true
                                        );

                                    @endphp


                                    @if($showGeneratedDocuments)

                                        <div class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 p-4">

                                            <div class="flex items-start gap-3">

                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">

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
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                        />

                                                    </svg>

                                                </div>


                                                <div class="min-w-0 flex-1">

                                                    <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">
                                                        Generated Documents
                                                    </p>


                                                    <p class="mt-1 text-[9px] leading-relaxed text-indigo-700">

                                                        @if(
                                                            $currentStep === 'mou' ||
                                                            $currentStep === 'mou_review'
                                                        )

                                                            MoU yang relevan dengan transaksi ini.

                                                        @elseif(
                                                            $currentStep === 'payment' ||
                                                            $currentStep === 'payment_review'
                                                        )

                                                            Invoice untuk transaksi ini.

                                                        @elseif(
                                                            $currentStep === 'kwitansi' ||
                                                            $currentStep === 'kwitansi_review'
                                                        )

                                                            Kwitansi untuk transaksi ini.

                                                        @elseif(
                                                            $currentStep === 'ba' ||
                                                            $currentStep === 'ba_review'
                                                        )

                                                            Berita Acara untuk transaksi ini.

                                                        @endif

                                                    </p>


                                                    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">


                                                        <!-- ================================================= -->
                                                        <!-- MOU -->
                                                        <!-- ================================================= -->

                                                        @if(
                                                            $currentStep === 'mou' ||
                                                            $currentStep === 'mou_review'
                                                        )

                                                            @if($requiresMou)

                                                                @foreach($order->mouDocuments as $document)

                                                                    <a
                                                                        href="{{ route('student.document.mou', [$order->id, $document->id]) }}"
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        class="flex w-full items-center justify-center rounded-xl border border-purple-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-purple-700 transition hover:bg-purple-100"
                                                                    >

                                                                        View
                                                                        {{ $mouLabels[$document->mou_type] ?? 'MoU' }}

                                                                    </a>

                                                                @endforeach

                                                            @else

                                                                <div class="rounded-xl border border-purple-100 bg-white px-4 py-3">

                                                                    <p class="text-[9px] font-bold text-gray-400">
                                                                        Tidak ada dokumen MoU untuk transaksi ini.
                                                                    </p>

                                                                </div>

                                                            @endif

                                                        @endif


                                                        <!-- ================================================= -->
                                                        <!-- INVOICE -->
                                                        <!-- ================================================= -->

                                                        @if(
                                                            $currentStep === 'payment' ||
                                                            $currentStep === 'payment_review'
                                                        )

                                                            <a
                                                                href="{{ route('student.document.invoice', $order->id) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex w-full items-center justify-center rounded-xl border border-indigo-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-indigo-700 transition hover:bg-indigo-100"
                                                            >
                                                                View Invoice
                                                            </a>

                                                        @endif


                                                        <!-- ================================================= -->
                                                        <!-- KWITANSI -->
                                                        <!-- ================================================= -->

                                                        @if(
                                                            $currentStep === 'kwitansi' ||
                                                            $currentStep === 'kwitansi_review'
                                                        )

                                                            <a
                                                                href="{{ route('student.document.kwitansi', $order->id) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex w-full items-center justify-center rounded-xl border border-pink-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-pink-700 transition hover:bg-pink-100"
                                                            >
                                                                View Kwitansi
                                                            </a>

                                                        @endif


                                                        <!-- ================================================= -->
                                                        <!-- BERITA ACARA -->
                                                        <!-- ================================================= -->

                                                        @if(
                                                            $currentStep === 'ba' ||
                                                            $currentStep === 'ba_review'
                                                        )

                                                            <a
                                                                href="{{ route('student.document.berita-acara', $order->id) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex w-full items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-red-700 transition hover:bg-red-100"
                                                            >
                                                                View Berita Acara
                                                            </a>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endif


                                    <!-- ================================================= -->
                                    <!-- UPLOADED DOCUMENTS - HISTORY -->
                                    <!-- ================================================= -->

                                    @php

                                        $hasUploadedDocuments =
                                            $order->mouDocuments->contains(
                                                function ($document) {

                                                    return !empty(
                                                        $document->signed_file_path
                                                    );

                                                }
                                            )
                                            ||
                                            !empty(
                                                $order->payment_receipt
                                            )
                                            ||
                                            !empty(
                                                $order->signed_kwitansi
                                            )
                                            ||
                                            !empty(
                                                $order->return_drive_link
                                            )
                                            ||
                                            !empty(
                                                $order->signed_ba_file
                                            );

                                    @endphp


                                    @if($hasUploadedDocuments)

                                        <div class="mt-5 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">

                                            <div class="flex items-start gap-3">

                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white">

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
                                                            d="M5 13l4 4L19 7"
                                                        />

                                                    </svg>

                                                </div>


                                                <div class="min-w-0 flex-1">

                                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                                        Uploaded Documents
                                                    </p>


                                                    <p class="mt-1 text-[9px] leading-relaxed text-emerald-700">
                                                        Riwayat dokumen yang sudah kamu upload.
                                                    </p>


                                                    <div class="mt-4 space-y-2">


                                                        <!-- ================================================= -->
                                                        <!-- SIGNED MOU HISTORY -->
                                                        <!-- ================================================= -->

                                                        @foreach($order->mouDocuments as $document)

                                                            @if($document->signed_file_path)

                                                                <a
                                                                    href="{{ asset('storage/' . $document->signed_file_path) }}"
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                                >

                                                                    <div class="min-w-0">

                                                                        <p class="truncate text-[9px] font-black text-gray-800">
                                                                            {{ $mouLabels[$document->mou_type] ?? 'Signed MoU' }}
                                                                        </p>

                                                                        <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                            Signed MoU
                                                                        </p>

                                                                    </div>


                                                                    <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                        View
                                                                    </span>

                                                                </a>

                                                            @endif

                                                        @endforeach


                                                        <!-- ================================================= -->
                                                        <!-- PAYMENT HISTORY -->
                                                        <!-- ================================================= -->

                                                        @if($order->payment_receipt)

                                                            <a
                                                                href="{{ asset('storage/' . $order->payment_receipt) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div class="min-w-0">

                                                                    <p class="truncate text-[9px] font-black text-gray-800">
                                                                        Payment Receipt
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Bukti pembayaran
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    View
                                                                </span>

                                                            </a>

                                                        @endif


                                                        <!-- ================================================= -->
                                                        <!-- SIGNED KWITANSI HISTORY -->
                                                        <!-- ================================================= -->

                                                        @if($order->signed_kwitansi)

                                                            <a
                                                                href="{{ asset('storage/' . $order->signed_kwitansi) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div class="min-w-0">

                                                                    <p class="truncate text-[9px] font-black text-gray-800">
                                                                        Signed Kwitansi
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Kwitansi yang sudah ditandatangani
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    View
                                                                </span>

                                                            </a>

                                                        @endif


                                                        <!-- ================================================= -->
                                                        <!-- RETURN HISTORY -->
                                                        <!-- ================================================= -->

                                                        @if($order->return_drive_link)

                                                            <a
                                                                href="{{ $order->return_drive_link }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div class="min-w-0">

                                                                    <p class="truncate text-[9px] font-black text-gray-800">
                                                                        Return Evidence
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Google Drive
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    Open
                                                                </span>

                                                            </a>

                                                        @endif


                                                        <!-- ================================================= -->
                                                        <!-- SIGNED BA HISTORY -->
                                                        <!-- ================================================= -->

                                                        @if($order->signed_ba_file)

                                                            <a
                                                                href="{{ asset('storage/' . $order->signed_ba_file) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div class="min-w-0">

                                                                    <p class="truncate text-[9px] font-black text-gray-800">
                                                                        Signed Berita Acara
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Berita Acara yang sudah ditandatangani
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    View
                                                                </span>

                                                            </a>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endif


                                    <!-- ================================================= -->
                                    <!-- CURRENT STEP -->
                                    <!-- ================================================= -->

                                    <div class="mt-5 overflow-hidden rounded-2xl border border-gray-100 bg-white">


                                        <!-- ================================================= -->
                                        <!-- WAITING FOR MOU -->
                                        <!-- ================================================= -->

                                        @if($currentStep === 'mou')

                                            <div class="border-b border-purple-100 bg-purple-50 px-4 py-4">

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
                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-purple-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Signed MoU
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-purple-700">
                                                            Upload semua MoU yang diperlukan untuk transaksi ini.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div class="space-y-3 p-4">

                                                @foreach($order->mouDocuments as $document)

                                                    @php
                                                        $mouLabel =
                                                            $mouLabels[$document->mou_type]
                                                            ?? 'MoU';
                                                    @endphp


                                                    <div class="rounded-xl border border-purple-100 bg-purple-50/40 p-4">

                                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


                                                            <div class="min-w-0">

                                                                <p class="text-[10px] font-black text-gray-900">
                                                                    {{ $mouLabel }}
                                                                </p>


                                                                @if($document->signed_file_path)

                                                                    <p class="mt-1 text-[8px] font-black text-emerald-600">
                                                                        ✓ Sudah diupload
                                                                    </p>

                                                                @else

                                                                    <p class="mt-1 text-[8px] font-black text-red-500">
                                                                        Belum diupload
                                                                    </p>

                                                                @endif

                                                            </div>


                                                            <a
                                                                href="{{ route('student.document.mou', [$order->id, $document->id]) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="inline-flex shrink-0 items-center justify-center rounded-xl border border-purple-200 bg-white px-4 py-2.5 text-[8px] font-black uppercase tracking-widest text-purple-700 transition hover:bg-purple-100"
                                                            >
                                                                View MoU
                                                            </a>

                                                        </div>


                                                        <form
                                                            action="{{ route('student.orders.upload-mou', [$order->id, $document->id]) }}"
                                                            method="POST"
                                                            enctype="multipart/form-data"
                                                            class="mt-3"
                                                        >

                                                            @csrf


                                                            <div class="flex flex-col gap-3 sm:flex-row">

                                                                <input
                                                                    type="file"
                                                                    name="signed_mou"
                                                                    accept=".pdf"
                                                                    required
                                                                    class="block w-full rounded-xl border border-purple-200 bg-white px-3 py-2.5 text-[9px] font-bold text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-purple-100 file:px-3 file:py-2 file:text-[8px] file:font-black file:text-purple-700"
                                                                />


                                                                <button
                                                                    type="submit"
                                                                    class="inline-flex shrink-0 items-center justify-center rounded-xl bg-purple-600 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-purple-700"
                                                                >
                                                                    {{ $document->signed_file_path ? 'Ganti MoU' : 'Upload MoU' }}
                                                                </button>

                                                            </div>

                                                        </form>

                                                    </div>

                                                @endforeach

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- PENDING REVIEW MOU -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'mou_review')

                                            <div class="bg-fuchsia-50 px-4 py-5">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-fuchsia-600 text-white">

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
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-fuchsia-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            MoU Under Review
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-fuchsia-700">
                                                            Semua MoU sudah diterima dan sedang diperiksa oleh admin.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- WAITING FOR PAYMENT -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'payment')

                                            <div class="border-b border-orange-100 bg-orange-50 px-4 py-4">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-500 text-white">

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
                                                                d="M12 8c-1.657 0-3 1.343-3 3v5m6-5a3 3 0 00-3-3m0 0V5m0 6v5m-5 4h10a2 2 0 002-2V7a2 2 0 00-2-2h-10a2 2 0 00-2 2v11a2 2 0 002 2z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-orange-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Payment
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-orange-700">
                                                            Lihat invoice lalu upload bukti pembayaran.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div class="p-4">

                                                <form
                                                    action="{{ route('student.orders.upload-payment', $order->id) }}"
                                                    method="POST"
                                                    enctype="multipart/form-data"
                                                >

                                                    @csrf


                                                    <div class="flex flex-col gap-3">


                                                        <div class="flex flex-col gap-3 sm:flex-row">

                                                            <input
                                                                type="file"
                                                                name="payment_receipt"
                                                                accept=".pdf,.jpg,.jpeg,.png"
                                                                required
                                                                class="block w-full rounded-xl border border-orange-200 bg-white px-3 py-2.5 text-[9px] font-bold text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-orange-100 file:px-3 file:py-2 file:text-[8px] file:font-black file:text-orange-700"
                                                            />


                                                            <button
                                                                type="submit"
                                                                class="inline-flex shrink-0 items-center justify-center rounded-xl bg-orange-500 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-orange-600"
                                                            >
                                                                Upload Bukti Pembayaran
                                                            </button>

                                                        </div>

                                                    </div>

                                                </form>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- PENDING REVIEW PAYMENT -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'payment_review')

                                            <div class="bg-yellow-50 px-4 py-5">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-yellow-500 text-white">

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
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-yellow-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Payment Under Review
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-yellow-700">
                                                            Bukti pembayaran sudah dikirim dan sedang diperiksa admin.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- WAITING FOR KWITANSI -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'kwitansi')

                                            <div class="border-b border-pink-100 bg-pink-50 px-4 py-4">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-500 text-white">

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
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-pink-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Signed Kwitansi
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-pink-700">
                                                            Lihat kwitansi, tanda tangani, lalu upload kembali.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div class="p-4">

                                                <form
                                                    action="{{ route('student.orders.upload-kwitansi', $order->id) }}"
                                                    method="POST"
                                                    enctype="multipart/form-data"
                                                >

                                                    @csrf


                                                    <div class="flex flex-col gap-3 sm:flex-row">

                                                        <input
                                                            type="file"
                                                            name="signed_kwitansi"
                                                            accept=".pdf"
                                                            required
                                                            class="block w-full rounded-xl border border-pink-200 bg-white px-3 py-2.5 text-[9px] font-bold text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-pink-100 file:px-3 file:py-2 file:text-[8px] file:font-black file:text-pink-700"
                                                        />


                                                        <button
                                                            type="submit"
                                                            class="inline-flex shrink-0 items-center justify-center rounded-xl bg-pink-500 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-pink-600"
                                                        >
                                                            Upload Kwitansi
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- PENDING REVIEW KWITANSI -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'kwitansi_review')

                                            <div class="bg-rose-50 px-4 py-5">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white">

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
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-rose-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Kwitansi Under Review
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-rose-700">
                                                            Kwitansi sudah dikirim dan sedang diperiksa admin.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- HANDED OVER -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'return')

                                            <div class="border-b border-cyan-100 bg-cyan-50 px-4 py-4">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-500 text-white">

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
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9M20 20v-5h-.581m-15.357-2A8.001 8.001 0 014.582 15"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-cyan-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Return Evidence
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-cyan-700">
                                                            Masukkan link Google Drive yang berisi dokumentasi kondisi barang.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div class="p-4">

                                                <form
                                                    action="{{ route('student.orders.return-link', $order->id) }}"
                                                    method="POST"
                                                >

                                                    @csrf


                                                    <input
                                                        type="url"
                                                        name="return_drive_link"
                                                        value="{{ old('return_drive_link', '') }}"
                                                        placeholder="https://drive.google.com/..."
                                                        maxlength="2000"
                                                        required
                                                        class="w-full rounded-xl border border-cyan-200 bg-white px-4 py-3 text-xs font-bold text-gray-800 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                                                    />


                                                    <button
                                                        type="submit"
                                                        class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-cyan-500 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-cyan-600"
                                                    >
                                                        Kirim Bukti Pengembalian
                                                    </button>

                                                </form>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- PENDING RETURN REVIEW -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'return_review')

                                            <div class="bg-cyan-50 px-4 py-5">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-500 text-white">

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
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-cyan-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Return Under Review
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-cyan-700">
                                                            Bukti pengembalian sudah dikirim dan sedang diperiksa admin.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- RETURNED / RETURNED DAMAGED -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'ba')

                                            <div class="border-b border-emerald-100 bg-emerald-50 px-4 py-4">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white">

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
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Berita Acara
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-emerald-700">
                                                            Download Berita Acara, isi, tanda tangani, lalu upload kembali.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div class="p-4">

                                                <form
                                                    action="{{ route('student.orders.upload-ba', $order->id) }}"
                                                    method="POST"
                                                    enctype="multipart/form-data"
                                                >

                                                    @csrf


                                                    <div class="flex flex-col gap-3 sm:flex-row">

                                                        <input
                                                            type="file"
                                                            name="signed_ba_file"
                                                            accept=".pdf"
                                                            required
                                                            class="block w-full rounded-xl border border-emerald-200 bg-white px-3 py-2.5 text-[9px] font-bold text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-100 file:px-3 file:py-2 file:text-[8px] file:font-black file:text-emerald-700"
                                                        />


                                                        <button
                                                            type="submit"
                                                            class="inline-flex shrink-0 items-center justify-center rounded-xl bg-emerald-500 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-600"
                                                        >
                                                            Upload BA
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- PENDING REVIEW BA -->
                                        <!-- ================================================= -->

                                        @elseif($currentStep === 'ba_review')

                                            <div class="bg-orange-50 px-4 py-5">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-500 text-white">

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
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-orange-700">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Berita Acara Under Review
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-orange-700">
                                                            Berita Acara sudah dikirim dan sedang diperiksa admin.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                        <!-- ================================================= -->
                                        <!-- DEFAULT -->
                                        <!-- ================================================= -->

                                        @else

                                            <div class="bg-gray-50 px-4 py-5">

                                                <div class="flex items-start gap-3">

                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-white">

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
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />

                                                        </svg>

                                                    </div>


                                                    <div>

                                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-500">
                                                            Current Step
                                                        </p>

                                                        <h4 class="mt-1 text-sm font-black text-gray-900">
                                                            Menunggu Proses Admin
                                                        </h4>

                                                        <p class="mt-1 text-[9px] font-bold leading-relaxed text-gray-500">
                                                            Tidak ada dokumen yang perlu diunggah pada tahap ini.
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        @endif

                                    </div>


                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </section>


            <!-- ===================================================== -->
            <!-- PAST TRANSACTIONS -->
            <!-- ===================================================== -->

            <section class="mt-14">


                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-500">
                            Completed / Closed
                        </p>

                        <h2 class="mt-1 text-2xl font-black tracking-tight text-gray-950">
                            Transaction History
                        </h2>

                    </div>


                    <span class="inline-flex w-fit rounded-full border border-gray-200 bg-white px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-gray-500 shadow-sm">
                        {{ $pastLoans->total() }} Records
                    </span>

                </div>


                @if($pastLoans->isEmpty())

                    <div class="rounded-[2rem] border border-gray-100 bg-white px-6 py-16 text-center shadow-sm">

                        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full border border-gray-100 bg-gray-50 text-gray-300">

                            <svg
                                class="h-8 w-8"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 012 2z"
                                />

                            </svg>

                        </div>


                        <h3 class="text-lg font-black tracking-tight text-gray-900">
                            Belum Ada Riwayat
                        </h3>

                        <p class="mt-2 text-sm font-bold text-gray-400">
                            Transaksi yang sudah selesai atau ditutup akan muncul di sini.
                        </p>

                    </div>

                @else

                    <div class="space-y-4">


                        @foreach($pastLoans as $order)

                            @php

                                $historyStatusClasses = [

                                    'Returned' =>
                                        'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    'Returned (Damaged)' =>
                                        'border-red-200 bg-red-50 text-red-700',

                                    'Resolved (Fine Paid)' =>
                                        'border-emerald-200 bg-emerald-50 text-emerald-700',

                                    'Rejected' =>
                                        'border-red-200 bg-red-50 text-red-700',

                                    'Cancelled' =>
                                        'border-gray-200 bg-gray-50 text-gray-600',

                                ];


                                $historyStatusClass =
                                    $historyStatusClasses[$order->status]
                                    ?? 'border-gray-200 bg-gray-50 text-gray-600';


                                $requiresHistoryMou =
                                    $order->mouDocuments->count() > 0;


                                $historyMouLabels = [

                                    'ht' =>
                                        'MoU Handy Talkie',

                                    'internal' =>
                                        'MoU Internal Rental',

                                    'vendor' =>
                                        'MoU Vendor Rental',

                                    'merch_baju' =>
                                        'MoU Baju',

                                    'merch_idcard' =>
                                        'MoU ID Card',

                                ];

                            @endphp


                            <div class="overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">


                                <!-- ================================================= -->
                                <!-- HEADER -->
                                <!-- ================================================= -->

                                <div class="border-b border-gray-100 bg-gray-50/70 px-5 py-5 sm:px-7">

                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                                        <div>

                                            <div class="flex flex-wrap items-center gap-3">

                                                <h3 class="text-lg font-black text-gray-950">
                                                    {{ $order->order_number }}
                                                </h3>


                                                <span class="rounded-full border px-3 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $historyStatusClass }}">
                                                    {{ $order->status }}
                                                </span>

                                            </div>


                                            <p class="mt-1 text-[10px] font-bold text-gray-400">
                                                Ditutup pada
                                                {{ $order->updated_at?->format('d M Y H:i') ?? '-' }}
                                            </p>

                                        </div>


                                        <div class="lg:text-right">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                Grand Total
                                            </p>

                                            <p class="mt-1 text-xl font-black text-gray-950">
                                                Rp
                                                {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- BODY -->
                                <!-- ================================================= -->

                                <div class="p-5 sm:p-7">


                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">


                                        <!-- INFO -->

                                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Transaction Information
                                            </p>


                                            <div class="mt-4 space-y-3">


                                                <div>

                                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                        Organization
                                                    </p>

                                                    <p class="mt-1 text-xs font-black text-gray-900">
                                                        {{ $order->organization ?? '-' }}
                                                    </p>

                                                </div>


                                                <div>

                                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                        Proker
                                                    </p>

                                                    <p class="mt-1 text-xs font-black text-gray-900">
                                                        {{ $order->proker_name ?? '-' }}
                                                    </p>

                                                </div>


                                                <div>

                                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                        Transaction Type
                                                    </p>

                                                    <p class="mt-1 text-xs font-black text-indigo-600">
                                                        {{ $order->order_type ?? '-' }}
                                                    </p>

                                                </div>

                                            </div>

                                        </div>


                                        <!-- ITEMS -->

                                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 lg:col-span-2">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Items
                                            </p>


                                            <div class="mt-4 space-y-3">

                                                @foreach($order->orderItems as $detail)

                                                    @php
                                                        $item = $detail->item;
                                                    @endphp


                                                    @if($item)

                                                        <div class="rounded-xl border border-gray-100 bg-white p-3">

                                                            <div class="flex items-center justify-between gap-3">

                                                                <div class="min-w-0">

                                                                    <p class="text-sm font-black text-gray-900">
                                                                        {{ $item->name }}
                                                                    </p>


                                                                    <p class="mt-1 text-[8px] font-bold text-gray-400">
                                                                        {{ optional($item->category)->name ?? '-' }}
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 rounded-lg bg-gray-50 px-2.5 py-1 text-[8px] font-black text-gray-700">
                                                                    {{ $detail->quantity }}x
                                                                </span>

                                                            </div>


                                                            <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-2">

                                                                <span class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                                    Subtotal
                                                                </span>

                                                                <span class="text-xs font-black text-gray-900">
                                                                    Rp
                                                                    {{ number_format($detail->subtotal_price ?? 0, 0, ',', '.') }}
                                                                </span>

                                                            </div>

                                                        </div>

                                                    @endif

                                                @endforeach

                                            </div>

                                        </div>

                                    </div>


                                    <!-- ================================================= -->
                                    <!-- HISTORY DOCUMENTS -->
                                    <!-- ================================================= -->

                                    @php

                                        $hasHistoryDocuments =
                                            $order->mouDocuments->contains(
                                                function ($document) {

                                                    return !empty(
                                                        $document->signed_file_path
                                                    );

                                                }
                                            )
                                            ||
                                            !empty(
                                                $order->payment_receipt
                                            )
                                            ||
                                            !empty(
                                                $order->signed_kwitansi
                                            )
                                            ||
                                            !empty(
                                                $order->return_drive_link
                                            )
                                            ||
                                            !empty(
                                                $order->signed_ba_file
                                            );

                                    @endphp


                                    @if($hasHistoryDocuments)

                                        <div class="mt-5 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">

                                            <div class="flex items-start gap-3">

                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white">

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
                                                            d="M5 13l4 4L19 7"
                                                        />

                                                    </svg>

                                                </div>


                                                <div class="min-w-0 flex-1">

                                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                                        Uploaded Documents
                                                    </p>

                                                    <p class="mt-1 text-[9px] leading-relaxed text-emerald-700">
                                                        Riwayat dokumen yang pernah kamu upload.
                                                    </p>


                                                    <div class="mt-4 space-y-2">


                                                        <!-- MOU -->

                                                        @foreach($order->mouDocuments as $document)

                                                            @if($document->signed_file_path)

                                                                <a
                                                                    href="{{ asset('storage/' . $document->signed_file_path) }}"
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                                >

                                                                    <div class="min-w-0">

                                                                        <p class="truncate text-[9px] font-black text-gray-800">
                                                                            {{ $historyMouLabels[$document->mou_type] ?? 'Signed MoU' }}
                                                                        </p>

                                                                        <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                            Signed MoU
                                                                        </p>

                                                                    </div>


                                                                    <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                        View
                                                                    </span>

                                                                </a>

                                                            @endif

                                                        @endforeach


                                                        <!-- PAYMENT -->

                                                        @if($order->payment_receipt)

                                                            <a
                                                                href="{{ asset('storage/' . $order->payment_receipt) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div>

                                                                    <p class="text-[9px] font-black text-gray-800">
                                                                        Payment Receipt
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Bukti pembayaran
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    View
                                                                </span>

                                                            </a>

                                                        @endif


                                                        <!-- KWITANSI -->

                                                        @if($order->signed_kwitansi)

                                                            <a
                                                                href="{{ asset('storage/' . $order->signed_kwitansi) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div>

                                                                    <p class="text-[9px] font-black text-gray-800">
                                                                        Signed Kwitansi
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Kwitansi yang sudah ditandatangani
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    View
                                                                </span>

                                                            </a>

                                                        @endif


                                                        <!-- RETURN -->

                                                        @if($order->return_drive_link)

                                                            <a
                                                                href="{{ $order->return_drive_link }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div>

                                                                    <p class="text-[9px] font-black text-gray-800">
                                                                        Return Evidence
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Google Drive
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    Open
                                                                </span>

                                                            </a>

                                                        @endif


                                                        <!-- BA -->

                                                        @if($order->signed_ba_file)

                                                            <a
                                                                href="{{ asset('storage/' . $order->signed_ba_file) }}"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 transition hover:bg-emerald-100"
                                                            >

                                                                <div>

                                                                    <p class="text-[9px] font-black text-gray-800">
                                                                        Signed Berita Acara
                                                                    </p>

                                                                    <p class="mt-0.5 text-[8px] font-bold text-emerald-600">
                                                                        Berita Acara yang sudah ditandatangani
                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-emerald-600">
                                                                    View
                                                                </span>

                                                            </a>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach


                        <!-- PAGINATION -->

                        @if($pastLoans->hasPages())

                            <div class="pt-4">

                                {{ $pastLoans->links() }}

                            </div>

                        @endif

                    </div>

                @endif

            </section>


        </div>

    </div>

</x-app-layout>