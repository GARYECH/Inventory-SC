<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between gap-4">

            <div>

                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    Modify Asset
                </h2>

                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                    Update Student Council Inventory
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



    <div class="py-12">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">


                <!-- ===================================================== -->
                <!-- LEFT -->
                <!-- ===================================================== -->

                <div class="lg:col-span-4 space-y-6">


                    <!-- RULES -->

                    <div class="relative overflow-hidden rounded-[2.5rem] bg-indigo-600 p-8 text-white shadow-2xl shadow-indigo-200">

                        <h3 class="text-xl font-black tracking-tight">
                            Inventory Rules
                        </h3>


                        <div class="mt-6 space-y-5">


                            <div>

                                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-200">
                                    1. Peralatan
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-indigo-50">
                                    Barang yang wajib dikembalikan setelah digunakan.
                                </p>

                            </div>



                            <div>

                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-200">
                                    2. Handy Talkie
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-indigo-50">
                                    HT yang wajib dikembalikan dan menggunakan detail tipe HT.
                                </p>

                            </div>



                            <div>

                                <p class="text-[10px] font-black uppercase tracking-widest text-rose-200">
                                    3. Habis Pakai
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-indigo-50">
                                    Barang yang stoknya berkurang permanen dan tidak dikembalikan.
                                </p>

                            </div>



                            <div>

                                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-200">
                                    4. Merchandise
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-indigo-50">
                                    Barang beli putus seperti Baju, ID Card, atau lainnya.
                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- IMAGE PREVIEW -->

                    <div class="relative h-72 overflow-hidden rounded-[2.5rem] border-2 border-dashed border-gray-200 bg-white shadow-inner">

                        @if($item->item_photo)

                            <img
                                id="preview"
                                src="{{ asset('storage/' . $item->item_photo) }}"
                                alt="{{ $item->name }}"
                                class="h-full w-full rounded-[2rem] object-cover"
                            >


                            <div
                                id="placeholder"
                                class="hidden h-full items-center justify-center"
                            >

                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">
                                    No Current Image
                                </p>

                            </div>

                        @else

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

                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-300">
                                    No Current Image
                                </p>

                            </div>

                        @endif

                    </div>

                </div>



                <!-- ===================================================== -->
                <!-- RIGHT -->
                <!-- ===================================================== -->

                <div class="lg:col-span-8">

                    <div class="rounded-[2.5rem] border border-gray-100 bg-white p-6 shadow-sm md:p-10">


                        <form
                            action="{{ route('admin.items.update', $item->id) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-7"
                        >

                            @csrf

                            @method('PUT')



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
                                        value="{{ old('name', $item->name) }}"
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
                                                {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}
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

                            @php

                                $currentType =
                                    old(
                                        'transaction_type',
                                        $item->transaction_type
                                    );

                                $currentDetail =
                                    old(
                                        'transaction_detail',
                                        $item->transaction_detail
                                    );

                                /*
                                |--------------------------------------------------------------------------
                                | Legacy fallback
                                |--------------------------------------------------------------------------
                                | Ini hanya membantu kalau migration normalization
                                | belum mengubah data lama.
                                */

                                if (
                                    $currentType === 'HT UV-82' ||
                                    $currentType === 'HT 888s' ||
                                    $currentType === 'HT UV-5R'
                                ) {

                                    $currentDetail =
                                        $currentType;

                                    $currentType =
                                        'Handy Talkie';

                                }


                                if (
                                    $currentType === 'Internal Rental' ||
                                    $currentType === 'Vendor Rental'
                                ) {

                                    $currentDetail =
                                        $currentType;

                                    $currentType =
                                        'Peralatan';

                                }


                                if (
                                    $currentType === 'ATK' ||
                                    $currentType === 'Obat'
                                ) {

                                    $currentType =
                                        'Habis Pakai';

                                }


                                if (
                                    $currentType === 'Sale'
                                ) {

                                    $currentType =
                                        'Merchandise';

                                }

                            @endphp


                            <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50/50 p-5">


                                <div class="mb-5">

                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">
                                        Transaction Setup
                                    </p>

                                    <p class="mt-1 text-xs font-semibold text-indigo-400">

                                        Category dan Transaction Type berbeda.
                                        Barang dalam Category yang sama dapat memiliki Transaction Type berbeda.

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
                                                {{ $currentType === 'Peralatan' ? 'selected' : '' }}
                                            >
                                                Peralatan
                                            </option>


                                            <option
                                                value="Handy Talkie"
                                                {{ $currentType === 'Handy Talkie' ? 'selected' : '' }}
                                            >
                                                Handy Talkie
                                            </option>


                                            <option
                                                value="Habis Pakai"
                                                {{ $currentType === 'Habis Pakai' ? 'selected' : '' }}
                                            >
                                                Habis Pakai
                                            </option>


                                            <option
                                                value="Merchandise"
                                                {{ $currentType === 'Merchandise' ? 'selected' : '' }}
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

                                            <option
                                                value="1"
                                                {{ old('requires_mou', $item->requires_mou) == '1' ? 'selected' : '' }}
                                            >
                                                YA - Butuh MoU
                                            </option>


                                            <option
                                                value="0"
                                                {{ old('requires_mou', $item->requires_mou) == '0' ? 'selected' : '' }}
                                            >
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
                                        Detail digunakan untuk membedakan model atau jalur penyewaan.
                                    </p>


                                    @error('transaction_detail')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <!-- ================================================= -->
                                <!-- MERCHANDISE -->
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
                                            {{ old('subcategory', $item->subcategory) === 'Baju' ? 'selected' : '' }}
                                        >
                                            Baju
                                        </option>


                                        <option
                                            value="ID Card"
                                            {{ old('subcategory', $item->subcategory) === 'ID Card' ? 'selected' : '' }}
                                        >
                                            ID Card
                                        </option>


                                        <option
                                            value="Lainnya"
                                            {{ old('subcategory', $item->subcategory) === 'Lainnya' ? 'selected' : '' }}
                                        >
                                            Lainnya
                                        </option>

                                    </select>


                                    <p class="mt-2 ml-1 text-[9px] font-semibold text-gray-400">
                                        Merchandise Baju dan ID Card akan menggunakan template MoU masing-masing.
                                    </p>


                                    @error('subcategory')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <!-- NOTE -->

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
                                                Contoh
                                            </p>


                                            <p class="mt-1 text-[9px] leading-relaxed text-gray-500">

                                                ATK
                                                +
                                                <span class="font-black text-gray-700">
                                                    Peralatan
                                                </span>
                                                =
                                                Stapler

                                                <span class="mx-1">
                                                    •
                                                </span>

                                                ATK
                                                +
                                                <span class="font-black text-gray-700">
                                                    Habis Pakai
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


                                <!-- STOCK -->

                                <div>

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Stock Quantity
                                    </label>


                                    <input
                                        type="number"
                                        name="stock_quantity"
                                        min="0"
                                        value="{{ old('stock_quantity', $item->stock_quantity) }}"
                                        required
                                        class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                    >


                                    @error('stock_quantity')

                                        <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <!-- PRICE -->

                                <div>

                                    <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                        Price / Rental Rate
                                    </label>


                                    <input
                                        type="number"
                                        name="price"
                                        min="0"
                                        value="{{ old('price', $item->price) }}"
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
                                    Asset Description
                                </label>


                                <textarea
                                    name="description"
                                    rows="4"
                                    required
                                    placeholder="Describe this inventory item..."
                                    class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-semibold text-gray-800 shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                >{{ old('description', $item->description) }}</textarea>


                                @error('description')

                                    <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            <!-- ================================================= -->
                            <!-- CONDITION -->
                            <!-- ================================================= -->

                            <div>

                                <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                    Condition Status
                                </label>


                                <select
                                    name="condition_status"
                                    required
                                    class="w-full rounded-2xl border-0 bg-gray-50 px-5 py-4 text-sm font-bold text-gray-800 shadow-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500"
                                >

                                    <option
                                        value="Good"
                                        {{ old('condition_status', $item->condition_status) === 'Good' ? 'selected' : '' }}
                                    >
                                        Good
                                    </option>


                                    <option
                                        value="Minor Damage"
                                        {{ old('condition_status', $item->condition_status) === 'Minor Damage' ? 'selected' : '' }}
                                    >
                                        Minor Damage
                                    </option>


                                    <option
                                        value="Damaged"
                                        {{ old('condition_status', $item->condition_status) === 'Damaged' ? 'selected' : '' }}
                                    >
                                        Damaged
                                    </option>

                                </select>


                                @error('condition_status')

                                    <p class="mt-2 ml-1 text-[9px] font-black uppercase text-red-500">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>



                            <!-- ================================================= -->
                            <!-- NEW PHOTO -->
                            <!-- ================================================= -->

                            <div>

                                <label class="mb-3 ml-1 block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                    Replace Item Photo
                                </label>


                                <input
                                    type="file"
                                    name="item_photo"
                                    id="item_photo"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="block w-full rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs font-bold text-gray-500"
                                >


                                <p class="mt-2 ml-1 text-[9px] font-semibold text-gray-400">
                                    Kosongkan apabila tidak ingin mengganti foto.
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
                                    Update Asset
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


                const existingDetail =
                    @json(
                        $currentDetail
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


                    if (
                        existingDetail ===
                        value
                    ) {

                        option.selected =
                            true;

                    }


                    transactionDetail.appendChild(
                        option
                    );

                }


                function updateTransactionFields()
                {

                    const type =
                        transactionType.value;


                    transactionDetail.innerHTML =
                        '<option value="">-- Pilih Detail --</option>';


                    transactionDetailSection.classList.add(
                        'hidden'
                    );


                    merchandiseSection.classList.add(
                        'hidden'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | PERALATAN
                    |--------------------------------------------------------------------------
                    */

                    if (
                        type ===
                        'Peralatan'
                    ) {

                        transactionDetailSection.classList.remove(
                            'hidden'
                        );


                        addOption(
                            'Internal Rental'
                        );


                        addOption(
                            'Vendor Rental'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HANDY TALKIE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        type ===
                        'Handy Talkie'
                    ) {

                        transactionDetailSection.classList.remove(
                            'hidden'
                        );


                        addOption(
                            'HT UV-82'
                        );


                        addOption(
                            'HT 888s'
                        );


                        addOption(
                            'HT UV-5R'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HABIS PAKAI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        type ===
                        'Habis Pakai'
                    ) {

                        requiresMou.value =
                            '0';


                        transactionDetail.value =
                            '';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MERCHANDISE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        type ===
                        'Merchandise'
                    ) {

                        merchandiseSection.classList.remove(
                            'hidden'
                        );


                        transactionDetail.value =
                            '';

                    }

                }


                transactionType.addEventListener(
                    'change',
                    function () {

                        updateTransactionFields();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | PHOTO PREVIEW
                |--------------------------------------------------------------------------
                */

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


                                    preview.classList.remove(
                                        'hidden'
                                    );


                                    placeholder.classList.add(
                                        'hidden'
                                    );

                                };


                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | INITIAL STATE
                |--------------------------------------------------------------------------
                */

                updateTransactionFields();


                /*
                |--------------------------------------------------------------------------
                | OLD INPUT OVERRIDE
                |--------------------------------------------------------------------------
                */

                const oldDetail =
                    @json(
                        old(
                            'transaction_detail'
                        )
                    );


                if (
                    oldDetail
                ) {

                    transactionDetail.value =
                        oldDetail;

                }

            }
        );

    </script>

</x-app-layout>