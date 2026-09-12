<x-app-layout>

    <div class="min-h-screen bg-[#f8f9fa] pb-12">

        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 shadow-sm backdrop-blur-xl">

            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg">

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
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 016 0"
                                />

                            </svg>

                        </div>

                        <div>

                            <h1 class="text-xl font-black tracking-tight text-gray-950 sm:text-2xl">
                                Transaction History
                            </h1>

                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 sm:text-[10px]">
                                Your Requests & Transaction Status
                            </p>

                        </div>

                    </div>


                    <a
                        href="{{ route('student.dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-700 transition hover:border-indigo-200 hover:text-indigo-600"
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



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">


            <!-- ===================================================== -->
            <!-- FLASH -->
            <!-- ===================================================== -->

            @if(session('success'))

                <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4">

                    <p class="text-sm font-bold text-emerald-800">
                        {{ session('success') }}
                    </p>

                </div>

            @endif


            @if(session('error'))

                <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-5 py-4">

                    <p class="text-sm font-bold text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- ACTIVE -->
            <!-- ===================================================== -->

            <div class="mb-10">

                <div class="mb-5 flex items-end justify-between gap-4">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-500">
                            Current
                        </p>

                        <h2 class="mt-1 text-2xl font-black tracking-tight text-gray-900">
                            Transaksi Aktif
                        </h2>

                    </div>


                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-indigo-600">
                        {{ $activeLoans->count() }} Transaksi
                    </span>

                </div>



                @forelse($activeLoans as $order)

                    @php

                        $transactionType =
                            $order->order_type;

                        $mouDocuments =
                            $order->mouDocuments;

                        $status =
                            $order->status;

                    @endphp


                    <div class="mb-6 overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">


                        <!-- ================================================= -->
                        <!-- HEADER ORDER -->
                        <!-- ================================================= -->

                        <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-5 sm:px-8">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Order Number
                                    </p>

                                    <h3 class="mt-1 text-lg font-black text-gray-900">
                                        {{ $order->order_number }}
                                    </h3>

                                </div>


                                <div class="text-left sm:text-right">

                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Status
                                    </p>

                                    <span class="mt-1 inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-indigo-700">
                                        {{ $order->status }}
                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- ================================================= -->
                        <!-- ORDER BODY -->
                        <!-- ================================================= -->

                        <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-12 sm:p-8">


                            <!-- ================================================= -->
                            <!-- LEFT -->
                            <!-- ================================================= -->

                            <div class="lg:col-span-7">

                                <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">


                                    <!-- TRANSACTION TYPE -->

                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Transaction Type
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">
                                            {{ $transactionType }}
                                        </p>

                                    </div>



                                    <!-- ORGANIZATION -->

                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Organization
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">
                                            {{ $order->organization }}
                                        </p>

                                    </div>



                                    <!-- PROKER -->

                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Proker / Event
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">
                                            {{ $order->proker_name }}
                                        </p>

                                    </div>



                                    <!-- DATE -->

                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Date
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">

                                            @if($order->start_date)

                                                {{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }}

                                                @if($order->end_date)

                                                    <span class="text-gray-400">
                                                        →
                                                    </span>

                                                    {{ \Carbon\Carbon::parse($order->end_date)->format('d M Y') }}

                                                @endif

                                            @else

                                                -

                                            @endif

                                        </p>

                                    </div>

                                </div>



                                <!-- ================================================= -->
                                <!-- ITEMS -->
                                <!-- ================================================= -->

                                <div>

                                    <div class="mb-3 flex items-center justify-between">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Barang
                                        </p>

                                        <span class="text-[9px] font-bold text-gray-400">
                                            {{ $order->orderItems->count() }} item
                                        </span>

                                    </div>


                                    <div class="space-y-3">

                                        @foreach($order->orderItems as $detail)

                                            @php
                                                $item = $detail->item;
                                            @endphp


                                            @if($item)

                                                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                                    <div class="flex gap-4">


                                                        <!-- ITEM PHOTO -->

                                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">

                                                            @if($item->item_photo)

                                                                <img
                                                                    src="{{ asset('storage/' . $item->item_photo) }}"
                                                                    class="h-full w-full object-cover"
                                                                    alt="{{ $item->name }}"
                                                                >

                                                            @endif

                                                        </div>



                                                        <!-- ITEM INFO -->

                                                        <div class="min-w-0 flex-1">

                                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">

                                                                <div>

                                                                    <p class="text-sm font-black text-gray-900">
                                                                        {{ $item->name }}
                                                                    </p>

                                                                    <p class="mt-1 text-[9px] font-bold text-gray-400">

                                                                        Category:
                                                                        {{ optional($item->category)->name ?? '-' }}

                                                                    </p>

                                                                </div>


                                                                <span class="shrink-0 rounded-lg bg-white px-2.5 py-1 text-[9px] font-black text-gray-600">
                                                                    {{ $detail->quantity }}x
                                                                </span>

                                                            </div>


                                                            @if($item->transaction_detail)

                                                                <p class="mt-2 text-[9px] font-black uppercase tracking-widest text-indigo-500">

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


                                                            @if($detail->design_link)

                                                                <a
                                                                    href="{{ $detail->design_link }}"
                                                                    target="_blank"
                                                                    class="mt-1 inline-block text-[9px] font-black text-indigo-600 hover:underline"
                                                                >
                                                                    Lihat Design Link
                                                                </a>

                                                            @endif


                                                            <div class="mt-3 flex items-center justify-between border-t border-gray-200 pt-3">

                                                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                                    Subtotal
                                                                </span>

                                                                <span class="text-sm font-black text-gray-900">
                                                                    Rp {{ number_format($detail->subtotal_price ?? 0, 0, ',', '.') }}
                                                                </span>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            @endif

                                        @endforeach

                                    </div>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- RIGHT -->
                            <!-- ================================================= -->

                            <div class="lg:col-span-5">


                                <!-- ================================================= -->
                                <!-- GRAND TOTAL -->
                                <!-- ================================================= -->

                                <div class="rounded-[1.75rem] bg-gray-950 p-6 text-white">

                                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Grand Total
                                    </p>

                                    <p class="mt-2 text-3xl font-black">
                                        Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                    </p>

                                </div>



                                <!-- ================================================= -->
                                <!-- STEP 1 : MOU -->
                                <!-- ================================================= -->

                                @if(
                                    $status === 'Waiting for MoU' ||
                                    $status === 'Pending Review MoU'
                                )

                                    @if($mouDocuments->count() > 0)

                                        <div class="mt-5 rounded-2xl border border-purple-100 bg-purple-50 p-4">


                                            <div class="mb-4">

                                                <p class="text-[9px] font-black uppercase tracking-widest text-purple-700">
                                                    Step 1
                                                </p>

                                                <p class="mt-1 text-sm font-black text-gray-900">
                                                    MoU Documents
                                                </p>

                                                <p class="mt-1 text-[9px] leading-relaxed text-purple-600">
                                                    Lihat dan unduh MoU terlebih dahulu sebelum mengunggah dokumen yang sudah ditandatangani.
                                                </p>

                                            </div>



                                            <div class="space-y-3">

                                                @foreach($mouDocuments as $document)

                                                    @php

                                                        $mouLabel = match(
                                                            $document->mou_type
                                                        ) {

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

                                                            default =>
                                                                'MoU',

                                                        };

                                                    @endphp


                                                    <div class="rounded-xl border border-purple-100 bg-white p-3">


                                                        <p class="text-[9px] font-black text-gray-800">
                                                            {{ $mouLabel }}
                                                        </p>



                                                        <!-- DOWNLOAD MOU -->

                                                        <a
                                                            href="{{ route('student.document.mou', [$order->id, $document->id]) }}"
                                                            target="_blank"
                                                            class="mt-3 flex w-full items-center justify-center rounded-xl border border-purple-200 bg-purple-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-purple-700 transition hover:bg-purple-100"
                                                        >
                                                            Download MoU
                                                        </a>



                                                        @if($document->signed_file_path)

                                                            <div class="mt-3 rounded-xl bg-emerald-50 px-3 py-2">

                                                                <p class="text-[9px] font-black text-emerald-700">
                                                                    ✓ Signed MoU sudah diupload
                                                                </p>

                                                            </div>

                                                        @else

                                                            @if($status === 'Waiting for MoU')

                                                                <form
                                                                    action="{{ route('student.orders.upload-mou', [$order->id, $document->id]) }}"
                                                                    method="POST"
                                                                    enctype="multipart/form-data"
                                                                    class="mt-3"
                                                                >

                                                                    @csrf


                                                                    <input
                                                                        type="file"
                                                                        name="signed_mou"
                                                                        accept=".pdf"
                                                                        required
                                                                        class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-[9px] font-semibold text-gray-600"
                                                                    >


                                                                    <button
                                                                        type="submit"
                                                                        class="mt-2 w-full rounded-xl bg-purple-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-purple-700"
                                                                    >
                                                                        Upload Signed MoU
                                                                    </button>

                                                                </form>

                                                            @else

                                                                <div class="mt-3 rounded-xl bg-yellow-50 px-3 py-2">

                                                                    <p class="text-[9px] font-black text-yellow-700">
                                                                        Menunggu pemeriksaan admin.
                                                                    </p>

                                                                </div>

                                                            @endif

                                                        @endif

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif

                                @endif



                                <!-- ================================================= -->
                                <!-- STEP 2 : PAYMENT -->
                                <!-- ================================================= -->

                                @if(
                                    $status === 'Waiting for Payment' ||
                                    $status === 'Pending Review Payment'
                                )

                                    <div class="mt-5 rounded-2xl border border-orange-100 bg-orange-50 p-4">


                                        <div class="mb-4">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-orange-700">
                                                Step 2
                                            </p>

                                            <p class="mt-1 text-sm font-black text-gray-900">
                                                Payment
                                            </p>

                                            <p class="mt-1 text-[9px] leading-relaxed text-orange-600">
                                                Lihat invoice terlebih dahulu, kemudian unggah bukti pembayaran.
                                            </p>

                                        </div>



                                        <!-- DOWNLOAD INVOICE -->

                                        <a
                                            href="{{ route('student.document.invoice', $order->id) }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-xl border border-orange-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-orange-700 transition hover:bg-orange-100"
                                        >
                                            Download Invoice
                                        </a>



                                        @if($status === 'Waiting for Payment')

                                            <form
                                                action="{{ route('student.orders.upload-payment', $order->id) }}"
                                                method="POST"
                                                enctype="multipart/form-data"
                                                class="mt-3"
                                            >

                                                @csrf


                                                <input
                                                    type="file"
                                                    name="payment_receipt"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    required
                                                    class="block w-full rounded-xl border border-orange-200 bg-white px-3 py-2 text-[9px] font-semibold text-gray-600"
                                                >


                                                <button
                                                    type="submit"
                                                    class="mt-2 w-full rounded-xl bg-orange-500 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-orange-600"
                                                >
                                                    Upload Bukti Pembayaran
                                                </button>

                                            </form>

                                        @else

                                            <div class="mt-3 rounded-xl bg-yellow-50 px-3 py-2">

                                                <p class="text-[9px] font-black text-yellow-700">
                                                    Bukti pembayaran sedang diperiksa admin.
                                                </p>

                                            </div>

                                        @endif



                                        @if($order->payment_receipt)

                                            <a
                                                href="{{ asset('storage/' . $order->payment_receipt) }}"
                                                target="_blank"
                                                class="mt-3 flex w-full items-center justify-center rounded-xl border border-orange-200 bg-white px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-orange-700 transition hover:bg-orange-100"
                                            >
                                                Lihat Bukti Pembayaran
                                            </a>

                                        @endif

                                    </div>

                                @endif



                                <!-- ================================================= -->
                                <!-- STEP 3 : KWITANSI -->
                                <!-- ================================================= -->

                                @if(
                                    $status === 'Waiting for Kwitansi' ||
                                    $status === 'Pending Review Kwitansi'
                                )

                                    <div class="mt-5 rounded-2xl border border-pink-100 bg-pink-50 p-4">


                                        <div class="mb-4">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-pink-700">
                                                Step 3
                                            </p>

                                            <p class="mt-1 text-sm font-black text-gray-900">
                                                Kwitansi
                                            </p>

                                            <p class="mt-1 text-[9px] leading-relaxed text-pink-600">
                                                Lihat kwitansi terlebih dahulu, lalu unggah kwitansi yang sudah ditandatangani.
                                            </p>

                                        </div>



                                        <!-- DOWNLOAD KWITANSI -->

                                        <a
                                            href="{{ route('student.document.kwitansi', $order->id) }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-xl border border-pink-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-pink-700 transition hover:bg-pink-100"
                                        >
                                            Download Kwitansi
                                        </a>



                                        @if($status === 'Waiting for Kwitansi')

                                            <form
                                                action="{{ route('student.orders.upload-kwitansi', $order->id) }}"
                                                method="POST"
                                                enctype="multipart/form-data"
                                                class="mt-3"
                                            >

                                                @csrf


                                                <input
                                                    type="file"
                                                    name="signed_kwitansi"
                                                    accept=".pdf"
                                                    required
                                                    class="block w-full rounded-xl border border-pink-200 bg-white px-3 py-2 text-[9px] font-semibold text-gray-600"
                                                >


                                                <button
                                                    type="submit"
                                                    class="mt-2 w-full rounded-xl bg-pink-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-pink-700"
                                                >
                                                    Upload Signed Kwitansi
                                                </button>

                                            </form>

                                        @else

                                            <div class="mt-3 rounded-xl bg-yellow-50 px-3 py-2">

                                                <p class="text-[9px] font-black text-yellow-700">
                                                    Signed kwitansi sedang diperiksa admin.
                                                </p>

                                            </div>

                                        @endif



                                        @if($order->signed_kwitansi)

                                            <a
                                                href="{{ asset('storage/' . $order->signed_kwitansi) }}"
                                                target="_blank"
                                                class="mt-3 flex w-full items-center justify-center rounded-xl border border-pink-200 bg-white px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-pink-700 transition hover:bg-pink-100"
                                            >
                                                Lihat Signed Kwitansi
                                            </a>

                                        @endif

                                    </div>

                                @endif



                                <!-- ================================================= -->
                                <!-- STEP 4 : RETURN -->
                                <!-- ================================================= -->

                                @if(
                                    in_array(
                                        $transactionType,
                                        [
                                            'Peralatan',
                                            'Handy Talkie'
                                        ],
                                        true
                                    )
                                )

                                    @if(
                                        $status === 'Handed Over' ||
                                        $status === 'Pending Return Review'
                                    )

                                        <div class="mt-5 rounded-2xl border border-cyan-100 bg-cyan-50 p-4">


                                            <div class="mb-4">

                                                <p class="text-[9px] font-black uppercase tracking-widest text-cyan-700">
                                                    Step 4
                                                </p>

                                                <p class="mt-1 text-sm font-black text-gray-900">
                                                    Return Evidence
                                                </p>

                                                <p class="mt-1 text-[9px] leading-relaxed text-cyan-600">
                                                    Kirim link bukti pengembalian barang.
                                                </p>

                                            </div>



                                            @if($order->return_drive_link)

                                                <a
                                                    href="{{ $order->return_drive_link }}"
                                                    target="_blank"
                                                    class="flex w-full items-center justify-center rounded-xl border border-cyan-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-cyan-700 transition hover:bg-cyan-100"
                                                >
                                                    Lihat Bukti Return
                                                </a>

                                            @endif



                                            @if($status === 'Handed Over')

                                                <form
                                                    action="{{ route('student.orders.return-link', $order->id) }}"
                                                    method="POST"
                                                    class="{{ $order->return_drive_link ? 'mt-3' : '' }}"
                                                >

                                                    @csrf


                                                    <input
                                                        type="url"
                                                        name="return_drive_link"
                                                        value="{{ $order->return_drive_link }}"
                                                        placeholder="https://drive.google.com/..."
                                                        required
                                                        class="block w-full rounded-xl border border-cyan-200 bg-white px-3 py-3 text-xs font-semibold text-gray-700"
                                                    >


                                                    <button
                                                        type="submit"
                                                        class="mt-2 w-full rounded-xl bg-cyan-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-cyan-700"
                                                    >
                                                        {{ $order->return_drive_link ? 'Update Bukti Return' : 'Kirim Bukti Return' }}
                                                    </button>

                                                </form>

                                            @else

                                                <div class="mt-3 rounded-xl bg-yellow-50 px-3 py-2">

                                                    <p class="text-[9px] font-black text-yellow-700">
                                                        Bukti return sedang diperiksa admin.
                                                    </p>

                                                </div>

                                            @endif

                                        </div>

                                    @endif

                                @endif



                                <!-- ================================================= -->
                                <!-- STEP 5 : BERITA ACARA -->
                                <!-- ================================================= -->

                                @if(
                                    $status === 'Returned (Damaged)' ||
                                    $status === 'Pending Review BA'
                                )

                                    <div class="mt-5 rounded-2xl border border-red-100 bg-red-50 p-4">


                                        <div class="mb-4">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-red-700">
                                                Step 5
                                            </p>

                                            <p class="mt-1 text-sm font-black text-gray-900">
                                                Berita Acara
                                            </p>

                                            <p class="mt-1 text-[9px] leading-relaxed text-red-600">
                                                Lihat Berita Acara terlebih dahulu, lalu unggah dokumen yang sudah ditandatangani.
                                            </p>

                                        </div>



                                        <!-- DOWNLOAD BA -->

                                        <a
                                            href="{{ route('student.document.berita-acara', $order->id) }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-red-700 transition hover:bg-red-100"
                                        >
                                            Download Berita Acara
                                        </a>



                                        @if($status === 'Returned (Damaged)')

                                            <form
                                                action="{{ route('student.orders.upload-ba', $order->id) }}"
                                                method="POST"
                                                enctype="multipart/form-data"
                                                class="mt-3"
                                            >

                                                @csrf


                                                <input
                                                    type="file"
                                                    name="signed_ba_file"
                                                    accept=".pdf"
                                                    required
                                                    class="block w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-[9px] font-semibold text-gray-600"
                                                >


                                                <button
                                                    type="submit"
                                                    class="mt-2 w-full rounded-xl bg-red-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-red-700"
                                                >
                                                    Upload Signed BA
                                                </button>

                                            </form>

                                        @else

                                            <div class="mt-3 rounded-xl bg-yellow-50 px-3 py-2">

                                                <p class="text-[9px] font-black text-yellow-700">
                                                    Signed BA sedang diperiksa admin.
                                                </p>

                                            </div>

                                        @endif



                                        @if($order->signed_ba_file)

                                            <a
                                                href="{{ asset('storage/' . $order->signed_ba_file) }}"
                                                target="_blank"
                                                class="mt-3 flex w-full items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-red-700 transition hover:bg-red-100"
                                            >
                                                Lihat Signed BA
                                            </a>

                                        @endif

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="rounded-[2rem] border border-dashed border-gray-200 bg-white px-6 py-20 text-center">

                        <h3 class="text-xl font-black text-gray-900">
                            Belum ada transaksi aktif
                        </h3>

                        <p class="mt-2 text-sm font-semibold text-gray-400">
                            Transaksi yang sedang berjalan akan muncul di sini.
                        </p>

                    </div>

                @endforelse

            </div>



            <!-- ===================================================== -->
            <!-- PAST -->
            <!-- ===================================================== -->

            <div>

                <div class="mb-5 flex items-end justify-between gap-4">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">
                            Archive
                        </p>

                        <h2 class="mt-1 text-2xl font-black tracking-tight text-gray-900">
                            Riwayat Transaksi
                        </h2>

                    </div>


                    <span class="rounded-full bg-gray-100 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-gray-500">
                        {{ $pastLoans->total() }} Transaksi
                    </span>

                </div>


                @forelse($pastLoans as $order)

                    <div class="mb-4 rounded-[1.75rem] border border-gray-100 bg-white p-5 shadow-sm">

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">


                            <div>

                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                    {{ $order->order_number }}
                                </p>

                                <h3 class="mt-1 text-base font-black text-gray-900">
                                    {{ $order->proker_name }}
                                </h3>

                                <p class="mt-1 text-[10px] font-bold text-gray-400">

                                    {{ $order->order_type }}

                                    •

                                    {{ $order->created_at->format('d M Y') }}

                                </p>

                            </div>


                            <div class="flex flex-wrap items-center gap-3">

                                <span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-gray-600">
                                    {{ $order->status }}
                                </span>


                                <span class="text-sm font-black text-gray-900">
                                    Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                </span>


                                <a
                                    href="{{ route('student.document.invoice', $order->id) }}"
                                    target="_blank"
                                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-[9px] font-black uppercase tracking-widest text-gray-600 hover:text-indigo-600"
                                >
                                    Invoice
                                </a>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="rounded-[2rem] border border-dashed border-gray-200 bg-white px-6 py-20 text-center">

                        <h3 class="text-xl font-black text-gray-900">
                            Belum ada riwayat transaksi
                        </h3>

                    </div>

                @endforelse


                <div class="mt-6">

                    {{ $pastLoans->links() }}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>