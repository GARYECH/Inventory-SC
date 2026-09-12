<x-app-layout>

    <div class="min-h-screen bg-[#f8f9fa] pb-12">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 backdrop-blur-xl shadow-sm">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">


                    <!-- BRAND -->
                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg shadow-indigo-200">

                            <svg
                                class="h-5 w-5 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>

                        </div>


                        <div>

                            <h1 class="text-lg font-black tracking-tight text-gray-950 sm:text-xl">
                                Catalog Hub
                            </h1>

                            <p class="mt-0.5 text-[9px] font-black uppercase tracking-[0.22em] text-indigo-600">
                                Rent & Request Gear
                            </p>

                        </div>

                    </div>


                    <!-- SEARCH + CHECKOUT -->
                    <div class="flex w-full items-center gap-2.5 sm:w-auto">

                        <form
                            action="{{ route('student.dashboard') }}"
                            method="GET"
                            id="searchForm"
                            class="relative min-w-0 flex-1 sm:w-72 sm:flex-none"
                        >

                            @if(request('type'))
                                <input
                                    type="hidden"
                                    name="type"
                                    value="{{ request('type') }}"
                                >
                            @endif


                            @if(request('category'))
                                <input
                                    type="hidden"
                                    name="category"
                                    value="{{ request('category') }}"
                                >
                            @endif


                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">

                                <svg
                                    class="h-4 w-4 text-gray-400"
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
                                value="{{ request('search') }}"
                                placeholder="Search gear, cameras..."
                                autocomplete="off"
                                class="w-full rounded-2xl border border-transparent bg-gray-100/70 py-3.5 pl-11 pr-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                            >

                        </form>


                        <a
                            href="{{ route('student.cart.index') }}"
                            class="relative inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-gray-950 px-4 py-3.5 text-[9px] font-black uppercase tracking-[0.14em] text-white shadow-lg transition-all hover:bg-indigo-600 active:scale-95 sm:px-6 sm:text-[10px]"
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
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>

                            Checkout


                            @if(isset($cartCount) && $cartCount > 0)

                                <span class="absolute -right-1.5 -top-1.5 flex h-5 min-w-[20px] items-center justify-center rounded-full border-2 border-white bg-indigo-500 px-1 text-[8px] font-black">
                                    {{ $cartCount }}
                                </span>

                            @endif

                        </a>

                    </div>

                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">


            <!-- ===================================================== -->
            <!-- FLASH SUCCESS -->
            <!-- ===================================================== -->

            @if(session('success'))

                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 shadow-sm">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500">

                        <svg
                            class="h-4 w-4 text-white"
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

                    <p class="text-sm font-bold text-emerald-800">
                        {{ session('success') }}
                    </p>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- FLASH ERROR -->
            <!-- ===================================================== -->

            @if(session('error'))

                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-red-100 bg-red-50 px-5 py-4 shadow-sm">

                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500">

                        <svg
                            class="h-4 w-4 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c-.98 0-1.54-1.06-1.05-1.91L13.05 4.91c-.47-.82-1.63-.82-2.1 0L3.89 16.09c-.49.85.07 1.91 1.05 1.91z"
                            />
                        </svg>

                    </div>

                    <p class="text-sm font-bold text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- TRANSACTION TYPE FILTER -->
            <!-- ===================================================== -->

            <div class="mb-5">

                <div class="mb-2 flex items-center justify-between">

                    <div>

                        <p class="text-[8px] font-black uppercase tracking-[0.18em] text-gray-400">
                            Jenis Transaksi
                        </p>

                    </div>

                </div>


                <div class="catalog-scrollbar flex gap-3 overflow-x-auto pb-2">


                    <!-- ALL -->
                    <a
                        href="{{ route(
                            'student.dashboard',
                            request()->except([
                                'type',
                                'page'
                            ])
                        ) }}"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                        {{ !request('type')
                            ? 'border-gray-950 bg-gray-950 text-white shadow-lg'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:text-gray-900'
                        }}"
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
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                            />
                        </svg>

                        Semua

                    </a>



                    <!-- PERALATAN -->
                    <a
                        href="{{ route(
                            'student.dashboard',
                            array_merge(
                                request()->except('page'),
                                [
                                    'type' => 'Peralatan'
                                ]
                            )
                        ) }}"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                        {{ request('type') === 'Peralatan'
                            ? 'border-indigo-200 bg-indigo-50 text-indigo-700'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600'
                        }}"
                    >

                        <span class="h-2.5 w-2.5 rounded-full {{ request('type') === 'Peralatan' ? 'bg-indigo-500' : 'bg-gray-300' }}"></span>

                        Peralatan SC

                    </a>



                    <!-- HT -->
                    <a
                        href="{{ route(
                            'student.dashboard',
                            array_merge(
                                request()->except('page'),
                                [
                                    'type' => 'HT'
                                ]
                            )
                        ) }}"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                        {{ request('type') === 'HT'
                            ? 'border-amber-200 bg-amber-50 text-amber-700'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600'
                        }}"
                    >

                        <span class="h-2.5 w-2.5 rounded-full {{ request('type') === 'HT' ? 'bg-amber-500' : 'bg-gray-300' }}"></span>

                        Handy Talkie

                    </a>



                    <!-- HABIS PAKAI -->
                    <a
                        href="{{ route(
                            'student.dashboard',
                            array_merge(
                                request()->except('page'),
                                [
                                    'type' => 'HabisPakai'
                                ]
                            )
                        ) }}"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                        {{ request('type') === 'HabisPakai'
                            ? 'border-rose-200 bg-rose-50 text-rose-700'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600'
                        }}"
                    >

                        <span class="h-2.5 w-2.5 rounded-full {{ request('type') === 'HabisPakai' ? 'bg-rose-500' : 'bg-gray-300' }}"></span>

                        Habis Pakai

                    </a>



                    <!-- MERCHANDISE -->
                    <a
                        href="{{ route(
                            'student.dashboard',
                            array_merge(
                                request()->except('page'),
                                [
                                    'type' => 'Merchandise'
                                ]
                            )
                        ) }}"
                        class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                        {{ request('type') === 'Merchandise'
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                            : 'border-gray-200 bg-white text-gray-500 hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600'
                        }}"
                    >

                        <span class="h-2.5 w-2.5 rounded-full {{ request('type') === 'Merchandise' ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>

                        Merchandise

                    </a>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- DYNAMIC CATEGORY FILTER -->
            <!-- ===================================================== -->

            @if(isset($categories) && $categories->count() > 0)

                <div class="mb-8">

                    <div class="mb-2 flex items-center justify-between">

                        <div>

                            <p class="text-[8px] font-black uppercase tracking-[0.18em] text-gray-400">
                                Kategori Barang
                            </p>

                            <p class="mt-0.5 text-[9px] font-semibold text-gray-400">
                                Pilih kategori untuk mempersempit katalog
                            </p>

                        </div>

                    </div>


                    <div class="catalog-scrollbar flex gap-3 overflow-x-auto pb-2">


                        <!-- ALL CATEGORIES -->
                        <a
                            href="{{ route(
                                'student.dashboard',
                                request()->except([
                                    'category',
                                    'page'
                                ])
                            ) }}"
                            class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                            {{ !request('category')
                                ? 'border-gray-950 bg-gray-950 text-white shadow-lg'
                                : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:text-gray-900'
                            }}"
                        >

                            Semua Kategori

                        </a>


                        @foreach($categories as $category)

                            <a
                                href="{{ route(
                                    'student.dashboard',
                                    array_merge(
                                        request()->except('page'),
                                        [
                                            'category' => $category->slug
                                        ]
                                    )
                                ) }}"
                                class="shrink-0 inline-flex items-center gap-2 rounded-2xl border px-5 py-3 text-[10px] font-black uppercase tracking-[0.14em] transition-all
                                {{ request('category') === $category->slug
                                    ? 'border-indigo-200 bg-indigo-50 text-indigo-700 shadow-sm'
                                    : 'border-gray-200 bg-white text-gray-500 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600'
                                }}"
                            >

                                <span
                                    class="h-2 w-2 rounded-full
                                    {{ request('category') === $category->slug
                                        ? 'bg-indigo-500'
                                        : 'bg-gray-300'
                                    }}"
                                ></span>

                                {{ $category->name }}


                                @if(isset($category->items_count))

                                    <span
                                        class="rounded-md bg-black/5 px-1.5 py-0.5 text-[8px] font-black
                                        {{ request('category') === $category->slug
                                            ? 'text-indigo-600'
                                            : 'text-gray-400'
                                        }}"
                                    >
                                        {{ $category->items_count }}
                                    </span>

                                @endif

                            </a>

                        @endforeach

                    </div>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- ITEM GRID -->
            <!-- ===================================================== -->

            @if(!$items->isEmpty())

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">


                    @foreach($items as $item)

                        @php

                            $type =
                                $item->transaction_type;


                            /*
                             * RENTAL
                             */
                            $isRental =
                                in_array(
                                    $type,
                                    [
                                        'Peralatan',
                                        'HT UV-82',
                                        'HT 888s',
                                        'HT UV-5R',
                                        'Internal Rental',
                                        'Vendor Rental'
                                    ]
                                );


                            /*
                             * CONSUMABLE
                             */
                            $isConsumable =
                                in_array(
                                    $type,
                                    [
                                        'ATK',
                                        'Obat'
                                    ]
                                );


                            /*
                             * MERCHANDISE
                             */
                            $isMerchandise =
                                $type ===
                                'Merchandise';


                            /*
                             * BADGE
                             */
                            $badgeText =
                                $type;

                            $badgeClass =
                                'bg-gray-900 text-white';


                            if (
                                $type ===
                                'Peralatan'
                            ) {

                                $badgeText =
                                    'Peralatan SC';

                                $badgeClass =
                                    'bg-indigo-600 text-white';

                            }


                            elseif (
                                in_array(
                                    $type,
                                    [
                                        'HT UV-82',
                                        'HT 888s',
                                        'HT UV-5R'
                                    ]
                                )
                            ) {

                                $badgeText =
                                    'Handy Talkie';

                                $badgeClass =
                                    'bg-amber-500 text-white';

                            }


                            elseif (
                                $isConsumable
                            ) {

                                $badgeText =
                                    $type === 'Obat'
                                        ? 'Habis Pakai · Obat'
                                        : 'Habis Pakai · ATK';

                                $badgeClass =
                                    'bg-rose-500 text-white';

                            }


                            elseif (
                                $isMerchandise
                            ) {

                                $badgeText =
                                    'Merchandise';

                                $badgeClass =
                                    'bg-emerald-500 text-white';

                            }


                            /*
                             * ACTIVE RENTAL BOOKINGS
                             */
                            $activeSchedules =
                                collect();


                            if ($isRental) {

                                $activeSchedules =
                                    $item->orderItems
                                        ->filter(
                                            function ($detail) {

                                                if (
                                                    !$detail->order
                                                ) {
                                                    return false;
                                                }

                                                return !in_array(
                                                    $detail->order->status,
                                                    [
                                                        'Returned',
                                                        'Resolved (Fine Paid)',
                                                        'Rejected',
                                                        'Cancelled'
                                                    ]
                                                );

                                            }
                                        )
                                        ->sortBy(
                                            function ($detail) {

                                                return $detail
                                                    ->order
                                                    ->start_date
                                                    ? $detail
                                                        ->order
                                                        ->start_date
                                                        ->timestamp
                                                    : PHP_INT_MAX;

                                            }
                                        );

                            }


                            $displaySchedules =
                                $activeSchedules
                                    ->take(2);


                            $remainingCount =
                                max(
                                    0,
                                    $activeSchedules
                                        ->count()
                                    -
                                    $displaySchedules
                                        ->count()
                                );

                        @endphp



                        <!-- ================================================= -->
                        <!-- PRODUCT CARD -->
                        <!-- ================================================= -->

                        <div
                            class="product-card group flex min-w-0 flex-col overflow-visible rounded-[1.75rem] border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                            data-item-id="{{ $item->id }}"
                        >


                            <!-- ================================================= -->
                            <!-- IMAGE -->
                            <!-- ================================================= -->

                            <div class="relative p-2">

                                <div class="relative overflow-hidden rounded-[1.4rem] bg-gray-100">


                                    @if($item->item_photo)

                                        <img
                                            src="{{ asset('storage/' . $item->item_photo) }}"
                                            alt="{{ $item->name }}"
                                            class="aspect-[4/3] w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                        >

                                    @else

                                        <div class="flex aspect-[4/3] items-center justify-center bg-gray-100">

                                            <div class="text-center text-gray-300">

                                                <svg
                                                    class="mx-auto mb-2 h-10 w-10"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.4"
                                                        d="M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm4 13l4-4 3 3 2-2 4 3"
                                                    />
                                                </svg>

                                                <span class="text-[8px] font-black uppercase tracking-widest">
                                                    No Image
                                                </span>

                                            </div>

                                        </div>

                                    @endif



                                    <!-- CATEGORY -->
                                    <div class="absolute left-3 top-3">

                                        <span
                                            class="rounded-lg px-2.5 py-1.5 text-[8px] font-black uppercase tracking-widest shadow-lg {{ $badgeClass }}"
                                        >
                                            {{ $badgeText }}
                                        </span>

                                    </div>



                                    <!-- CONDITION -->
                                    <div class="absolute right-3 top-3">

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-[8px] font-black uppercase tracking-widest shadow-lg backdrop-blur-md
                                            {{ $item->condition_status === 'Good'
                                                ? 'bg-white/90 text-emerald-600'
                                                : 'bg-red-500 text-white'
                                            }}"
                                        >

                                            <span
                                                class="h-1.5 w-1.5 rounded-full
                                                {{ $item->condition_status === 'Good'
                                                    ? 'bg-emerald-500'
                                                    : 'bg-white'
                                                }}"
                                            ></span>

                                            {{ $item->condition_status }}

                                        </span>

                                    </div>

                                </div>

                            </div>



                            <!-- ================================================= -->
                            <!-- INFORMATION -->
                            <!-- ================================================= -->

                            <div class="flex flex-1 flex-col px-5 pb-4 pt-2">


                                <div>

                                    <h2 class="line-clamp-2 text-[17px] font-black leading-tight tracking-tight text-gray-950">
                                        {{ $item->name }}
                                    </h2>


                                    @if($item->subcategory)

                                        <span class="mt-2 inline-flex rounded-lg bg-gray-100 px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-gray-500">
                                            {{ $item->subcategory }}
                                        </span>

                                    @endif


                                    <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-gray-500">
                                        {{ $item->description ?: 'Tidak ada deskripsi barang.' }}
                                    </p>

                                </div>



                                <!-- PRICE + STOCK -->
                                <div class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-100 pt-4">

                                    <div>

                                        <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">

                                            {{
                                                ($isConsumable || $isMerchandise)
                                                    ? 'Harga'
                                                    : 'Biaya Sewa'
                                            }}

                                        </p>

                                        <p class="mt-1 text-lg font-black text-indigo-600">

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

                                        <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                            Stok
                                        </p>

                                        <p
                                            class="mt-1 text-lg font-black
                                            {{
                                                $item->stock_quantity > 0
                                                    ? 'text-gray-950'
                                                    : 'text-red-500'
                                            }}"
                                        >

                                            {{ $item->stock_quantity }}

                                            <span class="text-[9px] font-bold text-gray-400">
                                                Unit
                                            </span>

                                        </p>

                                    </div>

                                </div>



                                <!-- ================================================= -->
                                <!-- ACTIVE BOOKINGS -->
                                <!-- ================================================= -->

                                @if(
                                    $isRental &&
                                    $activeSchedules->count() > 0
                                )

                                    <div class="mt-4 rounded-2xl border border-orange-100 bg-orange-50/70 p-3">

                                        <div class="mb-2 flex items-center gap-2">

                                            <svg
                                                class="h-3.5 w-3.5 text-orange-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2h14"
                                                />
                                            </svg>

                                            <span class="text-[8px] font-black uppercase tracking-widest text-orange-600">
                                                Sedang Ter-booking
                                            </span>

                                        </div>


                                        <div class="space-y-1.5">

                                            @foreach(
                                                $displaySchedules
                                                as $detail
                                            )

                                                <div class="rounded-xl border border-orange-100 bg-white px-2.5 py-2">

                                                    <div class="flex items-center justify-between gap-2">

                                                        <div class="min-w-0">

                                                            <p class="truncate text-[9px] font-black text-orange-700">

                                                                {{ optional(
                                                                    $detail->order->start_date
                                                                )->format('d M Y') }}

                                                                @if(
                                                                    $detail->order->end_date
                                                                )

                                                                    —
                                                                    {{ optional(
                                                                        $detail->order->end_date
                                                                    )->format('d M Y') }}

                                                                @endif

                                                            </p>


                                                            <p class="mt-1 text-[8px] font-bold text-orange-400">

                                                                {{ $detail->order->start_time ?? '--:--' }}

                                                                @if(
                                                                    $detail->order->end_time
                                                                )

                                                                    →
                                                                    {{ $detail->order->end_time }}

                                                                @endif

                                                            </p>

                                                        </div>


                                                        <span class="shrink-0 rounded-lg bg-orange-100 px-2 py-1 text-[8px] font-black text-orange-600">

                                                            {{ $detail->quantity }}
                                                            Unit

                                                        </span>

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>


                                        @if($remainingCount > 0)

                                            <p class="mt-2 text-center text-[8px] font-black uppercase tracking-widest text-orange-400">

                                                +
                                                {{ $remainingCount }}
                                                jadwal lainnya

                                            </p>

                                        @endif

                                    </div>

                                @endif

                            </div>



                            <!-- ================================================= -->
                            <!-- ACTION AREA -->
                            <!-- ================================================= -->

                            <div class="border-t border-gray-100 p-4">


                                @if(
                                    $item->stock_quantity > 0
                                )


                                    <!-- VIEW BOOKING -->
                                    @if($isRental)

                                        <a
                                            href="{{ route(
                                                'student.item.schedule',
                                                $item->id
                                            ) }}"
                                            class="mb-2.5 flex w-full items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-[9px] font-black uppercase tracking-widest text-indigo-600 transition-all hover:bg-indigo-600 hover:text-white"
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
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2h14"
                                                />
                                            </svg>

                                            Lihat Booking

                                        </a>

                                    @endif



                                    <!-- OPEN SCHEDULE -->
                                    <button
                                        type="button"
                                        class="schedule-toggle flex w-full items-center justify-center gap-2 rounded-xl bg-gray-950 px-4 py-3.5 text-[9px] font-black uppercase tracking-[0.16em] text-white shadow-lg transition-all hover:bg-indigo-600 active:scale-95"
                                        data-item-id="{{ $item->id }}"
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
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2h14"
                                            />
                                        </svg>

                                        {{
                                            $isRental
                                                ? 'Pilih Jadwal Sewa'
                                                : 'Pilih Jadwal Transaksi'
                                        }}

                                    </button>



                                    <!-- ================================================= -->
                                    <!-- SCHEDULE FORM -->
                                    <!-- ================================================= -->

                                    <form
                                        action="{{ route(
                                            'student.cart.add',
                                            $item->id
                                        ) }}"
                                        method="POST"
                                        class="schedule-form mt-3 hidden rounded-2xl border border-indigo-100 bg-indigo-50/50 p-3"
                                        data-item-id="{{ $item->id }}"
                                        data-is-rental="{{ $isRental ? '1' : '0' }}"
                                        data-subcategory="{{ $item->subcategory ?? '' }}"
                                    >

                                        @csrf


                                        <!-- ================================================= -->
                                        <!-- DATE -->
                                        <!-- ================================================= -->

                                        <div>

                                            <div class="mb-2 flex items-center justify-between gap-2">

                                                <label class="text-[8px] font-black uppercase tracking-[0.15em] text-indigo-700">

                                                    {{
                                                        $isRental
                                                            ? 'Tanggal Sewa'
                                                            : 'Tanggal Transaksi'
                                                    }}

                                                </label>


                                                <span class="text-[8px] font-bold text-gray-400">
                                                    Mulai hari ini
                                                </span>

                                            </div>


                                            <div class="relative">

                                                <div class="pointer-events-none absolute left-3 top-1/2 z-10 -translate-y-1/2">

                                                    <svg
                                                        class="h-4 w-4 text-indigo-500"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2h14"
                                                        />
                                                    </svg>

                                                </div>


                                                <input
                                                    type="text"
                                                    class="schedule-date-input w-full cursor-pointer rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-9 text-[11px] font-black text-gray-900 outline-none transition-all placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10"
                                                    placeholder="{{
                                                        $isRental
                                                            ? 'Pilih tanggal mulai - selesai'
                                                            : 'Pilih tanggal transaksi'
                                                    }}"
                                                    readonly
                                                    autocomplete="off"
                                                >


                                                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2">

                                                    <svg
                                                        class="h-3.5 w-3.5 text-gray-400"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 9l-7 7-7-7"
                                                        />
                                                    </svg>

                                                </div>

                                            </div>


                                            <input
                                                type="hidden"
                                                name="start_date"
                                                class="start-date"
                                                required
                                            >


                                            <input
                                                type="hidden"
                                                name="end_date"
                                                class="end-date"
                                                {{ $isRental ? 'required' : '' }}
                                            >

                                        </div>



                                        <!-- ================================================= -->
                                        <!-- TIME -->
                                        <!-- ================================================= -->

                                        <div class="mt-3">

                                            <div class="mb-2 flex items-center justify-between gap-2">

                                                <label class="text-[8px] font-black uppercase tracking-[0.15em] text-indigo-700">

                                                    {{
                                                        $isRental
                                                            ? 'Waktu'
                                                            : 'Jam Transaksi'
                                                    }}

                                                </label>


                                                <span class="text-[8px] font-black text-indigo-500">
                                                    17:00 — 19:00
                                                </span>

                                            </div>


                                            <div
                                                class="{{
                                                    $isRental
                                                        ? 'grid grid-cols-2'
                                                        : 'grid grid-cols-1'
                                                }} gap-2"
                                            >


                                                <!-- START -->
                                                <div>

                                                    <select
                                                        name="start_time"
                                                        class="start-time w-full rounded-xl border border-gray-200 bg-white px-3 py-3 text-[10px] font-black text-gray-900 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10"
                                                        required
                                                    >

                                                        <option value="">

                                                            {{
                                                                $isRental
                                                                    ? 'Jam Pengambilan'
                                                                    : 'Pilih Jam'
                                                            }}

                                                        </option>

                                                        <option value="17:00">
                                                            17:00
                                                        </option>

                                                        <option value="17:30">
                                                            17:30
                                                        </option>

                                                        <option value="18:00">
                                                            18:00
                                                        </option>

                                                        <option value="18:30">
                                                            18:30
                                                        </option>

                                                        <option value="19:00">
                                                            19:00
                                                        </option>

                                                    </select>

                                                </div>



                                                <!-- END -->
                                                @if($isRental)

                                                    <div>

                                                        <select
                                                            name="end_time"
                                                            class="end-time w-full rounded-xl border border-gray-200 bg-white px-3 py-3 text-[10px] font-black text-gray-900 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-500/10"
                                                            required
                                                        >

                                                            <option value="">
                                                                Jam Pengembalian
                                                            </option>

                                                            <option value="17:00">
                                                                17:00
                                                            </option>

                                                            <option value="17:30">
                                                                17:30
                                                            </option>

                                                            <option value="18:00">
                                                                18:00
                                                            </option>

                                                            <option value="18:30">
                                                                18:30
                                                            </option>

                                                            <option value="19:00">
                                                                19:00
                                                            </option>

                                                        </select>

                                                    </div>

                                                @endif

                                            </div>


                                            <p class="mt-2 text-center text-[8px] font-bold text-indigo-500">
                                                Jam transaksi tersedia pukul 17:00–19:00.
                                            </p>

                                        </div>



                                        <!-- ================================================= -->
                                        <!-- MERCHANDISE -->
                                        <!-- ================================================= -->

                                        @if(
                                            $isMerchandise &&
                                            $item->subcategory === 'Baju'
                                        )

                                            <!-- SIZE -->
                                            <div class="mt-4">

                                                <div class="mb-2 flex items-center justify-between">

                                                    <label class="text-[8px] font-black uppercase tracking-[0.15em] text-indigo-700">
                                                        Ukuran Baju
                                                    </label>

                                                    <span class="text-[8px] font-bold text-gray-400">
                                                        Size
                                                    </span>

                                                </div>


                                                <select
                                                    name="size"
                                                    class="shirt-size w-full rounded-xl border border-gray-200 bg-white px-3 py-3 text-[10px] font-black text-gray-900 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10"
                                                    required
                                                >

                                                    <option value="">
                                                        Pilih Ukuran
                                                    </option>

                                                    <option value="S">
                                                        S
                                                    </option>

                                                    <option value="M">
                                                        M
                                                    </option>

                                                    <option value="L">
                                                        L
                                                    </option>

                                                    <option value="XL">
                                                        XL
                                                    </option>

                                                    <option value="2XL">
                                                        2XL (+Rp5.000)
                                                    </option>

                                                    <option value="3XL">
                                                        3XL (+Rp10.000)
                                                    </option>

                                                    <option value="4XL">
                                                        4XL (+Rp15.000)
                                                    </option>

                                                    <option value="5XL">
                                                        5XL (+Rp20.000)
                                                    </option>

                                                </select>

                                            </div>


                                            <!-- DESIGN DRIVE -->
                                            <div class="mt-3">

                                                <label class="mb-2 block text-[8px] font-black uppercase tracking-[0.15em] text-indigo-700">
                                                    Link Drive Desain
                                                </label>


                                                <input
                                                    type="url"
                                                    name="design_link"
                                                    class="design-link w-full rounded-xl border border-gray-200 bg-white px-3 py-3 text-[10px] font-bold text-gray-900 outline-none placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10"
                                                    placeholder="https://drive.google.com/..."
                                                    required
                                                >


                                                <p class="mt-2 text-[8px] font-bold leading-relaxed text-gray-400">
                                                    Pastikan file dapat diakses oleh Admin SC.
                                                </p>

                                            </div>


                                            <!-- EXTRA SIZE -->
                                            <div class="shirt-extra-price mt-3 hidden rounded-xl border border-amber-100 bg-amber-50 px-3 py-2">

                                                <p class="text-[8px] font-black uppercase tracking-widest text-amber-600">
                                                    Tambahan Ukuran
                                                </p>

                                                <p class="mt-0.5 text-[10px] font-black text-amber-700">
                                                    +Rp0
                                                </p>

                                            </div>


                                        @elseif(
                                            $isMerchandise &&
                                            $item->subcategory === 'ID Card'
                                        )


                                            <!-- ID CARD DRIVE -->
                                            <div class="mt-4">

                                                <label class="mb-2 block text-[8px] font-black uppercase tracking-[0.15em] text-indigo-700">
                                                    Link Drive Desain
                                                </label>


                                                <input
                                                    type="url"
                                                    name="design_link"
                                                    class="design-link w-full rounded-xl border border-gray-200 bg-white px-3 py-3 text-[10px] font-bold text-gray-900 outline-none placeholder:text-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10"
                                                    placeholder="https://drive.google.com/..."
                                                    required
                                                >


                                                <p class="mt-2 text-[8px] font-bold leading-relaxed text-gray-400">
                                                    Pastikan file dapat diakses oleh Admin SC.
                                                </p>

                                            </div>

                                        @endif



                                        <!-- ================================================= -->
                                        <!-- SUMMARY -->
                                        <!-- ================================================= -->

                                        <div class="schedule-summary mt-3 hidden rounded-xl bg-white px-3 py-2.5 shadow-sm">

                                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                                Jadwal Dipilih
                                            </p>

                                            <p class="summary-text mt-1 text-[10px] font-black text-gray-800">
                                            </p>

                                        </div>



                                        <!-- ================================================= -->
                                        <!-- QUANTITY -->
                                        <!-- ================================================= -->

                                        <div class="mt-3 flex gap-2">

                                            <div class="w-20 shrink-0">

                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    value="1"
                                                    min="1"
                                                    max="{{ $item->stock_quantity }}"
                                                    required
                                                    class="w-full rounded-xl border border-gray-200 bg-white px-2 py-3 text-center text-[10px] font-black text-gray-900 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10"
                                                >

                                            </div>


                                            <button
                                                type="submit"
                                                class="flex-1 rounded-xl bg-indigo-600 px-3 py-3 text-[9px] font-black uppercase tracking-[0.12em] text-white shadow-lg shadow-indigo-100 transition-all hover:bg-indigo-700 active:scale-95"
                                            >

                                                <span class="inline-flex items-center justify-center gap-1.5">

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
                                                            d="M12 4v16m8-8H4"
                                                        />
                                                    </svg>

                                                    Ke Keranjang

                                                </span>

                                            </button>

                                        </div>

                                    </form>

                                @else

                                    <!-- OUT OF STOCK -->
                                    <button
                                        type="button"
                                        disabled
                                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-gray-100 px-4 py-3.5 text-[9px] font-black uppercase tracking-[0.16em] text-gray-400"
                                    >

                                        Stok Habis

                                    </button>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>



                <!-- ===================================================== -->
                <!-- PAGINATION -->
                <!-- ===================================================== -->

                @if($items->hasPages())

                    <div class="mt-10 flex justify-center">

                        <div class="rounded-2xl border border-gray-100 bg-white px-2 py-1 shadow-sm">

                            {{ $items->links() }}

                        </div>

                    </div>

                @endif


            @else


                <!-- ===================================================== -->
                <!-- EMPTY STATE -->
                <!-- ===================================================== -->

                <div class="py-28 text-center">

                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-[1.75rem] border border-gray-100 bg-white shadow-sm">

                        <svg
                            class="h-9 w-9 text-gray-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>

                    </div>


                    <h3 class="text-sm font-black uppercase tracking-[0.18em] text-gray-500">
                        Tidak ada barang ditemukan
                    </h3>


                    <p class="mt-2 text-xs text-gray-400">
                        Coba ubah pencarian atau pilih kategori lain.
                    </p>


                    <a
                        href="{{ route('student.dashboard') }}"
                        class="mt-5 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-indigo-600"
                    >
                        Reset Pencarian
                    </a>

                </div>

            @endif

        </div>

    </div>



    <!-- ============================================================= -->
    <!-- FLATPICKR -->
    <!-- ============================================================= -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    >

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>



    <!-- ============================================================= -->
    <!-- CSS -->
    <!-- ============================================================= -->

    <style>

        .catalog-scrollbar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }


        .catalog-scrollbar::-webkit-scrollbar {
            display: none;
        }


        .flatpickr-calendar {
            border: 1px solid #e5e7eb !important;
            border-radius: 16px !important;
            overflow: hidden !important;

            box-shadow:
                0 18px 45px rgba(15, 23, 42, 0.14),
                0 5px 15px rgba(15, 23, 42, 0.08) !important;
        }


        .flatpickr-months {
            padding: 5px !important;
        }


        .flatpickr-current-month {
            font-weight: 800 !important;
        }


        .flatpickr-day {
            border-radius: 9px !important;
            font-weight: 700 !important;
        }


        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
        }


        .flatpickr-day.inRange {
            background: #eef2ff !important;
            border-color: #eef2ff !important;
            color: #4338ca !important;
            box-shadow: none !important;
        }


        .flatpickr-day:hover {
            background: #f3f4f6 !important;
            border-color: #f3f4f6 !important;
        }


        .flatpickr-day.today {
            border-color: #a5b4fc !important;
        }


        .stock-badge {
            display: block;
            margin-top: 1px;
            font-size: 7px;
            line-height: 1;
            font-weight: 900;
        }


        .stock-green {
            color: #10b981;
        }


        .stock-red {
            color: #ef4444;
        }


        @media (max-width: 640px) {

            .flatpickr-calendar {
                max-width: calc(100vw - 24px) !important;
            }

        }

    </style>



    <!-- ============================================================= -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================= -->

    <script>

        /* ============================================================
         * SEARCH
         * ============================================================
         */

        let searchTimeout = null;


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
                        searchTimeout
                    );


                    searchTimeout =
                        setTimeout(
                            function () {

                                searchForm.submit();

                            },
                            700
                        );

                }
            );

        }



        /* ============================================================
         * DOM READY
         * ============================================================
         */

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                initializeScheduleButtons();

            }
        );



        /* ============================================================
         * TOGGLE BUTTONS
         * ============================================================
         */

        function initializeScheduleButtons() {

            const buttons =
                document.querySelectorAll(
                    '.schedule-toggle'
                );


            buttons.forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const itemId =
                                button.dataset.itemId;


                            const card =
                                document.querySelector(
                                    '.product-card[data-item-id="' +
                                    itemId +
                                    '"]'
                                );


                            if (!card) {
                                return;
                            }


                            const form =
                                card.querySelector(
                                    '.schedule-form'
                                );


                            if (!form) {
                                return;
                            }


                            const wasHidden =
                                form.classList.contains(
                                    'hidden'
                                );


                            /*
                             * Close all other forms.
                             */

                            document
                                .querySelectorAll(
                                    '.schedule-form'
                                )
                                .forEach(
                                    function (
                                        otherForm
                                    ) {

                                        if (
                                            otherForm !==
                                            form
                                        ) {

                                            otherForm.classList.add(
                                                'hidden'
                                            );

                                        }

                                    }
                                );


                            /*
                             * Toggle current.
                             */

                            form.classList.toggle(
                                'hidden'
                            );


                            /*
                             * Initialize calendar
                             * only once.
                             */

                            if (
                                wasHidden &&
                                form.dataset.initialized !==
                                'true'
                            ) {

                                initializeSchedule(
                                    form
                                );

                            }

                        }
                    );

                }
            );

        }



        /* ============================================================
         * INITIALIZE SCHEDULE
         * ============================================================
         */

        function initializeSchedule(
            form
        ) {

            const itemId =
                form.dataset.itemId;


            const isRental =
                form.dataset.isRental === '1';


            const subcategory =
                form.dataset.subcategory || '';


            const dateInput =
                form.querySelector(
                    '.schedule-date-input'
                );


            const startDate =
                form.querySelector(
                    '.start-date'
                );


            const endDate =
                form.querySelector(
                    '.end-date'
                );


            const startTime =
                form.querySelector(
                    '.start-time'
                );


            const endTime =
                form.querySelector(
                    '.end-time'
                );


            const summary =
                form.querySelector(
                    '.schedule-summary'
                );


            const summaryText =
                form.querySelector(
                    '.summary-text'
                );


            const shirtSize =
                form.querySelector(
                    '.shirt-size'
                );


            const extraPriceBox =
                form.querySelector(
                    '.shirt-extra-price'
                );


            let stockData = {};


            /*
             * ========================================================
             * DATE PICKER
             * ========================================================
             */

            const picker =
                flatpickr(
                    dateInput,
                    {

                        mode:
                            isRental
                                ? 'range'
                                : 'single',


                        minDate:
                            'today',


                        dateFormat:
                            'Y-m-d',


                        disableMobile:
                            true,


                        clickOpens:
                            true,


                        appendTo:
                            document.body,


                        onDayCreate:
                            function (
                                dObj,
                                dStr,
                                fp,
                                dayElem
                            ) {

                                if (
                                    !isRental
                                ) {

                                    return;

                                }


                                const dateString =
                                    fp.formatDate(
                                        dayElem.dateObj,
                                        'Y-m-d'
                                    );


                                if (
                                    stockData[
                                        dateString
                                    ] === undefined
                                ) {

                                    return;

                                }


                                const remaining =
                                    Number(
                                        stockData[
                                            dateString
                                        ]
                                    );


                                const badge =
                                    document.createElement(
                                        'span'
                                    );


                                badge.className =
                                    remaining > 0
                                        ? 'stock-badge stock-green'
                                        : 'stock-badge stock-red';


                                badge.textContent =
                                    remaining > 0
                                        ? 'Sisa ' +
                                            remaining
                                        : 'Habis';


                                dayElem.appendChild(
                                    badge
                                );

                            },


                        onChange:
                            function (
                                selectedDates,
                                dateStr,
                                instance
                            ) {

                                if (
                                    selectedDates.length ===
                                    0
                                ) {

                                    startDate.value =
                                        '';

                                    endDate.value =
                                        '';

                                    updateSummary();

                                    return;

                                }


                                startDate.value =
                                    instance.formatDate(
                                        selectedDates[0],
                                        'Y-m-d'
                                    );


                                if (
                                    isRental
                                ) {

                                    if (
                                        selectedDates.length >=
                                        2
                                    ) {

                                        endDate.value =
                                            instance.formatDate(
                                                selectedDates[1],
                                                'Y-m-d'
                                            );

                                    } else {

                                        endDate.value =
                                            instance.formatDate(
                                                selectedDates[0],
                                                'Y-m-d'
                                            );

                                    }

                                } else {

                                    endDate.value =
                                        '';

                                }


                                updateSummary();

                            }

                    }
                );


            /*
             * Mark initialized.
             */

            form.dataset.initialized =
                'true';



            /*
             * ========================================================
             * LOAD STOCK
             * ========================================================
             */

            if (
                isRental
            ) {

                fetch(
                    '/api/check-stock/' +
                    itemId
                )
                    .then(
                        function (response) {

                            if (
                                !response.ok
                            ) {

                                throw new Error(
                                    'Stock API failed.'
                                );

                            }

                            return response.json();

                        }
                    )
                    .then(
                        function (data) {

                            stockData =
                                data || {};


                            picker.redraw();

                        }
                    )
                    .catch(
                        function (error) {

                            console.error(
                                'Stock API Error:',
                                error
                            );

                        }
                    );

            }



            /*
             * ========================================================
             * SCHEDULE SUMMARY
             * ========================================================
             */

            function updateSummary() {

                if (
                    !startDate.value ||
                    !startTime.value
                ) {

                    summary.classList.add(
                        'hidden'
                    );

                    return;

                }


                const startText =
                    formatDate(
                        startDate.value
                    ) +
                    ' • ' +
                    startTime.value;


                /*
                 * Non-rental
                 */

                if (
                    !isRental
                ) {

                    summary.classList.remove(
                        'hidden'
                    );

                    summaryText.textContent =
                        startText;

                    return;

                }


                /*
                 * Rental, incomplete return.
                 */

                if (
                    !endDate.value ||
                    !endTime.value
                ) {

                    summary.classList.remove(
                        'hidden'
                    );

                    summaryText.textContent =
                        startText +
                        ' → Pilih pengembalian';

                    return;

                }


                const endText =
                    formatDate(
                        endDate.value
                    ) +
                    ' • ' +
                    endTime.value;


                summary.classList.remove(
                    'hidden'
                );


                summaryText.textContent =
                    startText +
                    ' → ' +
                    endText;

            }



            /*
             * ========================================================
             * SHIRT SIZE EXTRA PRICE
             * ========================================================
             */

            if (
                shirtSize &&
                extraPriceBox
            ) {

                shirtSize.addEventListener(
                    'change',
                    function () {

                        const extra =
                            getSizeExtraPrice(
                                shirtSize.value
                            );


                        if (
                            extra > 0
                        ) {

                            extraPriceBox.classList.remove(
                                'hidden'
                            );


                            const priceText =
                                extraPriceBox.querySelector(
                                    'p:last-child'
                                );


                            if (
                                priceText
                            ) {

                                priceText.textContent =
                                    '+Rp' +
                                    extra.toLocaleString(
                                        'id-ID'
                                    );

                            }

                        } else {

                            extraPriceBox.classList.add(
                                'hidden'
                            );

                        }

                    }
                );

            }



            /*
             * ========================================================
             * TIME LISTENERS
             * ========================================================
             */

            if (
                startTime
            ) {

                startTime.addEventListener(
                    'change',
                    updateSummary
                );

            }


            if (
                endTime
            ) {

                endTime.addEventListener(
                    'change',
                    updateSummary
                );

            }



            /*
             * ========================================================
             * FORM VALIDATION
             * ========================================================
             */

            form.addEventListener(
                'submit',
                function (event) {

                    /*
                     * DATE
                     */

                    if (
                        !startDate.value
                    ) {

                        event.preventDefault();

                        alert(
                            'Silakan pilih tanggal transaksi terlebih dahulu.'
                        );

                        return;

                    }


                    /*
                     * START TIME
                     */

                    if (
                        !startTime.value
                    ) {

                        event.preventDefault();

                        alert(
                            'Silakan pilih jam transaksi terlebih dahulu.'
                        );

                        return;

                    }


                    /*
                     * Baju
                     */

                    if (
                        subcategory ===
                        'Baju'
                    ) {

                        if (
                            !shirtSize ||
                            !shirtSize.value
                        ) {

                            event.preventDefault();

                            alert(
                                'Silakan pilih ukuran baju terlebih dahulu.'
                            );

                            return;

                        }

                    }


                    /*
                     * Rental.
                     */

                    if (
                        isRental
                    ) {

                        if (
                            !endDate.value
                        ) {

                            event.preventDefault();

                            alert(
                                'Silakan pilih tanggal pengembalian terlebih dahulu.'
                            );

                            return;

                        }


                        if (
                            !endTime.value
                        ) {

                            event.preventDefault();

                            alert(
                                'Silakan pilih jam pengembalian terlebih dahulu.'
                            );

                            return;

                        }


                        const startDateTime =
                            new Date(
                                startDate.value +
                                'T' +
                                startTime.value +
                                ':00'
                            );


                        const endDateTime =
                            new Date(
                                endDate.value +
                                'T' +
                                endTime.value +
                                ':00'
                            );


                        if (
                            endDateTime <=
                            startDateTime
                        ) {

                            event.preventDefault();

                            alert(
                                'Waktu pengembalian harus setelah waktu pengambilan.'
                            );

                            return;

                        }

                    }

                }
            );

        }



        /* ============================================================
         * SIZE EXTRA PRICE
         * ============================================================
         */

        function getSizeExtraPrice(
            size
        ) {

            const prices = {

                '2XL':
                    5000,

                '3XL':
                    10000,

                '4XL':
                    15000,

                '5XL':
                    20000

            };


            return prices[size] ||
                0;

        }



        /* ============================================================
         * DATE FORMAT
         * ============================================================
         */

        function formatDate(
            value
        ) {

            if (
                !value
            ) {

                return '';

            }


            const date =
                new Date(
                    value +
                    'T00:00:00'
                );


            return date.toLocaleDateString(
                'id-ID',
                {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }
            );

        }

    </script>

</x-app-layout>