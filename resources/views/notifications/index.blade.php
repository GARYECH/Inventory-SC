<x-app-layout>
    <!-- HEADER -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Inbox Notifikasi</h2>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Pusat Informasi & Transaksi</p>
                    </div>
                </div>

                @if(auth()->user()->notifications->count() > 0)
                    <form method="POST" action="{{ route('notifications.clear') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all duration-300 border border-red-100 hover:border-red-500 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <span class="hidden sm:inline">Bersihkan Semua</span>
                            <span class="sm:hidden">Bersihkan</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- BODY CONTENT -->
    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                @if(auth()->user()->notifications->count() > 0)
                    <ul class="divide-y divide-gray-100">
                        <!-- 🌟 BATASI TAMPIL MAKSIMAL 50 BIAR GAK LEMOT 🌟 -->
                        @foreach(auth()->user()->notifications()->take(50)->get() as $notification)
                            <li class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-start gap-5">
                                    
                                    <!-- Ikon Notifikasi (Berubah warna berdasarkan peran) -->
                                    @if(auth()->user()->role === 'admin')
                                        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @endif

                                    <!-- Konten -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start gap-4">
                                            <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">
                                                {{ $notification->data['message'] ?? 'Informasi Sistem' }}
                                            </h3>
                                            
                                            <!-- 🌟 TOMBOL HAPUS SATUAN (TONG SAMPAH) 🌟 -->
                                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-xl transition-all duration-200" title="Hapus Notifikasi ini">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="mt-2 flex items-center gap-3">
                                            <p class="text-xs font-bold text-gray-400 flex items-center gap-1.5 uppercase tracking-wider">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ $notification->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <!-- Tampilan Jika Kosong Melompong -->
                    <div class="py-24 flex flex-col items-center justify-center text-center px-4">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-6 border-8 border-white shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight mb-2">Inbox Kosong</h3>
                        <p class="text-sm font-medium text-gray-500 max-w-sm">
                            Saat ini belum ada notifikasi atau pembaruan transaksi untukmu. Pesan baru akan muncul di sini.
                        </p>
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}" class="mt-8 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-indigo-200 transition-all duration-300">
                            Kembali ke Beranda
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>