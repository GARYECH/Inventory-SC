<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-2xl font-black tracking-tight text-gray-950">
                    System Categories
                </h2>

                <p class="mt-1 text-[10px] font-black uppercase tracking-[0.18em] text-gray-500">
                    Master Data Management
                </p>

            </div>

        </div>

    </x-slot>



    <div class="py-10 sm:py-12">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


            <!-- ========================================================= -->
            <!-- SUCCESS -->
            <!-- ========================================================= -->

            @if(session('success'))

                <div class="mb-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white">

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
                                    d="M5 13l4 4L19 7"
                                />

                            </svg>

                        </div>


                        <div>

                            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                Berhasil
                            </p>

                            <p class="mt-1 text-sm font-bold text-emerald-900">
                                {{ session('success') }}
                            </p>

                        </div>

                    </div>

                </div>

            @endif



            <!-- ========================================================= -->
            <!-- ERROR -->
            <!-- ========================================================= -->

            @if(session('error'))

                <div class="mb-6 rounded-2xl border border-red-300 bg-red-50 px-5 py-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-600 text-white">

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
                                    d="M6 18L18 6M6 6l12 12"
                                />

                            </svg>

                        </div>


                        <div>

                            <p class="text-[9px] font-black uppercase tracking-widest text-red-700">
                                Terjadi Kesalahan
                            </p>

                            <p class="mt-1 text-sm font-bold text-red-900">
                                {{ session('error') }}
                            </p>

                        </div>

                    </div>

                </div>

            @endif



            <!-- ========================================================= -->
            <!-- VALIDATION -->
            <!-- ========================================================= -->

            @if($errors->any())

                <div class="mb-6 rounded-2xl border border-red-300 bg-red-50 px-5 py-4">

                    <p class="text-[9px] font-black uppercase tracking-widest text-red-700">
                        Periksa Form
                    </p>


                    <div class="mt-2 space-y-1">

                        @foreach($errors->all() as $error)

                            <p class="text-xs font-bold text-red-900">
                                • {{ $error }}
                            </p>

                        @endforeach

                    </div>

                </div>

            @endif



            <!-- ========================================================= -->
            <!-- MAIN GRID -->
            <!-- ========================================================= -->

            <div class="grid grid-cols-1 gap-7 lg:grid-cols-3">


                <!-- ===================================================== -->
                <!-- LEFT : ADD CATEGORY -->
                <!-- ===================================================== -->

                <div class="lg:col-span-1">

                    <div class="overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-sm">


                        <!-- HEADER -->

                        <div class="bg-black px-6 py-7 sm:px-7">

                            <div class="flex items-start gap-4">

                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-indigo-700 shadow-sm">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4"
                                        />

                                    </svg>

                                </div>


                                <div>

                                    <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-100">
                                        Master Data
                                    </p>

                                    <h3 class="mt-1 text-xl font-black text-white">
                                        Tambah Kategori
                                    </h3>

                                </div>

                            </div>

                        </div>



                        <!-- FORM -->

                        <div class="px-6 py-7 sm:px-7">

                            <p class="mb-5 text-xs font-semibold leading-relaxed text-gray-600">
                                Tambahkan kategori baru untuk mengelompokkan barang inventory.
                            </p>


                            <form
                                action="{{ route('admin.categories.store') }}"
                                method="POST"
                            >

                                @csrf


                                <div>

                                    <label
                                        for="category_name"
                                        class="mb-2 block text-[9px] font-black uppercase tracking-widest text-gray-700"
                                    >
                                        Nama Kategori
                                    </label>


                                    <input
                                        id="category_name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="Contoh: Alat Tulis"
                                        required
                                        autocomplete="off"
                                        class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3.5 text-sm font-bold text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100"
                                    >


                                    @error('name')

                                        <p class="mt-2 text-[9px] font-bold text-red-600">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                <button
                                    type="submit"
                                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-gray-950 px-5 py-3.5 text-[9px] font-black uppercase tracking-[0.16em] text-white transition hover:bg-indigo-700"
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
                                            d="M12 4v16m8-8H4"
                                        />

                                    </svg>

                                    Simpan Kategori

                                </button>

                            </form>

                        </div>

                    </div>



                    <!-- ================================================= -->
                    <!-- INFO BOX -->
                    <!-- ================================================= -->

                    <div class="mt-5 rounded-2xl border border-indigo-200 bg-indigo-50 px-5 py-4">

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-700 text-white">

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
                                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p class="text-[9px] font-black uppercase tracking-widest text-indigo-800">
                                    Catatan
                                </p>

                                <p class="mt-1 text-[10px] font-semibold leading-relaxed text-indigo-900">
                                    Gunakan nama kategori yang jelas dan konsisten agar barang lebih mudah dikelola.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ===================================================== -->
                <!-- RIGHT : CATEGORY LIST -->
                <!-- ===================================================== -->

                <div class="lg:col-span-2">

                    <div class="overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-sm">


                        <!-- HEADER -->

                        <div class="border-b border-gray-200 bg-gray-950 px-6 py-6 sm:px-7">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-300">
                                        Category List
                                    </p>

                                    <h3 class="mt-1 text-xl font-black text-white">
                                        Daftar Kategori
                                    </h3>

                                </div>


                                <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-3.5 py-2.5">

                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                        Total
                                    </span>

                                    <span class="text-base font-black text-white">
                                        {{ $categories->count() }}
                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- ================================================= -->
                        <!-- TABLE -->
                        <!-- ================================================= -->

                        @if($categories->count() > 0)

                            <div class="overflow-x-auto">

                                <table class="min-w-full border-collapse">

                                    <thead class="bg-gray-100">

                                        <tr class="border-b-2 border-gray-200">

                                            <th
                                                class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-widest text-gray-700 sm:px-7"
                                            >
                                                Nama Kategori
                                            </th>


                                            <th
                                                class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest text-gray-700"
                                            >
                                                Total Barang
                                            </th>


                                            <th
                                                class="px-6 py-4 text-right text-[9px] font-black uppercase tracking-widest text-gray-700 sm:px-7"
                                            >
                                                Aksi
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach($categories as $category)

                                            <tr
                                                class="group border-b border-gray-100 transition-colors last:border-b-0 hover:bg-indigo-50/50"
                                            >

                                                <!-- CATEGORY -->

                                                <td class="px-6 py-5 sm:px-7">

                                                    <div class="flex items-center gap-4">

                                                        <div
                                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-700 text-xs font-black text-white"
                                                        >

                                                            {{ $loop->iteration }}

                                                        </div>


                                                        <div class="min-w-0">

                                                            <p class="truncate text-sm font-black text-gray-950">
                                                                {{ $category->name }}
                                                            </p>

                                                            <p class="mt-0.5 text-[8px] font-bold uppercase tracking-widest text-gray-500">
                                                                Category
                                                            </p>

                                                        </div>

                                                    </div>

                                                </td>



                                                <!-- TOTAL ITEM -->

                                                <td class="px-4 py-5 text-center">

                                                    @if($category->items_count > 0)

                                                        <span
                                                            class="inline-flex min-w-[72px] items-center justify-center rounded-xl bg-indigo-700 px-3 py-2 text-[9px] font-black text-white"
                                                        >

                                                            {{ $category->items_count }}

                                                            {{ $category->items_count === 1 ? 'Item' : 'Items' }}

                                                        </span>

                                                    @else

                                                        <span
                                                            class="inline-flex min-w-[72px] items-center justify-center rounded-xl bg-gray-200 px-3 py-2 text-[9px] font-black text-gray-700"
                                                        >

                                                            0 Item

                                                        </span>

                                                    @endif

                                                </td>



                                                <!-- DELETE -->

                                                <td class="px-6 py-5 text-right sm:px-7">

                                                    <form
                                                        action="{{ route('admin.categories.destroy', $category) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus kategori {{ $category->name }}?')"
                                                    >

                                                        @csrf

                                                        @method('DELETE')


                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-3.5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-red-700"
                                                        >

                                                            <svg
                                                                class="h-3.5 w-3.5"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v-2a2 2 0 011-1.732V3h6v1.268A2 2 0 0119 6v2m-7 3v6m-4-6v6"
                                                                />

                                                            </svg>

                                                            Hapus

                                                        </button>

                                                    </form>

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        @else

                            <!-- ================================================= -->
                            <!-- EMPTY -->
                            <!-- ================================================= -->

                            <div class="px-6 py-20 text-center sm:px-8">

                                <div
                                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-500"
                                >

                                    <svg
                                        class="h-7 w-7"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.7"
                                            d="M4 7h16M4 12h16M4 17h10"
                                        />

                                    </svg>

                                </div>


                                <h4 class="mt-5 text-lg font-black text-gray-950">
                                    Belum Ada Kategori
                                </h4>


                                <p class="mx-auto mt-2 max-w-sm text-xs font-semibold leading-relaxed text-gray-500">
                                    Belum ada kategori yang tersedia.
                                    Gunakan form di sebelah kiri untuk menambahkan kategori pertama.
                                </p>

                            </div>

                        @endif


                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>