<x-app-layout>
    <div class="min-h-screen bg-[#f4f6f9] pb-20 font-sans selection:bg-indigo-500 selection:text-white">
        
        <!-- 🌟 PREMIUM HEADER 🌟 -->
        <div class="bg-white/70 backdrop-blur-2xl border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-900 rounded-2xl flex items-center justify-center shadow-lg shadow-gray-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none">Booking Timeline</h1>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-[0.2em] mt-1">{{ $item->name }}</p>
                    </div>
                </div>
                <a href="{{ route('student.dashboard') }}" class="flex items-center justify-center w-10 h-10 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-indigo-600 transition-all shadow-sm group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-6 mt-10">
            
            <!-- 🌟 HERO CAPACITY CARD (DARK MODE ACCENT) 🌟 -->
            <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-indigo-900 to-gray-900 rounded-[2.5rem] p-10 shadow-2xl shadow-indigo-200 border border-gray-800 mb-12 group">
                <!-- Abstract Glow -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500 rounded-full mix-blend-screen filter blur-[4rem] opacity-40 group-hover:opacity-60 transition-opacity duration-700"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-purple-500 rounded-full mix-blend-screen filter blur-[4rem] opacity-30 group-hover:opacity-50 transition-opacity duration-700"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/10 backdrop-blur-md border border-white/10 mb-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[9px] font-black text-indigo-100 uppercase tracking-widest">Inventory SC Capacity</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight drop-shadow-md">
                            {{ $item->stock_quantity }} <span class="text-indigo-300 font-bold text-3xl">Unit Total</span>
                        </h2>
                    </div>
                    
                    <div class="w-24 h-24 bg-white/5 border border-white/10 rounded-3xl flex items-center justify-center backdrop-blur-xl shadow-inner shrink-0 rotate-3 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
            </div>

            <!-- 🌟 MODERN TIMELINE SECTION 🌟 -->
            <div class="mb-6 flex items-center gap-4">
                <h3 class="text-lg font-black text-gray-900 tracking-tight">Active Schedule</h3>
                <div class="h-px bg-gray-200 flex-grow"></div>
            </div>

            @if($activeBookings->isEmpty())
                <div class="bg-white rounded-[2.5rem] border border-gray-100 p-16 text-center shadow-sm">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Jadwal Kosong!</h4>
                    <p class="text-sm font-bold text-gray-400">Belum ada yang meminjam barang ini. Seluruh kuota {{ $item->stock_quantity }} unit tersedia utuh.</p>
                </div>
            @else
                <!-- Timeline Container -->
                <div class="relative pl-4 md:pl-8 border-l-2 border-indigo-100 space-y-10 py-4">
                    
                    @foreach($activeBookings as $index => $booking)
                        <div class="relative group">
                            <!-- Timeline Dot -->
                            <div class="absolute -left-[23px] md:-left-[39px] top-8 w-4 h-4 bg-indigo-500 rounded-full border-4 border-[#f4f6f9] shadow-sm group-hover:scale-150 group-hover:bg-purple-500 transition-all duration-300"></div>

                            <!-- Schedule Card -->
                            <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:border-indigo-200 transition-all duration-300 hover:-translate-y-1 ml-4 md:ml-0">
                                
                                <div class="flex flex-col md:flex-row justify-between gap-6">
                                    
                                    <!-- Date Section -->
                                    <div class="md:w-1/3 flex gap-5 items-center md:border-r border-gray-100 md:pr-6">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex flex-col items-center justify-center border border-gray-100 shadow-inner shrink-0 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                            <span class="text-[9px] font-black uppercase text-gray-400 group-hover:text-indigo-400">Start</span>
                                            <span class="text-xl font-black text-gray-900 group-hover:text-indigo-600 leading-none mt-0.5">{{ \Carbon\Carbon::parse($booking->order->start_date)->format('d') }}</span>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 mb-1">Durasi Pinjam</p>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ \Carbon\Carbon::parse($booking->order->start_date)->format('d M') }} <span class="text-gray-300 mx-1">➔</span> {{ \Carbon\Carbon::parse($booking->order->end_date)->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Event Info Section -->
                                    <div class="md:w-1/3 flex flex-col justify-center">
                                        <p class="text-[9px] font-black text-purple-500 uppercase tracking-widest mb-1">{{ $booking->order->organization }}</p>
                                        <h4 class="text-lg font-black text-gray-900 tracking-tight leading-tight">{{ $booking->order->proker_name }}</h4>
                                        <div class="flex items-center gap-2 mt-2">
                                            <div class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center border border-gray-200">
                                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <p class="text-[11px] font-bold text-gray-500 uppercase">{{ $booking->order->full_name }}</p>
                                        </div>
                                    </div>

                                    <!-- Quantity Badge Section -->
                                    <div class="md:w-1/4 flex items-center md:justify-end">
                                        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 w-full md:w-auto text-center md:text-right shadow-sm group-hover:bg-orange-500 group-hover:border-orange-600 transition-colors duration-300">
                                            <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest mb-1 group-hover:text-orange-100">Ditahan</p>
                                            <p class="text-2xl font-black text-orange-600 group-hover:text-white leading-none">{{ $booking->quantity }} <span class="text-sm font-bold">Unit</span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Timeline End Indicator -->
                <div class="pl-4 md:pl-8 flex items-center mt-6">
                    <div class="w-4 h-4 bg-gray-200 rounded-full -ml-[23px] md:-ml-[39px] border-4 border-[#f4f6f9]"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">End of Schedule</span>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>