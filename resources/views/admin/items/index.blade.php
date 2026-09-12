<x-app-layout>

    <div class="min-h-screen bg-[#f8f9fa] pb-12 relative">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

                <div class="flex flex-col md:flex-row justify-between items-center gap-6">


                    <!-- TITLE -->
                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">

                            <svg
                                class="w-6 h-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
                                />
                            </svg>

                        </div>


                        <div>

                            <h2 class="text-xl font-black text-gray-900 tracking-tight leading-none">
                                Vault & Inventory
                            </h2>

                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1">
                                SC Centralized Database
                            </p>

                        </div>

                    </div>



                    <!-- SEARCH + ADD -->
                    <div class="flex items-center gap-4 w-full md:w-auto flex-1 justify-end">

                        <form
                            action="{{ route('admin.items.index') }}"
                            method="GET"
                            id="searchForm"
                            class="relative group w-full md:w-80"
                        >

                            @if(request('type'))

                                <input
                                    type="hidden"
                                    name="type"
                                    value="{{ request('type') }}"
                                >

                            @endif


                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">

                                <svg
                                    class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>

                            </div>


                            <input
                                type="text"
                                name="search"
                                id="searchInput"
                                placeholder="Search parameters..."
                                value="{{ request('search') }}"
                                class="block w-full pl-11 pr-4 py-3 bg-gray-100/50 border-transparent rounded-2xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300"
                            >

                        </form>



                        <a
                            href="{{ route('admin.items.create') }}"
                            class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-white uppercase tracking-widest transition-all duration-300 bg-gray-900 rounded-2xl hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-95 whitespace-nowrap overflow-hidden group shrink-0"
                        >

                            <svg
                                class="w-4 h-4 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                />
                            </svg>

                            Add Entry

                        </a>

                    </div>

                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8">


            <!-- SUCCESS -->
            @if(session('success'))

                <div class="mb-8 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">

                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">

                        <svg
                            class="w-4 h-4 text-white"
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



            <!-- ERROR -->
            @if(session('error'))

                <div class="mb-8 px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">

                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">

                        <svg
                            class="w-4 h-4 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>

                    </div>

                    <p class="text-sm font-bold text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            @endif



            <!-- ========================================================= -->
            <!-- TRANSACTION TYPE TABS -->
            <!-- ========================================================= -->

            <div class="mb-10">

                <div class="flex flex-nowrap overflow-x-auto gap-4 pb-4 -mx-4 px-4 sm:mx-0 sm:px-0 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">


                    <!-- ALL -->
                    <a
                        href="{{ route('admin.items.index') }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2
                        {{
                            !request('type')
                                ? 'bg-gray-900 border-gray-900 text-white shadow-xl shadow-gray-300/50'
                                : 'bg-white border-gray-100 text-gray-500 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-50'
                        }}"
                    >

                        <svg
                            class="w-5 h-5 {{ !request('type') ? 'text-white' : 'text-gray-400' }}"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                            />
                        </svg>

                        Semua Database
                        ({{ $counts['total'] ?? 0 }})

                    </a>



                    <!-- PERALATAN -->
                    <a
                        href="{{ route('admin.items.index', ['type' => 'Peralatan']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2
                        {{
                            request('type') === 'Peralatan'
                                ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-xl shadow-indigo-100/50'
                                : 'bg-white border-gray-100 text-gray-500 hover:border-indigo-100 hover:text-indigo-600 hover:bg-indigo-50/50'
                        }}"
                    >

                        <div
                            class="w-2.5 h-2.5 rounded-full
                            {{
                                request('type') === 'Peralatan'
                                    ? 'bg-indigo-500 animate-pulse'
                                    : 'bg-gray-300'
                            }}"
                        ></div>

                        Peralatan
                        ({{ $counts['peralatan'] ?? 0 }})

                    </a>



                    <!-- HANDY TALKIE -->
                    <a
                        href="{{ route('admin.items.index', ['type' => 'Handy Talkie']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2
                        {{
                            request('type') === 'Handy Talkie'
                                ? 'bg-amber-50 border-amber-200 text-amber-700 shadow-xl shadow-amber-100/50'
                                : 'bg-white border-gray-100 text-gray-500 hover:border-amber-100 hover:text-amber-600 hover:bg-amber-50/50'
                        }}"
                    >

                        <div
                            class="w-2.5 h-2.5 rounded-full
                            {{
                                request('type') === 'Handy Talkie'
                                    ? 'bg-amber-500 animate-pulse'
                                    : 'bg-gray-300'
                            }}"
                        ></div>

                        Handy Talkie
                        ({{ $counts['ht'] ?? 0 }})

                    </a>



                    <!-- HABIS PAKAI -->
                    <a
                        href="{{ route('admin.items.index', ['type' => 'Habis Pakai']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2
                        {{
                            request('type') === 'Habis Pakai'
                                ? 'bg-rose-50 border-rose-200 text-rose-700 shadow-xl shadow-rose-100/50'
                                : 'bg-white border-gray-100 text-gray-500 hover:border-rose-100 hover:text-rose-600 hover:bg-rose-50/50'
                        }}"
                    >

                        <div
                            class="w-2.5 h-2.5 rounded-full
                            {{
                                request('type') === 'Habis Pakai'
                                    ? 'bg-rose-500 animate-pulse'
                                    : 'bg-gray-300'
                            }}"
                        ></div>

                        Habis Pakai
                        ({{ $counts['habispakai'] ?? 0 }})

                    </a>



                    <!-- MERCHANDISE -->
                    <a
                        href="{{ route('admin.items.index', ['type' => 'Merchandise']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2
                        {{
                            request('type') === 'Merchandise'
                                ? 'bg-emerald-50 border-emerald-200 text-emerald-700 shadow-xl shadow-emerald-100/50'
                                : 'bg-white border-gray-100 text-gray-500 hover:border-emerald-100 hover:text-emerald-600 hover:bg-emerald-50/50'
                        }}"
                    >

                        <div
                            class="w-2.5 h-2.5 rounded-full
                            {{
                                request('type') === 'Merchandise'
                                    ? 'bg-emerald-500 animate-pulse'
                                    : 'bg-gray-300'
                            }}"
                        ></div>

                        Merchandise
                        ({{ $counts['merchandise'] ?? 0 }})

                    </a>

                </div>

            </div>



            <!-- ========================================================= -->
            <!-- ITEM GRID -->
            <!-- ========================================================= -->

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">


                @foreach($items as $item)

                    @php

                        /*
                         * =====================================================
                         * FINAL TRANSACTION TYPE
                         * =====================================================
                         */

                        $type =
                            $item->transaction_type;


                        /*
                         * =====================================================
                         * ONLY 4 TRANSACTION TYPES
                         * =====================================================
                         */

                        $isPeralatan =
                            $type === 'Peralatan';


                        $isHandyTalkie =
                            $type === 'Handy Talkie';


                        $isConsumable =
                            $type === 'Habis Pakai';


                        $isMerchandise =
                            $type === 'Merchandise';


                        /*
                         * =====================================================
                         * BADGE
                         * =====================================================
                         */

                        $badgeText =
                            $type;


                        $badgeClass =
                            'bg-gray-900 text-white';


                        if ($isPeralatan) {

                            $badgeText =
                                'Peralatan';

                            $badgeClass =
                                'bg-indigo-600 text-white';

                        }

                        elseif ($isHandyTalkie) {

                            $badgeText =
                                'Handy Talkie';

                            $badgeClass =
                                'bg-amber-500 text-white';

                        }

                        elseif ($isConsumable) {

                            $badgeText =
                                'Habis Pakai';

                            $badgeClass =
                                'bg-rose-500 text-white';

                        }

                        elseif ($isMerchandise) {

                            $badgeText =
                                'Merchandise';

                            $badgeClass =
                                'bg-emerald-500 text-white';

                        }


                        /*
                         * =====================================================
                         * TRANSACTION DETAIL
                         * =====================================================
                         */

                        $transactionDetail =
                            $item->transaction_detail ?? null;

                    @endphp



                    <!-- ================================================= -->
                    <!-- ITEM CARD -->
                    <!-- ================================================= -->

                    <div
                        class="group relative bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-indigo-100/50 hover:-translate-y-2 transition-all duration-500 flex flex-col overflow-hidden"
                    >


                        <!-- ================================================= -->
                        <!-- IMAGE -->
                        <!-- ================================================= -->

                        <div class="relative h-60 overflow-hidden bg-gray-50 p-2">


                            @if($item->item_photo)

                                <img
                                    src="{{ asset('storage/' . $item->item_photo) }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover rounded-3xl transition-transform duration-700 group-hover:scale-105"
                                >

                            @else

                                <div class="w-full h-full rounded-3xl bg-gray-100 flex items-center justify-center text-gray-300 font-bold text-xs uppercase tracking-widest">

                                    No Image

                                </div>

                            @endif



                            <!-- TYPE BADGE -->
                            <div class="absolute top-5 left-5 flex flex-col gap-2 z-10">

                                <span
                                    class="inline-flex px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg {{ $badgeClass }}"
                                >
                                    {{ $badgeText }}
                                </span>

                            </div>



                            <!-- CONDITION -->
                            <span
                                class="absolute top-5 right-5 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg backdrop-blur-md
                                {{
                                    $item->condition_status === 'Good'
                                        ? 'bg-white/90 text-green-600'
                                        : 'bg-red-500/90 text-white'
                                }}"
                            >

                                {{ $item->condition_status }}

                            </span>



                            <!-- HOVER ACTIONS -->
                            <div class="absolute inset-x-0 bottom-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out flex gap-2 z-20">


                                <a
                                    href="{{ route('admin.items.edit', $item) }}"
                                    class="flex-1 bg-white/90 backdrop-blur-sm border border-white/50 text-gray-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-2xl text-center hover:bg-indigo-600 hover:text-white transition-all shadow-lg flex items-center justify-center"
                                >

                                    Modify

                                </a>



                                <form
                                    action="{{ route('admin.items.destroy', $item) }}"
                                    method="POST"
                                    class="shrink-0 h-full"
                                    onsubmit="return confirm('Sistem: Yakin ingin menghapus aset ini secara permanen?')"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="w-12 h-[38px] bg-red-500/90 backdrop-blur-sm border border-red-400/50 text-white rounded-2xl flex items-center justify-center hover:bg-red-600 transition-all shadow-lg"
                                    >

                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 01-1 1v3M4 7h16"
                                            />
                                        </svg>

                                    </button>

                                </form>

                            </div>

                        </div>



                        <!-- ================================================= -->
                        <!-- CONTENT -->
                        <!-- ================================================= -->

                        <div class="p-6 flex-grow flex flex-col">


                            <!-- BASIC INFORMATION -->
                            <div>

                                <div class="flex justify-between items-start mb-2">

                                    <h3 class="text-lg font-black text-gray-900 leading-tight truncate pr-4">
                                        {{ $item->name }}
                                    </h3>

                                </div>


                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-3">
                                    ID: {{ $item->id }}
                                </p>


                                @if($item->subcategory)

                                    <span class="inline-flex mb-3 rounded-lg bg-gray-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-gray-500">
                                        {{ $item->subcategory }}
                                    </span>

                                @endif


                                <p class="text-xs text-gray-500 line-clamp-2 min-h-[32px]">
                                    {{ $item->description ?: 'Tidak ada deskripsi barang.' }}
                                </p>

                            </div>



                            <!-- ================================================= -->
                            <!-- INFORMATION / NOTES -->
                            <!-- ================================================= -->

                            <div class="mt-4 rounded-2xl border border-gray-100 bg-gray-50 px-3.5 py-3">

                                <div class="flex items-start gap-2.5">


                                    <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm">

                                        <svg
                                            class="h-3.5 w-3.5 text-indigo-500"
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



                                    <div class="min-w-0 flex-1">

                                        <p class="text-[8px] font-black uppercase tracking-[0.16em] text-gray-400">
                                            Informasi Barang
                                        </p>


                                        <div class="mt-2 space-y-1.5">


                                            <!-- CATEGORY -->
                                            <div class="flex items-start justify-between gap-3">

                                                <span class="text-[8px] font-bold uppercase tracking-wider text-gray-400">
                                                    Category
                                                </span>

                                                <span class="text-right text-[9px] font-black text-gray-700">
                                                    {{ $item->category->name ?? 'Tidak ada kategori' }}
                                                </span>

                                            </div>



                                            <!-- TRANSACTION TYPE -->
                                            <div class="flex items-start justify-between gap-3">

                                                <span class="text-[8px] font-bold uppercase tracking-wider text-gray-400">
                                                    Transaction Type
                                                </span>

                                                <span class="text-right text-[9px] font-black text-indigo-600">
                                                    {{ $type }}
                                                </span>

                                            </div>



                                            <!-- TRANSACTION DETAIL -->
                                            @if($transactionDetail)

                                                <div class="flex items-start justify-between gap-3">

                                                    <span class="text-[8px] font-bold uppercase tracking-wider text-gray-400">
                                                        Detail
                                                    </span>

                                                    <span class="text-right text-[9px] font-black text-gray-600">
                                                        {{ $transactionDetail }}
                                                    </span>

                                                </div>

                                            @endif

                                        </div>



                                        <!-- EXPLANATION -->
                                        <p class="mt-2 border-t border-gray-200 pt-2 text-[8px] font-semibold leading-relaxed text-gray-400">

                                            Category = Jenis Barang
                                            <span class="mx-1">•</span>
                                            Transaction Type = Cara Barang Diproses

                                        </p>

                                    </div>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- PRICE + STOCK -->
                            <!-- ================================================= -->

                            <div class="mt-5 flex items-end justify-between border-t border-gray-100 pt-5">


                                <div>

                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">

                                        {{
                                            ($isConsumable || $isMerchandise)
                                                ? 'Price'
                                                : 'Rental Rate'
                                        }}

                                    </p>


                                    <p class="text-lg font-black text-indigo-600">

                                        Rp
                                        {{ number_format(
                                            $item->price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </p>

                                </div>



                                <div class="text-right">

                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                        Available Stock
                                    </p>


                                    <p
                                        class="text-sm font-black
                                        {{
                                            $item->stock_quantity > 0
                                                ? 'text-gray-900'
                                                : 'text-red-500'
                                        }}"
                                    >

                                        {{ $item->stock_quantity }}
                                        Unit

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>



            <!-- ========================================================= -->
            <!-- PAGINATION -->
            <!-- ========================================================= -->

            @if($items->hasPages())

                <div class="mt-12 flex justify-center">

                    <div class="bg-white px-2 py-1 rounded-2xl shadow-sm border border-gray-100">

                        {{ $items->links() }}

                    </div>

                </div>

            @endif



            <!-- ========================================================= -->
            <!-- EMPTY STATE -->
            <!-- ========================================================= -->

            @if($items->isEmpty())

                <div class="text-center py-32">

                    <div class="w-24 h-24 bg-white border border-gray-100 shadow-sm rounded-[2rem] rotate-12 flex items-center justify-center mx-auto mb-6 text-gray-300">

                        <svg
                            class="w-12 h-12 -rotate-12"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                            />
                        </svg>

                    </div>


                    <p class="text-gray-400 font-black uppercase text-[11px] tracking-[0.3em]">
                        Inventory Kosong
                    </p>

                </div>

            @endif

        </div>

    </div>



    <!-- ============================================================= -->
    <!-- SEARCH SCRIPT -->
    <!-- ============================================================= -->

    <script>

        let timeout = null;


        const searchInput =
            document.getElementById(
                'searchInput'
            );


        const searchForm =
            document.getElementById(
                'searchForm'
            );


        if (
            searchInput &&
            searchForm
        ) {

            searchInput.addEventListener(
                'keyup',
                function () {

                    clearTimeout(
                        timeout
                    );


                    timeout =
                        setTimeout(
                            function () {

                                searchForm.submit();

                            },
                            700
                        );

                }
            );

        }

    </script>

</x-app-layout>