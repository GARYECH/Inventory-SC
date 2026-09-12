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
                                Order Management
                            </h1>

                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600">
                                Student Council Inventory
                            </p>

                        </div>

                    </div>


                    <div class="flex w-full gap-3 md:w-auto">

                        <form
                            action="{{ route('admin.orders') }}"
                            method="GET"
                            class="w-full md:w-80"
                        >

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari No. Order / Mahasiswa..."
                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10"
                            >

                        </form>


                        <a
                            href="{{ route('admin.orders.export') }}"
                            class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-700"
                        >
                            Export Excel
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">


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
            <!-- ORDERS -->
            <!-- ===================================================== -->

            <div class="space-y-6">

                @forelse($orders as $order)

                    @php

                        $statusClasses = [

                            'Pending' =>
                                'border-amber-200 bg-amber-50 text-amber-700',

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

                    @endphp


                    <div class="overflow-hidden rounded-[2rem] border border-gray-100 bg-white shadow-sm">


                        <!-- ================================================= -->
                        <!-- ORDER HEADER -->
                        <!-- ================================================= -->

                        <div class="border-b border-gray-100 bg-gray-50/60 px-6 py-5 sm:px-8">

                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                                <div>

                                    <div class="flex flex-wrap items-center gap-3">

                                        <h2 class="text-lg font-black text-gray-900">
                                            {{ $order->order_number }}
                                        </h2>

                                        <span class="rounded-full border px-3 py-1 text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                            {{ $order->status }}
                                        </span>

                                    </div>

                                    <p class="mt-1 text-[10px] font-bold text-gray-400">
                                        Dibuat {{ $order->created_at->format('d M Y H:i') }}
                                    </p>

                                </div>


                                <div class="text-left lg:text-right">

                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Grand Total
                                    </p>

                                    <p class="mt-1 text-2xl font-black text-gray-950">
                                        Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- BODY -->
                        <!-- ================================================= -->

                        <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-12 sm:p-8">


                            <!-- ================================================= -->
                            <!-- INFO -->
                            <!-- ================================================= -->

                            <div class="xl:col-span-3">

                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                    Customer
                                </p>

                                <h3 class="mt-2 text-base font-black text-gray-900">
                                    {{ $order->user->name }}
                                </h3>


                                <div class="mt-5 space-y-4">

                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Organization
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            {{ $order->organization }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Position
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            {{ $order->position }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Phone
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            {{ $order->phone_number }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Proker
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-gray-800">
                                            {{ $order->proker_name }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                            Transaction Type
                                        </p>

                                        <span class="mt-1 inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest text-indigo-700">
                                            {{ $order->order_type }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- ITEMS -->
                            <!-- ================================================= -->

                            <div class="xl:col-span-5">

                                <div class="mb-3 flex items-center justify-between">

                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Item Details
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

                                                <div class="flex gap-3">


                                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">

                                                        @if($item->item_photo)

                                                            <img
                                                                src="{{ asset('storage/' . $item->item_photo) }}"
                                                                alt="{{ $item->name }}"
                                                                class="h-full w-full object-cover"
                                                            >

                                                        @endif

                                                    </div>


                                                    <div class="min-w-0 flex-1">

                                                        <div class="flex items-start justify-between gap-3">

                                                            <div>

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


                                                        @if($detail->design_link)

                                                            <a
                                                                href="{{ $detail->design_link }}"
                                                                target="_blank"
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


                            <!-- ================================================= -->
                            <!-- ACTION -->
                            <!-- ================================================= -->

                            <div class="xl:col-span-4">

                                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">

                                    <p class="mb-3 text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Documents
                                    </p>


                                    <div class="space-y-2">


                                        @if($order->payment_receipt)

                                            <a
                                                href="{{ asset('storage/' . $order->payment_receipt) }}"
                                                target="_blank"
                                                class="flex w-full items-center justify-center rounded-xl border border-orange-200 bg-orange-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-orange-700"
                                            >
                                                View Payment Receipt
                                            </a>

                                        @endif


                                        @if($order->signed_kwitansi)

                                            <a
                                                href="{{ asset('storage/' . $order->signed_kwitansi) }}"
                                                target="_blank"
                                                class="flex w-full items-center justify-center rounded-xl border border-pink-200 bg-pink-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-pink-700"
                                            >
                                                View Signed Kwitansi
                                            </a>

                                        @endif


                                        @if($order->return_drive_link)

                                            <a
                                                href="{{ $order->return_drive_link }}"
                                                target="_blank"
                                                class="flex w-full items-center justify-center rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-cyan-700"
                                            >
                                                View Return Evidence
                                            </a>

                                        @endif


                                        @if($order->signed_ba_file)

                                            <a
                                                href="{{ asset('storage/' . $order->signed_ba_file) }}"
                                                target="_blank"
                                                class="flex w-full items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-emerald-700"
                                            >
                                                View Signed BA
                                            </a>

                                        @endif


                                    </div>

                                </div>


                                <!-- MOU DOCUMENTS -->

                                @if($requiresMou)

                                    <div class="mt-4 rounded-2xl border border-purple-100 bg-purple-50 p-4">

                                        <p class="mb-3 text-[9px] font-black uppercase tracking-widest text-purple-700">
                                            MoU Documents
                                        </p>


                                        <div class="space-y-3">

                                            @foreach($order->mouDocuments as $document)

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

                                                    <div class="flex items-center justify-between gap-3">

                                                        <p class="text-[9px] font-black text-gray-800">
                                                            {{ $mouLabel }}
                                                        </p>

                                                        <a
                                                            href="{{ asset('storage/' . $document->signed_file_path) }}"
                                                            target="_blank"
                                                            class="text-[9px] font-black text-purple-600 hover:underline {{ !$document->signed_file_path ? 'pointer-events-none opacity-40' : '' }}"
                                                        >
                                                            @if($document->signed_file_path)
                                                                View Signed
                                                            @else
                                                                Not Uploaded
                                                            @endif
                                                        </a>

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif


                                <!-- UPDATE FORM -->

                                <form
                                    action="{{ route('admin.orders.update', $order->id) }}"
                                    method="POST"
                                    class="mt-4 rounded-2xl border border-gray-100 bg-white p-4"
                                >

                                    @csrf
                                    @method('PATCH')


                                    <p class="mb-3 text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Update Transaction
                                    </p>


                                    <div class="space-y-3">


                                        <div>

                                            <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-gray-500">
                                                Status
                                            </label>

                                            <select
                                                name="status"
                                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-xs font-bold text-gray-800 outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10"
                                            >

                                                @foreach([
                                                    'Pending',
                                                    'Waiting for MoU',
                                                    'Pending Review MoU',
                                                    'Waiting for Payment',
                                                    'Pending Review Payment',
                                                    'Waiting for Kwitansi',
                                                    'Pending Review Kwitansi',
                                                    'Handed Over',
                                                    'Pending Return Review',
                                                    'Returned',
                                                    'Returned (Damaged)',
                                                    'Pending Review BA',
                                                    'Resolved (Fine Paid)',
                                                    'Rejected',
                                                    'Cancelled',
                                                ] as $status)

                                                    <option
                                                        value="{{ $status }}"
                                                        {{ $order->status === $status ? 'selected' : '' }}
                                                    >
                                                        {{ $status }}
                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>


                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">

                                            <div>

                                                <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                    MoU Number
                                                </label>

                                                <input
                                                    type="text"
                                                    name="mou_number"
                                                    value="{{ $order->mou_number }}"
                                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-[10px] font-bold"
                                                >

                                            </div>


                                            <div>

                                                <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                    Invoice Number
                                                </label>

                                                <input
                                                    type="text"
                                                    name="invoice_number"
                                                    value="{{ $order->invoice_number }}"
                                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-[10px] font-bold"
                                                >

                                            </div>


                                            <div>

                                                <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                    Kwitansi Number
                                                </label>

                                                <input
                                                    type="text"
                                                    name="kwitansi_number"
                                                    value="{{ $order->kwitansi_number }}"
                                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-[10px] font-bold"
                                                >

                                            </div>

                                        </div>


                                        @if(
                                            $order->status === 'Returned (Damaged)' ||
                                            $order->status === 'Pending Review BA'
                                        )

                                            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                                                <p class="mb-3 text-[9px] font-black uppercase tracking-widest text-red-700">
                                                    Berita Acara & Denda
                                                </p>


                                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">


                                                    <div>

                                                        <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-500">
                                                            BA Number
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="ba_number"
                                                            value="{{ $order->ba_number }}"
                                                            class="w-full rounded-xl border border-red-200 bg-white px-3 py-2.5 text-[10px] font-bold"
                                                        >

                                                    </div>


                                                    <div>

                                                        <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-500">
                                                            BA Date
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="ba_date"
                                                            value="{{ $order->ba_date }}"
                                                            class="w-full rounded-xl border border-red-200 bg-white px-3 py-2.5 text-[10px] font-bold"
                                                        >

                                                    </div>


                                                    <div class="sm:col-span-2">

                                                        <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-500">
                                                            Description
                                                        </label>

                                                        <textarea
                                                            name="ba_description"
                                                            rows="3"
                                                            class="w-full rounded-xl border border-red-200 bg-white px-3 py-2.5 text-[10px] font-bold"
                                                        >{{ $order->ba_description }}</textarea>

                                                    </div>


                                                    <div>

                                                        <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-gray-500">
                                                            Due Date
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="ba_due_date"
                                                            value="{{ $order->ba_due_date }}"
                                                            class="w-full rounded-xl border border-red-200 bg-white px-3 py-2.5 text-[10px] font-bold"
                                                        >

                                                    </div>


                                                    <div>

                                                        <label class="mb-1 block text-[8px] font-black uppercase tracking-widest text-red-600">
                                                            Total Fine
                                                        </label>

                                                        <input
                                                            type="number"
                                                            name="ba_total_fine"
                                                            value="{{ $order->ba_total_fine }}"
                                                            class="w-full rounded-xl border border-red-200 bg-red-100 px-3 py-2.5 text-[10px] font-black text-red-800"
                                                        >

                                                    </div>

                                                </div>

                                            </div>

                                        @endif


                                        <button
                                            type="submit"
                                            class="w-full rounded-xl bg-gray-950 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-indigo-600"
                                        >
                                            Update Transaction
                                        </button>

                                    </div>

                                </form>


                                <!-- DELETE -->

                                <form
                                    action="{{ route('admin.orders.destroy', $order->id) }}"
                                    method="POST"
                                    class="mt-2"
                                    onsubmit="return confirm('Hapus transaksi ini?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-red-600 transition hover:bg-red-600 hover:text-white"
                                    >
                                        Delete Transaction
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="rounded-[2rem] border border-dashed border-gray-200 bg-white px-6 py-24 text-center">

                        <h3 class="text-xl font-black text-gray-900">
                            Belum ada transaksi
                        </h3>

                        <p class="mt-2 text-sm font-semibold text-gray-400">
                            Order dari student akan muncul di halaman ini.
                        </p>

                    </div>

                @endforelse

            </div>


            <!-- ===================================================== -->
            <!-- PAGINATION -->
            <!-- ===================================================== -->

            <div class="mt-8">

                {{ $orders->links() }}

            </div>

        </div>

    </div>

</x-app-layout>