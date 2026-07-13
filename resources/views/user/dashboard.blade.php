<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Available for Rent') }}
            </h2>

            <form action="{{ route('student.dashboard') }}" method="GET" id="searchForm" class="relative w-full md:w-1/3">
                <input type="text" name="search" id="searchInput" placeholder="Search gear, cameras, projectors..." 
                    class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-xl focus:ring-indigo-500 shadow-sm text-sm"
                    value="{{ request('search') }}">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-l-4 border-green-500">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($items as $item)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl flex flex-col border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-48 object-cover">
                        <span class="absolute top-3 right-3 px-2 py-1 text-[9px] font-black uppercase rounded-md shadow-sm {{ $item->condition_status === 'Good' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $item->condition_status }}
                        </span>
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
                            <a href="{{ route('student.rent.create', $item) }}" class="block text-center w-full bg-indigo-600 text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-md shadow-indigo-100 active:scale-95">
                                Book Reservation
                            </a>
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

            @if($items->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-dashed border-gray-200">
                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <p class="text-gray-400 font-medium italic">No items matching your search, *mon cher*.</p>
                    <a href="{{ route('student.dashboard') }}" class="text-indigo-600 text-sm font-bold mt-2 inline-block hover:underline">Clear Search</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        let timeout = null;
        document.getElementById('searchInput').addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 700); // Tunggu 0.7 detik setelah user berhenti mengetik
        });
    </script>
</x-app-layout>