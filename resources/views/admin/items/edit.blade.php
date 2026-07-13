<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Asset: <span class="text-indigo-600">{{ $item->name }}</span>
            </h2>
            <a href="{{ route('admin.items.index') }}" class="text-xs text-gray-500 hover:text-indigo-600 font-bold uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Inventory
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="border-b border-gray-100 pb-8">
                        <h4 class="text-sm font-black uppercase text-gray-900 tracking-wider mb-4">Current Asset Photo</h4>
                        <div class="flex items-center gap-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $item->item_photo) }}" 
                                    class="w-32 h-32 rounded-xl object-cover border-4 border-white shadow-lg transition group-hover:scale-105" 
                                    alt="Current Photo">
                                <span class="absolute bottom-2 right-2 bg-indigo-600 text-white p-1 rounded-full shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </div>
                            <div class="flex-grow">
                                <label class="block text-xs font-black uppercase text-gray-500 mb-2">Upload New Photo (Optional)</label>
                                <input type="file" name="item_photo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-2">Leave blank to keep the current photo, *mon cher*.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h4 class="text-sm font-black uppercase text-gray-900 tracking-wider">Asset Details</h4>
                        <div>
                            <label class="block text-xs font-black uppercase text-gray-500 mb-2">Item Name</label>
                            <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-2">Price Per Day (Rp)</label>
                                <input type="number" name="price" value="{{ old('price', $item->price) }}" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-gray-500 mb-2">Stock Quantity</label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $item->stock_quantity) }}" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase text-gray-500 mb-2">Condition Status</label>
                            <select name="condition_status" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="Good" {{ $item->condition_status === 'Good' ? 'selected' : '' }}>Good (Ready to Rent)</option>
                                <option value="Broken" {{ $item->condition_status === 'Broken' ? 'selected' : '' }}>Broken (Maintenance Required)</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('admin.items.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-black uppercase tracking-widest hover:bg-gray-200 transition text-xs active:scale-95">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 text-white px-10 py-3 rounded-xl font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 active:scale-95">
                            Update Asset
                        </button>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</x-app-layout>