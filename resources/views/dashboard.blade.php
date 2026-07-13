<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Council Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(auth()->user()->role === 'admin')
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Admin Management</h3>
                        <a href="{{ route('items.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ Add New Item</a>
                    </div>
                    
                    <p class="text-gray-600">Welcome, Boss. You have 5 pending orders to confirm.</p>

                @else
                    <h3 class="text-lg font-bold mb-4">Available Items for Rental</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($items as $item)
                            <div class="border rounded-lg p-4 shadow-sm">
                                <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full h-40 object-cover rounded">
                                <h4 class="font-bold mt-2">{{ $item->name }}</h4>
                                <p class="text-sm text-gray-500">Stock: {{ $item->stock_quantity }}</p>
                                <button class="mt-4 w-full bg-green-600 text-white py-2 rounded">Request Rental</button>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>