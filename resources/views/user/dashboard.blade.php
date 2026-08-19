<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12 relative">
        
        <!-- 🌟 SLEEK HEADER STICKY 🌟 -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    
                    <!-- Title Area -->
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight leading-none">Catalog Hub</h2>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1">Rent & Request Gear</p>
                        </div>
                    </div>

                    <!-- Search & Cart Action -->
                    <div class="flex items-center gap-4 w-full md:w-auto flex-1 justify-end">
                        <form action="{{ route('student.dashboard') }}" method="GET" id="searchForm" class="relative group w-full md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" id="searchInput" placeholder="Search gear, cameras..." value="{{ request('search') }}" 
                                class="block w-full pl-11 pr-4 py-3 bg-gray-100/50 border-transparent rounded-2xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300">
                        </form>
                        
                        <!-- TOMBOL MENUJU KERANJANG -->
                        <a href="{{ route('student.cart.index') }}" class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-white uppercase tracking-widest transition-all duration-300 bg-gray-900 rounded-2xl hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-95 whitespace-nowrap overflow-hidden group shrink-0">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Checkout
                            @if($cartCount > 0)
                                <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-indigo-500 text-[8px] justify-center items-center font-black">{{ $cartCount }}</span>
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8">
            
            <!-- Notifikasi Sukses -->
            @if(session('success'))
                <div class="mb-8 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Notifikasi Error -->
            @if(session('error'))
                <div class="mb-8 px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- 🗃️ GRID BARANG -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($items as $item)
                <div class="group relative bg-white overflow-hidden shadow-sm sm:rounded-[2rem] flex flex-col border border-gray-100 hover:shadow-2xl hover:shadow-indigo-100/50 transition-all duration-500 transform hover:-translate-y-2">
                    
                    <!-- Area Foto -->
                    <div class="relative bg-gray-50 p-2">
                        @if($item->item_photo)
                            <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-48 object-cover rounded-3xl transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-48 rounded-3xl bg-gray-100 flex items-center justify-center text-gray-300 font-bold text-xs uppercase tracking-widest">No Image</div>
                        @endif
                        
                        <!-- Badge Tipe Transaksi (Kiri) -->
                        <span class="absolute top-5 left-5 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg
                            {{ $item->transaction_type === 'Internal Rental' ? 'bg-indigo-600 text-white' : '' }}
                            {{ $item->transaction_type === 'Vendor Rental' ? 'bg-amber-500 text-white' : '' }}
                            {{ $item->transaction_type === 'Sale' ? 'bg-emerald-500 text-white' : '' }}">
                            {{ $item->transaction_type === 'Internal Rental' ? 'Internal' : ($item->transaction_type === 'Vendor Rental' ? 'External' : 'Merch') }}
                        </span>

                        <!-- Badge Kondisi (Kanan) -->
                        <span class="absolute top-5 right-5 px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg backdrop-blur-md {{ $item->condition_status === 'Good' ? 'bg-white/90 text-green-600' : 'bg-red-500/90 text-white' }}">
                            {{ $item->condition_status }}
                        </span>
                    </div>

                    <!-- Detail Konten -->
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-black text-gray-900 leading-tight tracking-tight">{{ $item->name }}</h3>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 h-8 line-clamp-2">{{ $item->description }}</p>
                        </div>
                        
                        <div class="mt-5 flex justify-between items-end border-t border-gray-100 pt-4">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ $item->transaction_type === 'Sale' ? 'Harga' : 'Biaya Sewa' }}</p>
                                <span class="text-lg font-black text-indigo-600">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Stok Total Aset</p>
                                <span class="text-sm font-black {{ $item->stock_quantity > 0 ? 'text-gray-900' : 'text-red-500' }}">{{ $item->stock_quantity }}</span>
                            </div>
                        </div>

                        <!-- 🌟 📅 JADWAL TERPAKAI (Diperbaiki agar hanya menampilkan status AKTIF) 🌟 -->
                    
