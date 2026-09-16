.<x-app-layout>

    <div class="min-h-screen bg-[#f8f9fa] pb-12">

        <div class="border-b border-gray-100 bg-white shadow-sm">

            <div class="mx-auto max-w-5xl px-4 py-5 sm:px-6 lg:px-8">

                <div class="flex items-center gap-4">

                    <a
                        href="{{ route('student.dashboard') }}"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:text-indigo-600"
                    >
                        ←
                    </a>

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600">
                            Merchandise
                        </p>

                        <h1 class="mt-1 text-2xl font-black text-gray-950">
                            Detail Pesanan Baju
                        </h1>

                    </div>

                </div>

            </div>

        </div>


        <div class="mx-auto max-w-5xl px-4 pt-8 sm:px-6 lg:px-8">

            @if(session('error'))

                <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-5 py-4">

                    <p class="text-sm font-bold text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            @endif


            <form
              action="{{ route('student.cart.baju.store', $item->id) }}"
                method="POST"
                class="space-y-6"
            >

                @csrf


                <!-- ITEM -->

                <div class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">

                    <div class="flex flex-col gap-5 sm:flex-row">

                        <div class="h-28 w-28 shrink-0 overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">

                            @if($item->item_photo)

                                <img
                                    src="{{ asset('storage/' . $item->item_photo) }}"
                                    alt="{{ $item->name }}"
                                    class="h-full w-full object-cover"
                                >

                            @endif

                        </div>


                        <div>

                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">
                                Baju
                            </p>

                            <h2 class="mt-1 text-xl font-black text-gray-950">
                                {{ $item->name }}
                            </h2>

                            <p class="mt-2 text-sm text-gray-500">
                                Harga dasar:
                                <span class="font-black text-indigo-600">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </span>
                            </p>

                        </div>

                    </div>

                </div>


                <!-- SCHEDULE -->

                <div class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">

                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                        Jadwal
                    </p>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label class="mb-2 block text-[9px] font-black uppercase tracking-widest text-gray-500">
                                Tanggal Transaksi
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                value="{{ old('start_date', $prefillStartDate) }}"
                                min="{{ now()->format('Y-m-d') }}"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold"
                            >

                        </div>


                        <div>

                            <label class="mb-2 block text-[9px] font-black uppercase tracking-widest text-gray-500">
                                Jam Transaksi
                            </label>

                            <select
                                name="start_time"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold"
                            >

                                <option value="">
                                    Pilih Jam
                                </option>

                                @foreach([
                                    '17:00',
                                    '17:30',
                                    '18:00',
                                    '18:30',
                                    '19:00',
                                ] as $time)

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

                </div>


                <!-- SIZE BREAKDOWN -->

                <div class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">
                            Size Breakdown
                        </p>

                        <h2 class="mt-1 text-xl font-black text-gray-950">
                            Rincian Ukuran Baju
                        </h2>

                        <p class="mt-2 text-xs leading-relaxed text-gray-500">
                            Isi divisi dan jumlah hanya untuk ukuran yang digunakan.
                            Harga dan subtotal dihitung otomatis.
                        </p>

                    </div>


                    <div class="mt-6 overflow-x-auto">

                        <table class="w-full min-w-[760px] border-collapse">

                            <thead>

                                <tr class="border-b border-gray-200">

                                    <th class="px-4 py-3 text-left text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Size
                                    </th>

                                    <th class="px-4 py-3 text-left text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Divisi
                                    </th>

                                    <th class="px-4 py-3 text-center text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Jumlah
                                    </th>

                                    <th class="px-4 py-3 text-right text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Harga Satuan
                                    </th>

                                    <th class="px-4 py-3 text-right text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Subtotal
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($sizeOptions as $size)

                                    @php
                                        $additional =
                                            $sizePrices[$size] -
                                            (int) $item->price;
                                    @endphp

                                    <tr
                                        class="border-b border-gray-100"
                                        data-size-row="{{ $size }}"
                                        data-unit-price="{{ $sizePrices[$size] }}"
                                        data-additional="{{ $additional }}"
                                    >

                                        <td class="px-4 py-4">

                                            <span class="inline-flex min-w-[52px] justify-center rounded-xl bg-gray-950 px-3 py-2 text-xs font-black text-white">
                                                {{ $size }}
                                            </span>

                                            @if($additional > 0)

                                                <p class="mt-1 text-[8px] font-bold text-orange-500">
                                                    +Rp {{ number_format($additional, 0, ',', '.') }}
                                                </p>

                                            @endif

                                        </td>


                                        <td class="px-4 py-4">

                                            <input
                                                type="text"
                                                name="sizes[{{ $size }}][division]"
                                                value="{{ old("sizes.{$size}.division") }}"
                                                placeholder="Contoh: Event"
                                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-bold outline-none focus:border-indigo-400 focus:bg-white"
                                            >

                                        </td>


                                        <td class="px-4 py-4">

                                            <input
                                                type="number"
                                                name="sizes[{{ $size }}][quantity]"
                                                value="{{ old("sizes.{$size}.quantity", 0) }}"
                                                min="0"
                                                class="size-quantity mx-auto block w-24 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-xs font-black outline-none focus:border-indigo-400 focus:bg-white"
                                            >

                                        </td>


                                        <td class="size-unit-price px-4 py-4 text-right text-xs font-black text-gray-800">
                                            Rp {{ number_format($sizePrices[$size], 0, ',', '.') }}
                                        </td>


                                        <td class="size-subtotal px-4 py-4 text-right text-xs font-black text-gray-950">
                                            Rp 0
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">

                        <div class="rounded-2xl bg-gray-50 p-4">

                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                Total Quantity
                            </p>

                            <p
                                id="totalQuantity"
                                class="mt-1 text-2xl font-black text-gray-950"
                            >
                                0 pcs
                            </p>

                        </div>


                        <div class="rounded-2xl bg-gray-950 p-4 text-white">

                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                Grand Total
                            </p>

                            <p
                                id="grandTotal"
                                class="mt-1 text-2xl font-black"
                            >
                                Rp 0
                            </p>

                        </div>

                    </div>

                </div>


                <!-- COLOR -->

                <div class="rounded-[2rem] border border-purple-100 bg-purple-50 p-6">

                    <p class="text-[9px] font-black uppercase tracking-widest text-purple-700">
                        Warna Baju
                    </p>

                    <p class="mt-1 text-sm font-black text-gray-950">
                        Pilih warna berdasarkan Color Chart
                    </p>


                    <div class="mt-4">

                        @if(!empty($colorCharts))

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">

                                @foreach($colorCharts as $chart)

                                    <a
                                        href="{{ asset('storage/' . $chart) }}"
                                        target="_blank"
                                        class="overflow-hidden rounded-xl border border-purple-100 bg-white"
                                    >

                                        <img
                                            src="{{ asset('storage/' . $chart) }}"
                                            alt="Color Chart"
                                            class="h-32 w-full object-cover"
                                        >

                                    </a>

                                @endforeach

                            </div>

                        @else

                            <div class="rounded-xl border border-purple-100 bg-white px-4 py-3">

                                <p class="text-xs font-semibold text-gray-500">
                                    Color Chart belum tersedia. Silakan hubungi admin.
                                </p>

                            </div>

                        @endif

                    </div>


                    <div class="mt-5">

                        <label class="mb-2 block text-[9px] font-black uppercase tracking-widest text-purple-700">
                            Warna / Nomor Warna
                        </label>

                        <input
                            type="text"
                            name="color_number"
                            value="{{ old('color_number') }}"
                            placeholder="Contoh: 47 / Navy / 47 Navy"
                            required
                            class="w-full rounded-xl border border-purple-200 bg-white px-4 py-3 text-sm font-bold outline-none focus:border-purple-400 focus:ring-4 focus:ring-purple-500/10"
                        >

                    </div>

                </div>


                <!-- DESIGN -->

                <div class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-sm">

                    <label class="mb-2 block text-[9px] font-black uppercase tracking-widest text-gray-500">
                        Link Design
                    </label>

                    <input
                        type="url"
                        name="design_link"
                        value="{{ old('design_link') }}"
                        placeholder="https://drive.google.com/..."
                        required
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold outline-none focus:border-indigo-400 focus:bg-white"
                    >

                </div>


                <!-- SAVE -->

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-gray-950 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition hover:bg-indigo-600"
                >
                    Simpan Detail Baju ke Keranjang
                </button>

            </form>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const rows =
                document.querySelectorAll(
                    '[data-size-row]'
                );

            const totalQuantity =
                document.getElementById(
                    'totalQuantity'
                );

            const grandTotal =
                document.getElementById(
                    'grandTotal'
                );


            function formatRupiah(number) {

                return new Intl.NumberFormat(
                    'id-ID'
                ).format(number);

            }


            function calculate() {

                let quantity = 0;
                let total = 0;

                rows.forEach(function (row) {

                    const input =
                        row.querySelector(
                            '.size-quantity'
                        );

                    const subtotalElement =
                        row.querySelector(
                            '.size-subtotal'
                        );

                    const unitPrice =
                        Number(
                            row.dataset.unitPrice
                        );

                    const rowQuantity =
                        Math.max(
                            0,
                            Number(
                                input.value || 0
                            )
                        );

                    const subtotal =
                        unitPrice *
                        rowQuantity;

                    quantity += rowQuantity;
                    total += subtotal;

                    subtotalElement.textContent =
                        'Rp ' +
                        formatRupiah(
                            subtotal
                        );
                });


                totalQuantity.textContent =
                    quantity +
                    ' pcs';

                grandTotal.textContent =
                    'Rp ' +
                    formatRupiah(
                        total
                    );
            }


            document
                .querySelectorAll(
                    '.size-quantity'
                )
                .forEach(function (input) {

                    input.addEventListener(
                        'input',
                        calculate
                    );

                });


            calculate();

        });
    </script>

</x-app-layout>