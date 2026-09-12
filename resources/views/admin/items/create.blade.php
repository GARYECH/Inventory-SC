<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between gap-4">

            <div>

                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    Create New Asset
                </h2>

                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                    Student Council Inventory Management
                </p>

            </div>


            <a
                href="{{ route('admin.items.index') }}"
                class="group inline-flex items-center text-xs font-black text-gray-400 hover:text-indigo-600 transition uppercase tracking-widest"
            >

                <svg
                    class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition"
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

                Back to Inventory

            </a>

        </div>

    </x-slot>



    <!-- ============================================================= -->
    <!-- CONTENT -->
    <!-- ============================================================= -->

    <div class="py-12">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">


                <!-- ===================================================== -->
                <!-- LEFT -->
                <!-- ===================================================== -->

                <div class="lg:col-span-4 space-y-6">


                    <!-- PROTOCOL -->

                    <div class="relative overflow-hidden rounded-[2.5rem] bg-indigo-600 p-8 text-white shadow-2xl shadow-indigo-200">


                        <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-500 opacity-50"></div>


                        <h3 class="relative z-10 text-xl font-black tracking-tight">
                            Inventory Protocol
                        </h3>


                        <p class="relative z-10 mt-3 text-xs font-medium leading-relaxed text-indigo-100">

                            Category menentukan jenis barang.
                            Transaction Type menentukan bagaimana barang tersebut diproses oleh sistem.

                        </p>


                        <div class="relative z-10 mt-7 space-y-4">


                            <!-- STEP 1 -->

                            <div class="flex items-start gap-3">

                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-400 text-[10px] font-black">
                                    1
                                </span>

                                <div>

                                    <p class="text-[10px] font-black uppercase tracking-widest">
                                        Choose Category
                                    </p>

                                    <p class="mt-1 text-[9px] leading-relaxed text-indigo-100">
                                        Contoh: ATK, Obat, Handy Talkie, dan lainnya.
                                    </p>

                                </div>

                            </div>



                            <!-- STEP 2 -->

                            <div class="flex items-start gap-3">

                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-400 text-[10px] font-black">
                                    2
                                </span>

                                <div>

                                    <p class="text-[10px] font-black uppercase tracking-widest">
                                        Choose Transaction Type
                                    </p>

                                    <p class="mt-1 text-[9px] leading-relaxed text-indigo-100">
                                        Hanya ada 4 jenis transaksi.
                                    </p>

                                </div>

                            </div>



                            <!-- STEP 3 -->

                            <div class="flex items-start gap-3">

                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-400 text-[10px] font-black">
                                    3
                                </span>

                                <div>

                                    <p class="text-[10px] font-black uppercase tracking-widest">
                                        Set Detail
                                    </p>

                                    <p class="mt-1 text-[9px] leading-relaxed text-indigo-100">
                                        Detail hanya digunakan jika diperlukan.
                                    </p>

                                </div>

                            </div>



                            <!-- STEP 4 -->

                            <div class="flex items-start gap-3">

                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-400 text-[10px] font-black text-amber-950">
                                    4
                                </span>

                                <div>

                                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-200">
                                        MoU Requirement
                                    </p>

                                    <p class="mt-1 text-[9px] leading-relaxed text-indigo-100">
                                        Tentukan apakah barang membutuhkan MoU.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- TRANSACTION RULES -->

                    <div class="rounded-[2.5rem] border border-gray-100 bg-white p-8 shadow-sm">

                        <h3 class="text-lg font-black tracking-tight text-gray-900">
                            Transaction Rules
                        </h3>


                        <div class="mt-6 space-y-5">


                            <!-- PERALATAN -->

                            <div>

                                <div class="flex items-center gap-2">

                                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-500"></span>

                                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600">
                                        Peralatan
                                    </p>

                                </div>

                                <p class="mt-1 text-[10px] leading-relaxed text-gray-500">

                                    Barang yang harus dikembalikan setelah digunakan.

                                </p>

                            </div>



                            <!-- HT -->

                            <div>

                                <div class="flex items-center gap-2">

                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>

                                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">
                                        Handy Talkie
                                    </p>

                                </div>

                                <p class="mt-1 text-[10px] leading-relaxed text-gray-500">

                                    HT yang disewa dan wajib dikembalikan.

                                </p>

                            </div>



                            <!-- HABIS PAKAI -->

                            <div>

                                <div class="flex items-center gap-2">

                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>

                                    <p class="text-[10px] font-black uppercase tracking-widest text-rose-600">
                                        Habis Pakai
                                    </p>

                                </div>

                                <p class="mt-1 text-[10px] leading-relaxed text-gray-500">

                                    Barang yang tidak dikembalikan dan stok berkurang permanen.

                                </p>

                            </div>



                            <!-- MERCH -->

                            <div>

                                <div class="flex items-center gap-2">

                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>

                                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">
                                        Merchandise
                                    </p>

                                </div>

                                <p class="mt-1 text-[10px] leading-relaxed text-gray-500">

                                    Barang beli putus seperti Baju atau ID Card.

                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- IMAGE PREVIEW -->

                    <div class="relative h-72 overflow-hidden rounded-[2.5rem] border-2 border-dashed border-gray-200 bg-white shadow-inner">


                        <img
                            id="preview"
                            src=""
                            alt="Preview"
                            class="hidden h-full w-full rounded-[2rem] object-cover"
                        >


                        <div
                            id="placeholder"
                            class="flex h-full items-center justify-center"
                        >

                            <div class="text-center">

                                <svg
                                    class="mx-auto mb-3 h-12 w-12 text-gray-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2v12a2 2 0 00-2-2z"
                                    />

                                </svg>


                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">
                                    Live Photo Preview
                                </p>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ===================================================== -->
                <!-- RIGHT -->
                <!-- ===================================================== -->

                <div class="lg:col-span-8">


                    <div class="rounded-[2.5rem] border border-gray-100 bg-white p-6 shadow-sm md:p-10">


                        <form
                            action="{{ route('admin.items.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-7"
                        >

                            @csrf



                            <!-- ================================================= -->
                            <!-- NAME + CATEGORY -->
                            <!-- ================================================= -->

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


                                <!-- NAME -->

                                <div>

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Asset Name
                                    </label>


                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="e.g. HT Baofeng UV-82"
                                        required
                                        class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-sm outline-none transition focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >


                                    @error('name')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <!-- CATEGORY -->

                                <div>

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Kategori Barang
                                    </label>


                                    <select
                                        name="category_id"
                                        required
                                        class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-sm outline-none transition focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >

                                        <option value="">
                                            -- Pilih Kategori --
                                        </option>


                                        @foreach($categories as $category)

                                            <option
                                                value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}
                                            >
                                                {{ $category->name }}
                                            </option>

                                        @endforeach

                                    </select>


                                    @error('category_id')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- TRANSACTION SETUP -->
                            <!-- ================================================= -->

                            <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50/50 p-5">


                                <div class="mb-5">

                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">
                                        Transaction Setup
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-indigo-400">

                                        Category dan Transaction Type dipisahkan.
                                        Satu category dapat mempunyai lebih dari satu Transaction Type.

                                    </p>

                                </div>


                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


                                    <!-- TRANSACTION TYPE -->

                                    <div>

                                        <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500">
                                            Transaction Type
                                        </label>


                                        <select
                                            name="transaction_type"
                                            id="transaction_type"
                                            required
                                            class="w-full rounded-2xl border-0 bg-white px-5 py-4 text-sm font-bold text-gray-900 shadow-sm outline-none focus:ring-2 focus:ring-indigo-500"
                                        >

                                            <option value="">
                                                -- Pilih Transaction Type --
                                            </option>


                                            <option
                                                value="Peralatan"
                                                {{ old('transaction_type') === 'Peralatan' ? 'selected' : '' }}
                                            >
                                                Peralatan
                                            </option>


                                            <option
                                                value="Handy Talkie"
                                                {{ old('transaction_type') === 'Handy Talkie' ? 'selected' : '' }}
                                            >
                                                Handy Talkie
                                            </option>


                                            <option
                                                value="Habis Pakai"
                                                {{ old('transaction_type') === 'Habis Pakai' ? 'selected' : '' }}
                                            >
                                                Habis Pakai
                                            </option>


                                            <option
                                                value="Merchandise"
                                                {{ old('transaction_type') === 'Merchandise' ? 'selected' : '' }}
                                            >
                                                Merchandise
                                            </option>

                                        </select>


                                        @error('transaction_type')

                                            <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>



                                    <!-- MOU -->

                                    <div>

                                        <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-amber-500">
                                            Wajib Pakai MoU?
                                        </label>


                                        <select
                                            name="requires_mou"
                                            id="requires_mou"
                                            required
                                            class="w-full rounded-2xl border-0 bg-white px-5 py-4 text-sm font-bold text-gray-900 shadow-sm outline-none focus:ring-2 focus:ring-amber-500"
                                        >

                                            <option value="1">
                                                YA - Butuh MoU
                                            </option>

                                            <option value="0">
                                                TIDAK - Tanpa MoU
                                            </option>

                                        </select>


                                        @error('requires_mou')

                                            <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>

                                </div>



                                <!-- ================================================= -->
                                <!-- TRANSACTION DETAIL -->
                                <!-- ================================================= -->

                                <div
                                    id="transactionDetailSection"
                                    class="mt-5 hidden"
                                >

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500">
                                        Transaction Detail
                                    </label>


                                    <select
                                        name="transaction_detail"
                                        id="transaction_detail"
                                        class="w-full rounded-2xl border-0 bg-white px-5 py-4 text-sm font-bold text-gray-900 shadow-sm outline-none focus:ring-2 focus:ring-indigo-500"
                                    >

                                        <option value="">
                                            -- Pilih Detail --
                                        </option>

                                    </select>


                                    <p class="mt-2 ml-1 text-[9px] font-semibold text-gray-400">
                                        Detail digunakan untuk membedakan jenis/model di dalam Transaction Type.
                                    </p>


                                    @error('transaction_detail')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <!-- ================================================= -->
                                <!-- MERCHANDISE SUBCATEGORY -->
                                <!-- ================================================= -->

                                <div
                                    id="merchandiseSection"
                                    class="mt-5 hidden"
                                >

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500">
                                        Merchandise Subcategory
                                    </label>


                                    <select
                                        name="subcategory"
                                        id="subcategory"
                                        class="w-full rounded-2xl border-0 bg-white px-5 py-4 text-sm font-bold text-gray-900 shadow-sm outline-none focus:ring-2 focus:ring-emerald-500"
                                    >

                                        <option value="">
                                            -- Pilih Subcategory --
                                        </option>


                                        <option
                                            value="Baju"
                                            {{ old('subcategory') === 'Baju' ? 'selected' : '' }}
                                        >
                                            Baju
                                        </option>


                                        <option
                                            value="ID Card"
                                            {{ old('subcategory') === 'ID Card' ? 'selected' : '' }}
                                        >
                                            ID Card
                                        </option>


                                        <option
                                            value="Lainnya"
                                            {{ old('subcategory') === 'Lainnya' ? 'selected' : '' }}
                                        >
                                            Lainnya
                                        </option>

                                    </select>


                                    <p class="mt-2 ml-1 text-[9px] font-semibold text-gray-400">
                                        Contoh: Baju membutuhkan ukuran dan link desain saat mahasiswa checkout.
                                    </p>


                                    @error('subcategory')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <!-- IMPORTANT NOTE -->

                                <div class="mt-5 rounded-2xl border border-indigo-100 bg-white px-4 py-3">

                                    <div class="flex items-start gap-3">

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


                                        <div>

                                            <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">
                                                Contoh Penggunaan
                                            </p>


                                            <p class="mt-1 text-[9px] leading-relaxed text-gray-500">

                                                <span class="font-black text-gray-700">
                                                    ATK + Peralatan
                                                </span>
                                                =
                                                Stapler

                                                <span class="mx-1">
                                                    •
                                                </span>

                                                <span class="font-black text-gray-700">
                                                    ATK + Habis Pakai
                                                </span>
                                                =
                                                Kertas A4

                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- STOCK + PRICE -->
                            <!-- ================================================= -->

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


                                <div>

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Stock Quantity
                                    </label>


                                    <input
                                        type="number"
                                        name="stock_quantity"
                                        min="0"
                                        value="{{ old('stock_quantity', 0) }}"
                                        required
                                        class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >


                                    @error('stock_quantity')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <div>

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Price / Rental Rate
                                    </label>


                                    <input
                                        type="number"
                                        name="price"
                                        min="0"
                                        value="{{ old('price', 0) }}"
                                        required
                                        class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >


                                    @error('price')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- DESCRIPTION -->
                            <!-- ================================================= -->

                            <div>

                                <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                    Description
                                </label>


                                <textarea
                                    name="description"
                                    rows="5"
                                    required
                                    placeholder="Describe the item..."
                                    class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-semibold text-gray-800 shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                >{{ old('description') }}</textarea>


                                @error('description')

                                    <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            <!-- ================================================= -->
                            <!-- PHOTO -->
                            <!-- ================================================= -->

                            <div>

                                <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                    Item Photo
                                </label>


                                <input
                                    type="file"
                                    name="item_photo"
                                    id="item_photo"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    required
                                    class="block w-full rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs font-bold text-gray-500"
                                >


                                <p class="mt-2 ml-1 text-[9px] font-semibold text-gray-400">
                                    JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                                </p>


                                @error('item_photo')

                                    <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            <!-- ================================================= -->
                            <!-- SUBMIT -->
                            <!-- ================================================= -->

                            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">


                                <a
                                    href="{{ route('admin.items.index') }}"
                                    class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-7 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-gray-50 hover:text-gray-900"
                                >
                                    Cancel
                                </a>


                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-gray-950 px-7 py-3.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg hover:bg-indigo-600"
                                >

                                    Save Asset

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- ============================================================= -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================= -->

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const transactionType =
                    document.getElementById(
                        'transaction_type'
                    );

                const transactionDetailSection =
                    document.getElementById(
                        'transactionDetailSection'
                    );

                const transactionDetail =
                    document.getElementById(
                        'transaction_detail'
                    );

                const merchandiseSection =
                    document.getElementById(
                        'merchandiseSection'
                    );

                const subcategory =
                    document.getElementById(
                        'subcategory'
                    );

                const requiresMou =
                    document.getElementById(
                        'requires_mou'
                    );

                const photoInput =
                    document.getElementById(
                        'item_photo'
                    );

                const preview =
                    document.getElementById(
                        'preview'
                    );

                const placeholder =
                    document.getElementById(
                        'placeholder'
                    );

                const oldTransactionDetail =
                    @json(
                        old(
                            'transaction_detail'
                        )
                    );

                const oldSubcategory =
                    @json(
                        old(
                            'subcategory'
                        )
                    );

                const oldRequiresMou =
                    @json(
                        old(
                            'requires_mou',
                            null
                        )
                    );

                function addOption(
                    value
                ) {
                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        value;

                    option.textContent =
                        value;

                    transactionDetail.appendChild(
                        option
                    );
                }

                function updateTransactionFields(
                    preserveValues = true
                ) {
                    const type =
                        transactionType.value;

                    const currentDetail =
                        preserveValues
                            ? (
                                transactionDetail.value
                                ||
                                oldTransactionDetail
                            )
                            : '';

                    const currentSubcategory =
                        preserveValues
                            ? (
                                subcategory.value
                                ||
                                oldSubcategory
                            )
                            : '';

                    transactionDetail.innerHTML =
                        '<option value="">-- Pilih Detail --</option>';

                    transactionDetailSection
                        .classList
                        .add('hidden');

                    merchandiseSection
                        .classList
                        .add('hidden');

                    if (
                        type ===
                        'Peralatan'
                    ) {
                        transactionDetailSection
                            .classList
                            .remove('hidden');

                        addOption(
                            'Internal Rental'
                        );

                        addOption(
                            'Vendor Rental'
                        );

                        transactionDetail.value =
                            currentDetail;

                        return;
                    }

                    if (
                        type ===
                        'Handy Talkie'
                    ) {
                        transactionDetailSection
                            .classList
                            .remove('hidden');

                        addOption(
                            'HT UV-82'
                        );

                        addOption(
                            'HT 888s'
                        );

                        addOption(
                            'HT UV-5R'
                        );

                        transactionDetail.value =
                            currentDetail;

                        return;
                    }

                    if (
                        type ===
                        'Merchandise'
                    ) {
                        merchandiseSection
                            .classList
                            .remove('hidden');

                        subcategory.value =
                            currentSubcategory;

                        return;
                    }

                    if (
                        type ===
                        'Habis Pakai'
                    ) {
                        requiresMou.value =
                            '0';
                    }
                }

                transactionType.addEventListener(
                    'change',
                    function () {
                        updateTransactionFields(
                            false
                        );
                    }
                );

                if (
                    requiresMou &&
                    oldRequiresMou !== null
                ) {
                    requiresMou.value =
                        String(
                            oldRequiresMou
                        ) === '1'
                            ? '1'
                            : '0';
                }

                if (
                    photoInput &&
                    preview &&
                    placeholder
                ) {
                    photoInput.addEventListener(
                        'change',
                        function () {

                            const file =
                                this.files[0];

                            if (!file) {
                                preview
                                    .classList
                                    .add('hidden');

                                placeholder
                                    .classList
                                    .remove('hidden');

                                preview.src =
                                    '';

                                return;
                            }

                            const reader =
                                new FileReader();

                            reader.onload =
                                function (
                                    event
                                ) {

                                    preview.src =
                                        event.target.result;

                                    preview
                                        .classList
                                        .remove(
                                            'hidden'
                                        );

                                    placeholder
                                        .classList
                                        .add(
                                            'hidden'
                                        );
                                };

                            reader.readAsDataURL(
                                file
                            );
                        }
                    );
                }

                updateTransactionFields(
                    true
                );
            }
        );
    </script>

</x-app-layout>