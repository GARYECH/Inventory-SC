<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    Booking: <span class="text-indigo-600">{{ $item->name }}</span>
                </h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                    Student Council Asset Management
                </p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="text-xs font-black text-gray-400 hover:text-indigo-600 transition uppercase tracking-tighter flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Catalog
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl shadow-sm flex items-center animate-bounce">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
                    <span class="text-xs font-black uppercase italic">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-2 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row overflow-hidden">
                        <img src="{{ asset('storage/' . $item->item_photo) }}" class="w-full md:w-64 h-48 object-cover rounded-2xl shadow-inner">
                        <div class="p-6">
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase rounded-full border border-indigo-100">
                                    Total: {{ $item->stock_quantity }} Units
                                </span>
                                <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-full border border-green-100">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}/Day
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mt-4 tracking-tight">{{ $item->name }}</h3>
                            <p class="text-sm text-gray-500 mt-2 leading-relaxed italic">"{{ $item->description }}"</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Occupancy Timeline
                        </h3>
                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($existingReservations as $res)
                                <div class="relative pl-6 border-l-2 border-indigo-100 pb-2">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 border-indigo-600"></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl border border-gray-100 hover:border-indigo-200 transition group">
                                        <div>
                                            <p class="text-xs font-black text-gray-900">{{ $res->user->name }}</p>
                                            <p class="text-[10px] text-indigo-500 font-bold uppercase mt-1">
                                                {{ \Carbon\Carbon::parse($res->start_date)->format('d M') }} — {{ \Carbon\Carbon::parse($res->end_date)->format('d M Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-black text-gray-800">{{ $res->quantity }}x</p>
                                            <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded bg-white border border-gray-200 text-gray-400">{{ $res->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 border-2 border-dashed border-gray-100 rounded-3xl">
                                    <p class="text-gray-300 text-xs font-black uppercase tracking-tighter">Inventory is free for the taking, mon cher.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="sticky top-8">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-2 bg-indigo-600"></div>

                        <h3 class="font-black text-2xl text-gray-900 mb-1">Reserve Now</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-8">Exclusive for Students</p>

                        <form action="{{ route('student.rent.store', $item) }}" method="POST" class="space-y-5">
                            @csrf
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">How many units?</label>
                                <div class="relative">
                                    <input type="number" name="quantity" min="1" max="{{ $item->stock_quantity }}" 
                                        value="{{ old('quantity') }}"
                                        class="w-full pl-5 pr-12 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-black text-xl text-gray-900 @error('quantity') ring-2 ring-red-500 @enderror" 
                                        placeholder="0" required>
                                    <span class="absolute right-5 top-5 text-gray-300 font-bold text-xs uppercase">Qty</span>
                                </div>
                                @error('quantity')
                                    <p class="text-[9px] text-red-500 font-black mt-1 uppercase tracking-tighter">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Start Date</label>
                                    <input type="date" name="start_date" min="{{ date('Y-m-d') }}" 
                                        value="{{ old('start_date') }}"
                                        class="w-full p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-sm text-gray-700 @error('start_date') ring-2 ring-red-500 @enderror" required>
                                    @error('start_date')
                                        <p class="text-[9px] text-red-500 font-black mt-1 uppercase tracking-tighter">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">End Date</label>
                                    <input type="date" name="end_date" min="{{ date('Y-m-d') }}" 
                                        value="{{ old('end_date') }}"
                                        class="w-full p-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-sm text-gray-700 @error('end_date') ring-2 ring-red-500 @enderror" required>
                                    @error('end_date')
                                        <p class="text-[9px] text-red-500 font-black mt-1 uppercase tracking-tighter">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="pt-6">
                                <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-700 shadow-2xl shadow-indigo-200 transition-all transform active:scale-95">
                                    Confirm Reservation
                                </button>
                                <div class="mt-6 flex items-center justify-center space-x-2 opacity-50">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"></path></svg>
                                    <span class="text-[9px] font-black uppercase">Secure Student Portal</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>