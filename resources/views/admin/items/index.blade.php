<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        
        <!-- 🌟 SLEEK HEADER STICKY 🌟 -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    
                    <!-- Title Area -->
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Vault & Inventory</h1>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1.5">SC Centralized Database</p>
                        </div>
                    </div>

                    <!-- Search & Action Area -->
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <form action="{{ route('admin.items.index') }}" method="GET" class="relative group w-full md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" placeholder="Search parameters..." value="{{ request('search') }}" 
                                class="block w-full pl-11 pr-4 py-3 bg-gray-100/50 border-transparent rounded-2xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300">
                        </form>
                        
                        <a href="{{ route('admin.items.create') }}" class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-white uppercase tracking-widest transition-all duration-300 bg-gray-900 rounded-2xl hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-95 whitespace-nowrap overflow-hidden group shrink-0">
                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                            Add Entry
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            
            <!-- Alert Notification -->
            @if(session('success'))
                <div class="mb-8 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- 📊 STATS CARDS (INTERACTIVE FILTERS) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-10">
                
                <!-- ALL ASSETS CARD -->
                <a href="{{ route('admin.items.index') }}" 
                   class="block bg-white p-6 rounded-3xl border {{ !request('type') ? 'ring-4 ring-gray-900 border-transparent shadow-xl scale-105' : 'border-gray-100 shadow-sm hover:scale-105 hover:shadow-md' }} relative overflow-hidden group transition-all duration-300 cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-gray-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-widest mb-1 {{ !request('type') ? 'text-gray-500' : 'text-gray-400' }}">Total Database</p>
                        <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ $counts['total'] ?? 0 }}</h4>
                    </div>
                </a>

                <!-- INTERNAL CARD -->
                <a href="{{ route('admin.items.index', ['type' => 'Internal Rental']) }}" 
                   class="block bg-white p-6 rounded-3xl border {{ request('type') == 'Internal Rental' ? 'ring-4 ring-indigo-500 border-transparent shadow-xl shadow-indigo-200 scale-105' : 'border-gray-100 shadow-sm hover:scale-105 hover:shadow-md' }} relative overflow-hidden group transition-all duration-300 cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-widest mb-1 {{ request('type') == 'Internal Rental' ? 'text-indigo-600' : 'text-indigo-500' }}">Internal</p>
                        <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ $counts['internal'] ?? 0 }}</h4>
                    </div>
                </a>

                <!-- EXTERNAL CARD -->
                <a href="{{ route('admin.items.index', ['type' => 'Vendor Rental']) }}" 
                   class="block bg-white p-6 rounded-3xl border {{ request('type') == 'Vendor Rental' ? 'ring-4 ring-amber-500 border-transparent shadow-xl shadow-amber-200 scale-105' : 'border-gray-100 shadow-sm hover:scale-105 hover:shadow-md' }} relative overflow-hidden group transition-all duration-300 cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-widest mb-1 {{ request('type') == 'Vendor Rental' ? 'text-amber-600' : 'text-amber-500' }}">External</p>
                        <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ $counts['external'] ?? 0 }}</h4>
                    </div>
                </a>

                <!-- MERCHANDISE CARD -->
                <a href="{{ route('admin.items.index', ['type' => 'Sale']) }}" 
                   class="block bg-white p-6 rounded-3xl border {{ request('type') == 'Sale' ? 'ring-4 ring-emerald-500 border-transparent shadow-xl shadow-emerald-200 scale-105' : 'border-gray-100 shadow-sm hover:scale-105 hover:shadow-md' }} relative overflow-hidden group transition-all duration-300 cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-widest mb-1 {{ request('type') == 'Sale' ? 'text-emerald-600' : 'text-emerald-500' }}">Merchandise</p>
                        <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ $counts['merchandise'] ?? 0 }}</h4>
                    </div>
                </a>
                
            </div>

            <!-- 🗃️ ASSET GRID (THE MASTERPIECE) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                @foreach($items as $item)
                <div class="group relative bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-indigo-100/50 hover:-translate-y-2 transition-all duration-500 flex flex-col overflow-hidden">
                    
                    <!-- Image Section with Overlays -->
                    <div class="relative h-60 overflow-hidden bg-gray-50 p-2">
                        @if($item->item_photo)
                            <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-full object-cover rounded-3xl transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full rounded-3xl bg-gray-100 flex items-center justify-center text-gray-300 font-bold text-xs uppercase tracking-widest">No Image</div>
                        @endif
                        
                        <!-- Badges (Top Left) -->
                        <div class="absolute top-5 left-5 flex flex-col gap-2 z-10">
                            <!-- Type Badge -->
                            <span class="inline-flex px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg
                                {{ $item->transaction_type === 'Internal Rental' ? 'bg-indigo-600 text-white' : '' }}
                                {{ $item->transaction_type === 'Vendor Rental' ? 'bg-amber-500 text-white' : '' }}
                                {{ $item->transaction_type === 'Sale' ? 'bg-emerald-500 text-white' : '' }}">
                                {{ $item->transaction_type === 'Internal Rental' ? 'Internal' : ($item->transaction_type === 'Vendor Rental' ? 'External' : 'Merchandise') }}
                            </span>
                        </div>
                        
                        <!-- Hover Actions (Glassmorphism Slide-Up) -->
                        <div class="absolute inset-x-0 bottom-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out flex gap-2 z-20">
                            <a href="{{ route('admin.items.edit', $item) }}" class="flex-1 bg-white/90 backdrop-blur-sm border border-white/50 text-gray-900 text-[10px] font-black uppercase tracking-widest py-3 rounded-2xl text-center hover:bg-indigo-600 hover:text-white transition-all shadow-lg flex items-center justify-center">
                                Modify
                            </a>
                            <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="shrink-0 h-full" onsubmit="return confirm('Sistem: Yakin ingin menghapus aset ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-12 h-[38px] bg-red-500/90 backdrop-blur-sm border border-red-400/50 text-white rounded-2xl flex items-center justify-center hover:bg-red-600 transition-all shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-black text-gray-900 leading-tight truncate pr-4">{{ $item->name }}</h3>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2 h-8">{{ $item->description }}</p>
                        </div>
                        
                        <div class="mt-5 flex items-end justify-between border-t border-gray-100 pt-5">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ $item->transaction_type === 'Sale' ? 'Price' : 'Rental Rate' }}</p>
                                <p class="text-lg font-black text-indigo-600">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Available Stock</p>
                                <p class="text-sm font-black {{ $item->stock_quantity > 0 ? 'text-gray-900' : 'text-red-500' }}">{{ $item->stock_quantity }} Unit</p>
                            </div>
                        </div>
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

            <!-- Empty State -->
            @if($items->isEmpty())
                <div class="text-center py-32">
                    <div class="w-24 h-24 bg-white border border-gray-100 shadow-sm rounded-[2rem] rotate-12 flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg class="w-12 h-12 -rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-gray-400 font-black uppercase text-[11px] tracking-[0.3em]">The Inventory is a Blank Canvas, Mon Cher.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>