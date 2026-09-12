<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12 relative">
        <!-- HEADER -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight leading-none">
                                Catalog Hub
                            </h2>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1">
                                Rent & Request Gear
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full md:w-auto flex-1 justify-end">
                        <form
                            action="{{ route('student.dashboard') }}"
                            method="GET"
                            id="searchForm"
                            class="relative group w-full md:w-80"
                        >
                            @if(request('type'))
                                <input type="hidden" name="type" value="{{ request('type') }}">
                            @endif

                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            <input
                                type="text"
                                name="search"
                                id="searchInput"
                                placeholder="Search gear, cameras..."
                                value="{{ request('search') }}"
                                class="block w-full pl-11 pr-4 py-3 bg-gray-100/50 border-transparent rounded-2xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300"
                            >
                        </form>

                        <a
                            href="{{ route('student.cart.index') }}"
                            class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-white uppercase tracking-widest transition-all duration-300 bg-gray-900 rounded-2xl hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-95 whitespace-nowrap overflow-hidden group shrink-0"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Checkout

                            @if(isset($cartCount) && $cartCount > 0)
                                <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-indigo-500 text-[8px] justify-center items-center font-black">
                                        {{ $cartCount }}
                                    </span>
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8">
            @if(session('success'))
                <div class="mb-8 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 1.707 1.732 1.707z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- CATEGORY TABS -->
            <div class="mb-10">
                <div class="flex flex-nowrap overflow-x-auto gap-4 pb-4 -mx-4 px-4 sm:mx-0 sm:px-0 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                    <a
                        href="{{ route('student.dashboard') }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2 {{ !request('type') ? 'bg-gray-900 border-gray-900 text-white shadow-xl shadow-gray-300/50' : 'bg-white border-gray-100 text-gray-500 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-50' }}"
                    >
                        <svg class="w-5 h-5 {{ !request('type') ? 'text-white' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Semua Katalog
                    </a>

                    <a
                        href="{{ route('student.dashboard', ['type' => 'Peralatan']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2 {{ request('type') == 'Peralatan' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-xl shadow-indigo-100/50' : 'bg-white border-gray-100 text-gray-500 hover:border-indigo-100 hover:text-indigo-600 hover:bg-indigo-50/50' }}"
                    >
                        <div class="w-2.5 h-2.5 rounded-full {{ request('type') == 'Peralatan' ? 'bg-indigo-500 animate-pulse' : 'bg-gray-300' }}"></div>
                        Peralatan SC
                    </a>

                    <a
                        href="{{ route('student.dashboard', ['type' => 'HT']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2 {{ request('type') == 'HT' ? 'bg-amber-50 border-amber-200 text-amber-700 shadow-xl shadow-amber-100/50' : 'bg-white border-gray-100 text-gray-500 hover:border-amber-100 hover:text-amber-600 hover:bg-amber-50/50' }}"
                    >
                        <div class="w-2.5 h-2.5 rounded-full {{ request('type') == 'HT' ? 'bg-amber-500 animate-pulse' : 'bg-gray-300' }}"></div>
                        Handy Talkie
                    </a>

                    <a
                        href="{{ route('student.dashboard', ['type' => 'HabisPakai']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2 {{ request('type') == 'HabisPakai' ? 'bg-rose-50 border-rose-200 text-rose-700 shadow-xl shadow-rose-100/50' : 'bg-white border-gray-100 text-gray-500 hover:border-rose-100 hover:text-rose-600 hover:bg-rose-50/50' }}"
                    >
                        <div class="w-2.5 h-2.5 rounded-full {{ request('type') == 'HabisPakai' ? 'bg-rose-500 animate-pulse' : 'bg-gray-300' }}"></div>
                        Habis Pakai (ATK/Obat)
                    </a>

                    <a
                        href="{{ route('student.dashboard', ['type' => 'Merchandise']) }}"
                        class="relative flex items-center gap-3 px-6 py-3.5 rounded-[1.25rem] font-black text-[11px] uppercase tracking-[0.15em] transition-all duration-300 shrink-0 border-2 {{ request('type') == 'Merchandise' ? 'bg-emerald-50 border-emerald-200 text-emerald-700 shadow-xl shadow-emerald-100/50' : 'bg-white border-gray-100 text-gray-500 hover:border-emerald-100 hover:text-emerald-600 hover:bg-emerald-50/50' }}"
                    >
                        <div class="w-2.5 h-2.5 rounded-full {{ request('type') == 'Merchandise' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300' }}"></div>
                        Merchandise
                    </a>
                </div>
            </div>

            <!-- ITEM GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($items as $item)
                    @php
                        $type = $item->transaction_type;

                        $isRental = in_array($type, [
                            'Peralatan',
                            'HT UV-82',
                            'HT 888s',
                            'HT UV-5R',
                            'Internal Rental',
                            'Vendor Rental'
                        ]);

                        $isConsumable = in_array($type, [
                            'ATK',
                            'Obat'
                        ]);

                        $isMerchandise = $type === 'Merchandise';

                        $badgeClass = 'bg-gray-600 text-white';
                        $badgeText = $type;

                        if ($type === 'Peralatan') {
                            $badgeClass = 'bg-indigo-600 text-white';
                        } elseif (in_array($type, ['HT UV-82', 'HT 888s', 'HT UV-5R'])) {
                            $badgeClass = 'bg-amber-500 text-white';
                            $badgeText = 'Handy Talkie';
                        } elseif ($isConsumable) {
                            $badgeClass = 'bg-rose-500 text-white';
                            $badgeText = 'Habis Pakai';
                        } elseif ($isMerchandise) {
                            $badgeClass = 'bg-emerald-500 text-white';
                        }

                        $activeSchedules = collect();

                        if ($isRental) {
                            $activeSchedules = $item->orderItems->filter(function ($detail) {
                                if (!$detail->order) {
                                    return false;
                                }

                                return !in_array($detail->order->status, [
                                    'Returned',
                                    'Resolved (Fine Paid)',
                                    'Rejected',
                                    'Cancelled'
                                ]);
                            });

                            $activeSchedules = $activeSchedules->sortBy(function ($detail) {
                                return $detail->order->start_date
                                    ? $detail->order->start_date->timestamp
                                    : 0;
                            });
                        }

                        $displaySchedules = $activeSchedules->take(2);
                        $remainingCount = max(
                            0,
                            $activeSchedules->count() - $displaySchedules->count()
                        );
                    @endphp

                    <div class="group relative bg-white overflow-hidden shadow-sm sm:rounded-[2rem] flex flex-col border border-gray-100 hover:shadow-2xl hover:shadow-indigo-100/50 transition-all duration-500 transform hover:-translate-y-2">
                        <!-- IMAGE -->
                        <div class="relative bg-gray-50 p-2">
                            @if($item->item_photo)
                                <img
                                    src="{{ asset('storage/' . $item->item_photo) }}"
                                    class="w-full h-48 object-cover rounded-3xl transition-transform duration-700 group-hover:scale-105"
                                    alt="{{ $item->name }}"
                                >
                            @else
                                <div class="w-full h-48 rounded-3xl bg-gray-100 flex items-center justify-center text-gray-300 font-bold text-xs uppercase tracking-widest">
                                    No Image
                                </div>
                            @endif

                            <span class="absolute top-5 left-5 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg {{ $badgeClass }}">
                                {{ $badgeText }}
                            </span>

                            <span class="absolute top-5 right-5 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg backdrop-blur-md {{ $item->condition_status === 'Good' ? 'bg-white/90 text-green-600' : 'bg-red-500/90 text-white' }}">
                                {{ $item->condition_status }}
                            </span>
                        </div>

                        <!-- ITEM DETAILS -->
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="text-lg font-black text-gray-900 leading-tight tracking-tight">
                                            {{ $item->name }}
                                        </h3>

                                        @if($item->subcategory)
                                            <span class="inline-flex mt-2 px-2.5 py-1 bg-gray-100 text-gray-600 text-[8px] font-black rounded-lg uppercase tracking-widest">
                                                {{ $item->subcategory }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mt-1 h-8 line-clamp-2">
                                    {{ $item->description }}
                                </p>
                            </div>

                            <div class="mt-5 flex justify-between items-end border-t border-gray-100 pt-4">
                                <div>
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                        {{ ($isConsumable || $isMerchandise) ? 'Harga' : 'Biaya Sewa' }}
                                    </p>

                                    <span class="text-lg font-black text-indigo-600">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="text-right">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                        Stok Gudang
                                    </p>

                                    <span class="text-sm font-black {{ $item->stock_quantity > 0 ? 'text-gray-900' : 'text-red-500' }}">
                                        {{ $item->stock_quantity }} Unit
                                    </span>
                                </div>
                            </div>

                            <!-- ACTIVE RENTAL SCHEDULES -->
                            @if($isRental && $activeSchedules->count() > 0)
                                <div class="mt-4 p-3 bg-orange-50/80 border border-orange-100 rounded-2xl">
                                    <p class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Sedang Ter-Booking:
                                    </p>

                                    <ul class="text-[10px] text-orange-700 space-y-1.5">
                                        @foreach($displaySchedules as $detail)
                                            <li class="bg-white px-2.5 py-2 rounded-xl border border-orange-50 shadow-sm">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold">
                                                        {{ optional($detail->order->start_date)->format('d M Y') }}
                                                        -
                                                        {{ optional($detail->order->end_date)->format('d M Y') }}
                                                    </span>

                                                    <span class="font-black bg-orange-100 text-orange-600 px-2 py-1 rounded-lg text-[9px]">
                                                        {{ $detail->quantity }} Unit
                                                    </span>
                                                </div>

                                                <div class="flex justify-between mt-1 text-[9px] text-orange-500 font-bold">
                                                    <span>
                                                        {{ $detail->order->start_time ?? '--:--' }}
                                                    </span>

                                                    <span>
                                                        {{ $detail->order->end_time ?? '--:--' }}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    @if($remainingCount > 0)
                                        <div class="mt-2 text-center bg-orange-100/50 py-1.5 rounded-lg border border-orange-100 border-dashed">
                                            <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest">
                                                + {{ $remainingCount }} Jadwal Lainnya
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- ACTION AREA -->
                        <div
                            x-data="{ showDateForm: false }"
                            class="p-4 bg-white border-t border-gray-50 flex flex-col gap-2.5 transition-all duration-300"
                        >
                            @if($item->stock_quantity > 0)
                                @if($isRental)
                                    <a
                                        href="{{ route('student.item.schedule', $item->id) }}"
                                        class="w-full inline-flex justify-center items-center py-2.5 bg-indigo-50/50 text-indigo-600 border border-indigo-100 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white transition-all active:scale-95 group"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002-2z"/>
                                        </svg>
                                        Lihat Siapa Saja Yang Booking
                                    </a>
                                @endif

                                <button
                                    type="button"
                                    x-show="!showDateForm"
                                    @click="showDateForm = true"
                                    class="w-full text-center bg-gray-900 text-white py-3.5 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all duration-300 shadow-lg shadow-gray-200 active:scale-95 group flex justify-center items-center"
                                >
                                    <svg class="w-4 h-4 mr-2 group-hover:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $isRental ? 'Pilih Jadwal Sewa' : 'Pilih Jadwal Transaksi' }}
                                </button>

                                <!-- SCHEDULE FORM -->
                                <form
                                    action="{{ route('student.cart.add', $item->id) }}"
                                    method="POST"
                                    x-show="showDateForm"
                                    x-transition
                                    style="display: none;"
                                    id="scheduleForm_{{ $item->id }}"
                                    data-item-id="{{ $item->id }}"
                                    data-is-rental="{{ $isRental ? '1' : '0' }}"
                                    class="flex flex-col gap-3 mt-2"
                                >
                                    @csrf

                                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 relative shadow-inner">
                                        <button
                                            type="button"
                                            @click="showDateForm = false"
                                            class="absolute top-2 right-2 bg-white border border-gray-200 rounded-full p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors shadow-sm z-10"
                                        >
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>

                                        <label class="block text-[10px] font-black uppercase text-indigo-800 tracking-widest mb-3 mt-1 text-center">
                                            {{ $isRental ? 'Pilih Rentang Jadwal' : 'Pilih Jadwal Transaksi' }}
                                        </label>

                                        @if($isRental)
                                            <div
                                                id="loading_{{ $item->id }}"
                                                class="w-full py-8 flex flex-col items-center justify-center gap-3"
                                            >
                                                <svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>

                                                <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">
                                                    Mengecek Stok...
                                                </span>
                                            </div>
                                        @endif

                                        <div class="w-full flex justify-center">
                                            <input
                                                type="text"
                                                id="date_picker_{{ $item->id }}"
                                                class="{{ $isRental ? 'hidden' : 'w-full' }}"
                                                placeholder="Pilih tanggal"
                                                readonly
                                            >
                                        </div>

                                        <div
                                            id="selected_display_{{ $item->id }}"
                                            class="w-full mt-3 py-2.5 px-3 bg-white border border-indigo-100 rounded-xl text-center text-[10px] font-bold text-indigo-600 shadow-sm hidden"
                                        ></div>

                                        <input
                                            type="hidden"
                                            name="start_date"
                                            id="start_date_{{ $item->id }}"
                                            required
                                        >

                                        @if($isRental)
                                            <input
                                                type="hidden"
                                                name="end_date"
                                                id="end_date_{{ $item->id }}"
                                                required
                                            >
                                        @else
                                            <input
                                                type="hidden"
                                                name="end_date"
                                                id="end_date_{{ $item->id }}"
                                            >
                                        @endif

                                        <div class="grid grid-cols-1 {{ $isRental ? 'md:grid-cols-2' : '' }} gap-3 mt-3">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1.5">
                                                    Jam {{ $isRental ? 'Pengambilan' : 'Transaksi' }}
                                                </label>

                                                <select
                                                    name="start_time"
                                                    id="start_time_{{ $item->id }}"
                                                    required
                                                    class="w-full px-3 py-3 bg-white border border-gray-200 rounded-xl text-xs font-black text-gray-900 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm"
                                                >
                                                    <option value="">Pilih Jam</option>
                                                    <option value="17:00">17:00</option>
                                                    <option value="17:30">17:30</option>
                                                    <option value="18:00">18:00</option>
                                                    <option value="18:30">18:30</option>
                                                    <option value="19:00">19:00</option>
                                                </select>
                                            </div>

                                            @if($isRental)
                                                <div>
                                                    <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1.5">
                                                        Jam Pengembalian
                                                    </label>

                                                    <select
                                                        name="end_time"
                                                        id="end_time_{{ $item->id }}"
                                                        required
                                                        class="w-full px-3 py-3 bg-white border border-gray-200 rounded-xl text-xs font-black text-gray-900 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm"
                                                    >
                                                        <option value="">Pilih Jam</option>
                                                        <option value="17:00">17:00</option>
                                                        <option value="17:30">17:30</option>
                                                        <option value="18:00">18:00</option>
                                                        <option value="18:30">18:30</option>
                                                        <option value="19:00">19:00</option>
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        <p class="text-[9px] text-indigo-600 font-bold mt-3 text-center">
                                            Jam transaksi hanya tersedia pukul 17:00–19:00.
                                        </p>
                                    </div>

                                    <div class="flex gap-2">
                                        <div class="relative w-20 shrink-0">
                                            <span class="absolute -top-2 left-2 bg-white px-1 text-[8px] font-black text-gray-400 uppercase tracking-widest z-10">
                                                Jumlah
                                            </span>

                                            <input
                                                type="number"
                                                name="quantity"
                                                value="1"
                                                min="1"
                                                max="{{ $item->stock_quantity }}"
                                                required
                                                class="w-full h-full px-2 py-3 bg-white border border-gray-200 rounded-xl text-xs font-black text-center text-gray-900 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm pt-3"
                                            >
                                        </div>

                                        <button
                                            type="submit"
                                            class="flex-grow text-center {{ $isMerchandise ? 'bg-emerald-600 hover:bg-gray-900 shadow-emerald-200' : ($isConsumable ? 'bg-rose-600 hover:bg-gray-900 shadow-rose-200' : 'bg-indigo-600 hover:bg-gray-900 shadow-indigo-200') }} text-white py-3.5 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all duration-300 shadow-lg active:scale-95 group"
                                        >
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Ke Keranjang
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <button
                                    disabled
                                    class="w-full bg-gray-100 text-gray-400 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] cursor-not-allowed border border-gray-200"
                                >
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION -->
            <div class="mt-12 flex justify-center">
                <div class="bg-white px-2 py-1 rounded-2xl shadow-sm border border-gray-100">
                    {{ $items->links() }}
                </div>
            </div>

            <!-- EMPTY STATE -->
            @if($items->isEmpty())
                <div class="text-center py-32">
                    <div class="w-24 h-24 bg-white border border-gray-100 shadow-sm rounded-[2rem] rotate-12 flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg class="w-12 h-12 -rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <p class="text-gray-400 font-black uppercase text-[11px] tracking-[0.3em] mb-4">
                        No items matching your search.
                    </p>

                    <a
                        href="{{ route('student.dashboard') }}"
                        class="px-6 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-all shadow-sm"
                    >
                        Clear Search
                    </a>
                </div>
            @endif
        </div>
    </div>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    >

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        .flatpickr-calendar.inline {
            box-shadow: none !important;
            border: none !important;
            background: transparent !important;
            width: 100% !important;
            max-width: 280px !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }

        .flatpickr-days,
        .dayContainer {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        .flatpickr-day {
            height: 46px !important;
            max-width: none !important;
            width: calc(14.28% - 4px) !important;
            margin: 2px !important;
            line-height: 1.2 !important;
            border-radius: 10px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            border: 2px solid transparent !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
        }

        .flatpickr-day.inRange {
            background: #e0e7ff !important;
            border-color: #e0e7ff !important;
            color: #3730a3 !important;
            box-shadow: none !important;
        }

        .flatpickr-day:hover {
            background: #f3f4f6 !important;
            border-color: #e5e7eb !important;
        }

        .stock-badge {
            font-size: 8.5px;
            margin-top: 3px;
            font-weight: 900;
            letter-spacing: 0.05em;
        }

        .stock-green {
            color: #10B981;
        }

        .stock-red {
            color: #EF4444;
            text-decoration: line-through;
        }

        .flatpickr-day.selected .stock-green,
        .flatpickr-day.startRange .stock-green,
        .flatpickr-day.endRange .stock-green {
            color: #c7d2fe !important;
        }
    </style>

    <script>
        let searchTimeout = null;

        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        if (searchInput && searchForm) {
            searchInput.addEventListener('keyup', function () {
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(() => {
                    searchForm.submit();
                }, 700);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const scheduleForms = document.querySelectorAll('form[data-item-id]');

            scheduleForms.forEach(function (form) {
                const itemId = form.dataset.itemId;
                const isRental = form.dataset.isRental === '1';

                const datePicker = document.getElementById('date_picker_' + itemId);
                const startDateInput = document.getElementById('start_date_' + itemId);
                const endDateInput = document.getElementById('end_date_' + itemId);
                const startTimeInput = document.getElementById('start_time_' + itemId);
                const endTimeInput = document.getElementById('end_time_' + itemId);
                const displayDiv = document.getElementById('selected_display_' + itemId);
                const loadingDiv = document.getElementById('loading_' + itemId);

                function updateDisplay() {
                    if (!startDateInput.value || !startTimeInput.value) {
                        return;
                    }

                    const startDate = new Date(
                        startDateInput.value + 'T00:00:00'
                    );

                    const startText =
                        startDate.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) +
                        ' · ' +
                        startTimeInput.value;

                    if (!isRental) {
                        displayDiv.classList.remove('hidden');
                        displayDiv.innerHTML = '📅 ' + startText;
                        return;
                    }

                    if (
                        !endDateInput.value ||
                        !endTimeInput.value
                    ) {
                        displayDiv.classList.remove('hidden');
                        displayDiv.innerHTML =
                            '📅 ' +
                            startText +
                            ' · Pilih pengembalian';

                        return;
                    }

                    const endDate = new Date(
                        endDateInput.value + 'T00:00:00'
                    );

                    const endText =
                        endDate.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) +
                        ' · ' +
                        endTimeInput.value;

                    displayDiv.classList.remove('hidden');

                    displayDiv.innerHTML =
                        '📦 ' +
                        startText +
                        ' → ' +
                        endText;
                }

                function validateSchedule() {
                    if (
                        !startDateInput.value ||
                        !startTimeInput.value
                    ) {
                        return false;
                    }

                    if (!isRental) {
                        return true;
                    }

                    if (
                        !endDateInput.value ||
                        !endTimeInput.value
                    ) {
                        return false;
                    }

                    const startDateTime = new Date(
                        startDateInput.value +
                        'T' +
                        startTimeInput.value +
                        ':00'
                    );

                    const endDateTime = new Date(
                        endDateInput.value +
                        'T' +
                        endTimeInput.value +
                        ':00'
                    );

                    if (endDateTime <= startDateTime) {
                        alert(
                            'Waktu pengembalian harus setelah waktu pengambilan.'
                        );

                        return false;
                    }

                    return true;
                }

                if (startTimeInput) {
                    startTimeInput.addEventListener(
                        'change',
                        updateDisplay
                    );
                }

                if (endTimeInput) {
                    endTimeInput.addEventListener(
                        'change',
                        updateDisplay
                    );
                }

                if (isRental) {
                    fetch('/api/check-stock/' + itemId)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Stock API failed.');
                            }

                            return response.json();
                        })
                        .then(stockData => {
                            if (loadingDiv) {
                                loadingDiv.classList.add('hidden');
                            }

                            flatpickr(datePicker, {
                                mode: 'range',
                                minDate: 'today',
                                dateFormat: 'Y-m-d',
                                showMonths: 1,
                                inline: true,

                                onDayCreate: function (
                                    dObj,
                                    dStr,
                                    fp,
                                    dayElem
                                ) {
                                    const dateString =
                                        fp.formatDate(
                                            dayElem.dateObj,
                                            'Y-m-d'
                                        );

                                    if (
                                        stockData[dateString] !== undefined
                                    ) {
                                        const remainingStock =
                                            stockData[dateString];

                                        const stockClass =
                                            remainingStock > 0
                                                ? 'stock-green'
                                                : 'stock-red';

                                        const stockText =
                                            remainingStock > 0
                                                ? `Sisa ${remainingStock}`
                                                : 'Habis';

                                        dayElem.innerHTML +=
                                            `<span class="stock-badge ${stockClass}">${stockText}</span>`;
                                    }
                                },

                                onChange: function (
                                    selectedDates,
                                    dateStr,
                                    instance
                                ) {
                                    if (
                                        selectedDates.length === 0
                                    ) {
                                        return;
                                    }

                                    startDateInput.value =
                                        instance.formatDate(
                                            selectedDates[0],
                                            'Y-m-d'
                                        );

                                    if (
                                        selectedDates.length === 2
                                    ) {
                                        endDateInput.value =
                                            instance.formatDate(
                                                selectedDates[1],
                                                'Y-m-d'
                                            );
                                    } else {
                                        endDateInput.value =
                                            instance.formatDate(
                                                selectedDates[0],
                                                'Y-m-d'
                                            );
                                    }

                                    updateDisplay();
                                }
                            });
                        })
                        .catch(error => {
                            console.error(
                                'Stock API Error:',
                                error
                            );

                            if (loadingDiv) {
                                loadingDiv.innerHTML =
                                    '<span class="text-red-500 font-bold text-[10px]">Gagal memuat jadwal. Coba lagi.</span>';
                            }
                        });
                } else {
                    flatpickr(datePicker, {
                        mode: 'single',
                        minDate: 'today',
                        dateFormat: 'Y-m-d',
                        inline: true,

                        onChange: function (
                            selectedDates,
                            dateStr,
                            instance
                        ) {
                            if (
                                selectedDates.length === 0
                            ) {
                                return;
                            }

                            startDateInput.value =
                                instance.formatDate(
                                    selectedDates[0],
                                    'Y-m-d'
                                );

                            endDateInput.value = '';

                            updateDisplay();
                        }
                    });
                }

                form.addEventListener(
                    'submit',
                    function (event) {
                        if (
                            !startDateInput.value ||
                            !startTimeInput.value
                        ) {
                            event.preventDefault();

                            alert(
                                'Silakan pilih tanggal dan jam transaksi terlebih dahulu.'
                            );

                            return;
                        }

                        if (isRental) {
                            if (
                                !endDateInput.value ||
                                !endTimeInput.value
                            ) {
                                event.preventDefault();

                                alert(
                                    'Silakan pilih tanggal dan jam pengembalian terlebih dahulu.'
                                );

                                return;
                            }

                            if (!validateSchedule()) {
                                event.preventDefault();
                            }
                        }
                    }
                );
            });
        });
    </script>
</x-app-layout>