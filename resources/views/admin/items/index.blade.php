<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                        {{ __('Asset Masterlist') }}
                    </h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-1 italic">Total Control Center — Admin Exclusive</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 w-full md:w-auto">
                <form action="{{ route('admin.items.index') }}" method="GET" class="relative flex-grow md:w-72">
                    <input type="text" name="search" placeholder="Search inventory..." 
                        class="pl-12 pr-4 py-3 bg-white border-none rounded-2xl text-xs font-bold text-gray-600 focus:ring-2 focus:ring-indigo-500 shadow-sm w-full transition-all"
                        value="{{ request('search') }}">
                    <div class="absolute left-4 top-3.5 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <a href="{{ route('admin.items.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-100 shrink-0 active:scale-95 flex items-center group">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Asset
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[2rem] border border-gray-50 shadow-sm">
                    <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest mb-1">Total Assets</p>
                    <h4 class="text-2xl font-black text-gray-900">{{ $items->total() }}</h4>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-50 shadow-sm">
                    <p class="text-[9px] font-black uppercase text-green-500 tracking-widest mb-1">Good Condition</p>
                    <h4 class="text-2xl font-black text-gray-900">{{ $items->where('condition_status', 'Good')->count() }}</h4>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-50 shadow-sm text-gray-400">
                    <p class="text-[9px] font-black uppercase tracking-widest mb-1 italic italic">Page Index</p>
                    <h4 class="text-xl font-black">{{ $items->currentPage() }} / {{ $items->lastPage() }}</h4>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-[10px] font-black uppercase shadow-sm rounded-r-xl animate-pulse">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($items as $item)
                <div class="group bg-white rounded-[2.5rem] border border-gray-50 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden flex flex-col">
                    
                    <div class="relative h-56 overflow-hidden p-3">
                        <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-full object-cover rounded-[2rem] group-hover:scale-110 transition duration-1000">
                        
                        <div class="absolute top-6 left-6">
                            <span class="px-3 py-1 text-[8px] font-black uppercase rounded-full shadow-lg backdrop-blur-md {{ $item->condition_status === 'Good' ? 'bg-green-500/90 text-white' : 'bg-red-500/90 text-white' }}">
                                {{ $item->condition_status }}
                            </span>
                        </div>
                    </div>

                    <div class="px-8 py-6 flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-md font-black text-gray-900 leading-tight group-hover:text-indigo-600 transition truncate pr-2">
                                {{ $item->name }}
                            </h3>
                            <span class="text-[9px] font-bold text-gray-300 uppercase">#{{ $item->id }}</span>
                        </div>
                        
                        <div class="flex items-end justify-between mt-4">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Rental Rate</p>
                                <p class="text-lg font-black text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Available</p>
                                <p class="text-sm font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg">{{ $item->stock_quantity }} Unit</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50/50 flex gap-2">
                        <a href="{{ route('admin.items.edit', $item) }}" class="flex-1 flex items-center justify-center bg-white border border-gray-100 text-gray-500 py-3 rounded-2xl text-[9px] font-black uppercase hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                            Modify
                        </a>
                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="shrink-0" onsubmit="return confirm('Est-ce que tu es sûr, mon cher?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-12 flex items-center justify-center bg-red-50 text-red-400 py-3 rounded-2xl text-[9px] font-black uppercase hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-16 flex justify-center">
                <div class="bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-50">
                    {{ $items->links() }}
                </div>
            </div>

            @if($items->isEmpty())
                <div class="text-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-gray-400 font-black uppercase text-[10px] tracking-widest italic">The inventory is a blank canvas, mon cher.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>