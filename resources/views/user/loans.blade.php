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

                    @endphp


                    <div class="mb-6 overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">


                        <!-- HEADER ORDER -->

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


                        <!-- ORDER BODY -->

                        <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-12 sm:p-8">


                            <!-- LEFT -->

                            <div class="lg:col-span-7">

                                <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Transaction Type
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">
                                            {{ $transactionType }}
                                        </p>

                                    </div>


                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Organization
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">
                                            {{ $order->organization }}
                                        </p>

                                    </div>


                                    <div class="rounded-2xl bg-gray-50 p-4">

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Proker / Event
                                        </p>

                                        <p class="mt-2 text-sm font-black text-gray-900">
                                            {{ $order->proker_name }}
                                        </p>

                                    </div>


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


                                <!-- ITEMS -->

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

                                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">

                                                            @if($item->item_photo)

                                                                <img
                                                                    src="{{ asset('storage/' . $item->item_photo) }}"
                                                                    class="h-full w-full object-cover"
                                                                    alt="{{ $item->name }}"
                                                                >

                                                            @endif

                                                        </div>


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


                            <!-- RIGHT -->

                            <div class="lg:col-span-5">

                                <div class="rounded-[1.75rem] bg-gray-950 p-6 text-white">

                                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Grand Total
                                    </p>

                                    <p class="mt-2 text-3xl font-black">
                                        Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                    </p>

                                </div>


                                <!-- DOCUMENTS -->

                                <div class="mt-5 space-y-3">

                                    <a
                                        href="{{ route('student.document.invoice', $order->id) }}"
                                        target="_blank"
                                        class="flex w-full items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-3 text-[9px] font-black uppercase tracking-widest text-gray-700 transition hover:border-indigo-200 hover:text-indigo-600"
                                    >
                                        Download Invoice
                                    </a>


                                    @if($mouDocuments->count() > 0)

                                        <div class="rounded-2xl border border-purple-100 bg-purple-50 p-4">

                                            <p class="mb-3 text-[9px] font-black uppercase tracking-widest text-purple-700">
                                                MoU Documents
                                            </p>


                                            <div class="space-y-3">

                                                @foreach($mouDocuments as $document)

                                                    @php
                                                        $mouLabel = match($document->mou_type) {
                                                            'ht' => 'MoU Handy Talkie',
                                                            'internal' => 'MoU Internal Rental',
                                                            'vendor' => 'MoU Vendor Rental',
                                                            'merch_baju' => 'MoU Baju',
                                                            'merch_idcard' => 'MoU ID Card',
                                                            default => 'MoU',
                                                        };
                                                    @endphp


                                                    <div class="rounded-xl border border-purple-100 bg-white p-3">

                                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                                                            <p class="text-[9px] font-black text-gray-800">
                                                                {{ $mouLabel }}
                                                            </p>


                                                            <a
                                                                href="{{ route('student.document.mou', [$order->id, $document->id]) }}"
                                                                target="_blank"
                                                                class="text-[9px] font-black text-purple-600 hover:underline"
                                                            >
                                                                Download
                                                            </a>

                                                        </div>


                                                        @if($document->signed_file_path)

                                                            <p class="mt-2 text-[9px] font-bold text-emerald-600">
                                                                ✓ Signed MoU sudah diupload
                                                            </p>

                                                        @else

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
                                                                    class="mt-2 w-full rounded-xl bg-purple-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white hover:bg-purple-700"
                                                                >
                                                                    Upload Signed MoU
                                                                </button>

                                                            </form>

                                                        @endif

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif


                                    @if($order->status === 'Waiting for Payment')

                                        <div class="rounded-2xl border border-orange-100 bg-orange-50 p-4">

                                            <p class="text-[9px] font-black uppercase tracking-widest text-orange-700">
                                                Payment
                                            </p>

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
                                                    class="mt-2 w-full rounded-xl bg-orange-500 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white hover:bg-orange-600"
                                                >
                                                    Upload Bukti Pembayaran
                                                </button>

                                            </form>

                                        </div>

                                    @endif


                                    @if($order->payment_receipt)

                                        <a
                                            href="{{ asset('storage/' . $order->payment_receipt) }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-orange-700"
                                        >
                                            Lihat Bukti Pembayaran
                                        </a>

                                    @endif


                                    @if($order->status === 'Waiting for Kwitansi')

                                        <form
                                            action="{{ route('student.orders.upload-kwitansi', $order->id) }}"
                                            method="POST"
                                            enctype="multipart/form-data"
                                            class="rounded-2xl border border-pink-100 bg-pink-50 p-4"
                                        >

                                            @csrf

                                            <p class="text-[9px] font-black uppercase tracking-widest text-pink-700">
                                                Signed Kwitansi
                                            </p>

                                            <input
                                                type="file"
                                                name="signed_kwitansi"
                                                accept=".pdf"
                                                required
                                                class="mt-3 block w-full rounded-xl border border-pink-200 bg-white px-3 py-2 text-[9px] font-semibold text-gray-600"
                                            >

                                            <button
                                                type="submit"
                                                class="mt-2 w-full rounded-xl bg-pink-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white hover:bg-pink-700"
                                            >
                                                Upload Signed Kwitansi
                                            </button>

                                        </form>

                                    @endif


                                    @if($order->signed_kwitansi)

                                        <a
                                            href="{{ asset('storage/' . $order->signed_kwitansi) }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-2xl border border-pink-200 bg-pink-50 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-pink-700"
                                        >
                                            Lihat Signed Kwitansi
                                        </a>

                                    @endif


                                    @if(in_array(
                                        $transactionType,
                                        ['Peralatan', 'Handy Talkie'],
                                        true
                                    ))

                                        @if(in_array(
                                            $order->status,
                                            [
                                                'Handed Over',
                                                'Pending Return Review',
                                            ],
                                            true
                                        ))

                                            <form
                                                action="{{ route('student.orders.return-link', $order->id) }}"
                                                method="POST"
                                                class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4"
                                            >

                                                @csrf

                                                <p class="text-[9px] font-black uppercase tracking-widest text-cyan-700">
                                                    Return Evidence
                                                </p>

                                                <input
                                                    type="url"
                                                    name="return_drive_link"
                                                    value="{{ $order->return_drive_link }}"
                                                    placeholder="https://drive.google.com/..."
                                                    required
                                                    class="mt-3 block w-full rounded-xl border border-cyan-200 bg-white px-3 py-3 text-xs font-semibold text-gray-700"
                                                >

                                                <button
                                                    type="submit"
                                                    class="mt-2 w-full rounded-xl bg-cyan-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white hover:bg-cyan-700"
                                                >
                                                    Kirim Bukti Return
                                                </button>

                                            </form>

                                        @endif

                                    @endif


                                    @if($order->return_drive_link)

                                        <a
                                            href="{{ $order->return_drive_link }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-cyan-700"
                                        >
                                            Lihat Bukti Return
                                        </a>

                                    @endif


                                    @if($order->signed_ba_file)

                                        <a
                                            href="{{ asset('storage/' . $order->signed_ba_file) }}"
                                            target="_blank"
                                            class="flex w-full items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-emerald-700"
                                        >
                                            Lihat Signed BA
                                        </a>

                                    @endif

                                </div>

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