@if($item->transaction_type !== 'Sale')
    @php
        $activeSchedules = $item->orderItems->filter(function($detail) {
            return $detail->order && !in_array($detail->order->status, ['Returned', 'Resolved (Fine Paid)', 'Rejected', 'Cancelled']) && $detail->order->order_type !== 'Sale';
        })->sortBy(function($detail) {
            return \Carbon\Carbon::parse($detail->order->start_date)->timestamp;
        });
        
        $displaySchedules = $activeSchedules->take(2); // Ambil 2 terdekat
        $remainingCount = $activeSchedules->count() - 2; // Hitung sisanya
    @endphp
    
    @if($activeSchedules->count() > 0)
        <div class="mt-4 p-3 bg-orange-50/80 border border-orange-100 rounded-2xl">
            <p class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-2 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Sedang Ter-Booking:
            </p>
            <ul class="text-[10px] text-orange-700 space-y-1.5">
                @foreach($displaySchedules as $detail)
                    <li class="flex justify-between items-center bg-white px-2.5 py-2 rounded-xl border border-orange-50 shadow-sm">
                        <span class="font-bold">{{ \Carbon\Carbon::parse($detail->order->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($detail->order->end_date)->format('d M') }}</span>
                        <span class="font-black bg-orange-100 text-orange-600 px-2 py-1 rounded-lg text-[9px]">{{ $detail->quantity }} Unit</span>
                    </li>
                @endforeach
            </ul>
            
            <!-- Jika jadwal lebih dari 2 -->
            @if($remainingCount > 0)
                <div class="mt-2 text-center bg-orange-100/50 py-1.5 rounded-lg border border-orange-100 border-dashed">
                    <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest">+ {{ $remainingCount }} Jadwal Lainnya</p>
                </div>
            @endif
        </div>
    @endif
@endif

                    </div>

                    <!-- 🌟 AREA ACTION: TOMBOL CEK JADWAL & ADD TO CART 🌟 -->
                    <div class="p-4 bg-white border-t border-gray-50 flex flex-col gap-2.5">
                        
                        <!-- TOMBOL CEK JADWAL LENGKAP (HANYA MUNCUL UNTUK BARANG SEWAAN) -->
                        @if($item->transaction_type !== 'Sale')
                            <a href="{{ route('student.item.schedule', $item->id) }}" class="w-full inline-flex justify-center items-center py-2.5 bg-indigo-50/50 text-indigo-600 border border-indigo-100 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white transition-all active:scale-95 group">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Lihat Jadwal Detail
                            </a>
                        @endif

                        @if($item->stock_quantity > 0)
                            <form action="{{ route('student.cart.add', $item->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                
                                <!-- KOTAK INPUT QUANTITY -->
                                <input type="number" name="quantity" value="1" min="1" max="{{ $item->stock_quantity }}" required
                                    class="w-16 px-2 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-black text-center text-gray-900 focus:ring-2 focus:ring-indigo-500 outline-none shadow-inner"
                                    title="Jumlah Barang">

                                <button type="submit" class="flex-grow text-center bg-gray-900 text-white py-3.5 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all duration-300 shadow-lg shadow-gray-200 active:scale-95 group">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add to Cart
                                    </span>
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-100 text-gray-400 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] cursor-not-allowed border border-gray-200">
                                Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <div class="bg-white px-2 py-1 rounded-2xl shadow-sm border border-gray-100">
                    {{ $items->links() }}
                </div>
            </div>

            <!-- Kondisi Kosong (Empty State) -->
            @if($items->isEmpty())
                <div class="text-center py-32">
                    <div class="w-24 h-24 bg-white border border-gray-100 shadow-sm rounded-[2rem] rotate-12 flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg class="w-12 h-12 -rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <p class="text-gray-400 font-black uppercase text-[11px] tracking-[0.3em] mb-4">No items matching your search, mon cher.</p>
                    <a href="{{ route('student.dashboard') }}" class="px-6 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-all shadow-sm">
                        Clear Search
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Script Autocomplete Search -->
    <script>
        let timeout = null;
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 700); // Tunggu 0.7 detik setelah ngetik
        });
    </script>
</x-app-layout>