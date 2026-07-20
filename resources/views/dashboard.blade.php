<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Council Inventory') }}
            </h2>

            <a href="{{ route('student.cart.index') }}" class="relative inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                View Cart
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-center mb-8 border-b border-gray-200">
                <a href="{{ route('student.dashboard', ['type' => 'Internal Rental']) }}" 
                   class="px-6 py-3 font-bold text-sm uppercase {{ $type === 'Internal Rental' ? 'text-indigo-600 border-b-4 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                   Internal SC Equipments
                </a>
                <a href="{{ route('student.dashboard', ['type' => 'Vendor Rental']) }}" 
                   class="px-6 py-3 font-bold text-sm uppercase {{ $type === 'Vendor Rental' ? 'text-indigo-600 border-b-4 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                   Vendor Equipments (HT/Tenda)
                </a>
                <a href="{{ route('student.dashboard', ['type' => 'Sale']) }}" 
                   class="px-6 py-3 font-bold text-sm uppercase {{ $type === 'Sale' ? 'text-indigo-600 border-b-4 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                   Merchandise (Buy)
                </a>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm border-l-4 border-red-500 font-bold">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-l-4 border-green-500">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('student.dashboard') }}" method="GET" id="searchForm" class="mb-6 relative w-full md:w-1/2 mx-auto">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="text" name="search" id="searchInput" placeholder="Search {{ $type }}..." 
                    class="w-full pl-10 pr-4 py-3 border-gray-200 rounded-xl focus:ring-indigo-500 shadow-sm text-sm"
                    value="{{ request('search') }}">
                <div class="absolute left-4 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($items as $item)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl flex flex-col border border-gray-100 hover:shadow-lg transition-all duration-300">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-48 object-cover">
                    </div>

                    <div class="p-4 flex-grow">
                        <h3 class="text-md font-black text-gray-900 tracking-tight">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1 h-8">{{ Str::limit($item->description, 55) }}</p>
                        
                        <div class="mt-4 flex justify-between items-center text-sm">
                            <span class="font-black text-indigo-600">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-gray-400 text-[10px] font-bold uppercase border">Stock: {{ $item->stock_quantity }}</span>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50/50 border-t border-gray-100">
                        @if($item->stock_quantity > 0)
                            <form action="{{ route('student.cart.add', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-md shadow-indigo-100 active:scale-95">
                                    Add to Cart
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-200 text-gray-400 py-2.5 rounded-xl font-bold text-xs uppercase cursor-not-allowed">
                                Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $items->links() }}
            </div>
        </div>
    </div>

    <script>
        let timeout = null;
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 700);
        });
    </script>
</x-app-layout>