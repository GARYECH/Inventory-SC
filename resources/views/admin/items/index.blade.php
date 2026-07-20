<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Vault & Inventory</h1>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1.5">SC Centralized Database</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <form action="{{ route('admin.items.index') }}" method="GET" class="relative group w-full md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" placeholder="Search parameters..." value="{{ request('search') }}" 
                                class="block w-full pl-11 pr-4 py-3 bg-gray-100/50 border-transparent rounded-2xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300">
                        </form>
                        <a href="{{ route('admin.items.create') }}" class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-white uppercase tracking-widest transition-all duration-300 bg-gray-900 rounded-2xl hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-500/30 active:scale-95 whitespace-nowrap overflow-hidden group shrink-0">
                            Add Entry
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-10">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest mb-1">Total Assets</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ $items->total() }}</h4>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black uppercase text-indigo-500 tracking-widest mb-1">Internal</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ $items->where('transaction_type', 'Internal Rental')->count() }}</h4>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black uppercase text-amber-500 tracking-widest mb-1">External</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ $items->where('transaction_type', 'Vendor Rental')->count() }}</h4>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[9px] font-black uppercase text-emerald-500 tracking-widest mb-1">Merchandise</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ $items->where('transaction_type', 'Sale')->count() }}</h4>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($items as $item)
                <div class="group bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden flex flex-col">
                    <div class="relative h-60 overflow-hidden bg-gray-50 p-2">
                        <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-full object-cover rounded-3xl">
                        <div class="absolute top-5 left-5">
                            <span class="inline-flex px-3 py-1.5 text-[8px] font-black uppercase tracking-widest rounded-xl shadow-lg 
                                {{ $item->transaction_type == 'Internal Rental' ? 'bg-indigo-600 text-white' : ($item->transaction_type == 'Vendor Rental' ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white') }}">
                                {{ $item->transaction_type == 'Internal Rental' ? 'Internal' : ($item->transaction_type == 'Vendor Rental' ? 'External' : 'Merchandise') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-black text-gray-900 truncate">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 mt-2">{{ $item->description }}</p>
                        <div class="mt-5 pt-5 border-t border-gray-100 flex justify-between items-center">
                            <p class="text-lg font-black text-indigo-600">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            <p class="text-sm font-black">{{ $item->stock_quantity }} Unit</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>