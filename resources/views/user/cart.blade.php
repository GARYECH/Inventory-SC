<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        
        <!-- 🌟 SLEEK HEADER STICKY 🌟 -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Checkout Request</h1>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1.5">Review & Confirm Your Items</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('student.dashboard') }}" class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-gray-700 uppercase tracking-widest transition-all duration-300 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:text-indigo-600 hover:shadow-sm active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Katalog
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
            
            @if(session('error'))
                <div class="mb-8 px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            @if(empty($cart))
                <div class="text-center py-32 bg-white rounded-[3rem] shadow-sm border border-gray-100">
                    <div class="w-24 h-24 bg-gray-50 border border-gray-100 shadow-inner rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Keranjangmu Masih Kosong!</h3>
                    <p class="text-gray-400 font-bold text-sm mb-8">Ayo pilih barang untuk proker atau kebutuhanmu terlebih dahulu.</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 active:scale-95">
                        Jelajahi Katalog
                    </a>
                </div>
            @else
                
                @php 
                    $firstItem = reset($cart);
                    $isRental = $firstItem['transaction_type'] !== 'Sale';
                    $totalPrice = 0;
                    $sopPath = \App\Models\Setting::where('key', 'sop_pdf_path')->value('value');
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    
                    <!-- 🛒 BAGIAN KIRI: DAFTAR BARANG -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden relative">
                            
                            <div class="p-8 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="font-black text-xl text-gray-900 tracking-tight mb-1">Rincian Barang</h3>
                                    <span class="inline-flex px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg 
                                        {{ $firstItem['transaction_type'] === 'Internal Rental' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        {{ $firstItem['transaction_type'] === 'Vendor Rental' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $firstItem['transaction_type'] === 'Sale' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                        Mode: {{ $firstItem['transaction_type'] }}
                                    </span>
                                </div>
                                <form action="{{ route('student.cart.clear') }}" method="POST" class="relative z-10">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black text-red-500 hover:text-red-700 hover:bg-red-50 px-4 py-2 rounded-xl transition-all uppercase tracking-widest border border-transparent hover:border-red-100 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Kosongkan
                                    </button>
                                </form>
                            </div>
                            
                            <div class="p-8 space-y-6">
                                @foreach($cart as $id => $details)
                                    @php $totalPrice += $details['price'] * $details['quantity']; @endphp
                                    <div class="flex justify-between items-center p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-indigo-100 hover:shadow-md transition-all group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 group-hover:bg-indigo-50 transition-colors">
                                                <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-black text-gray-900 text-lg">{{ $details['name'] }}</h4>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Harga Satuan: Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-gray-100 text-gray-800 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border border-gray-200 inline-block mb-1">
                                                Qty: {{ $details['quantity'] }}
                                            </span>
                                            <p class="font-black text-indigo-600 text-lg">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="p-8 bg-gray-900 flex justify-between items-center rounded-b-[2.5rem]">
                                <span class="font-black text-gray-400 uppercase tracking-[0.2em] text-[11px]">Grand Total</span>
                                <span class="font-black text-3xl text-white">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 📝 BAGIAN KANAN: FORM CHECKOUT -->
                    <div class="lg:col-span-5">
                        <div class="bg-white shadow-xl shadow-gray-100/50 rounded-[2.5rem] border border-gray-100 p-8 lg:sticky lg:top-32">
                            
                            <h3 class="font-black text-xl text-gray-900 mb-8 tracking-tight flex items-center">
                                <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </span>
                                Formulir Pengajuan
                            </h3>
                            
                            <form action="{{ route('student.cart.checkout') }}" method="POST" class="space-y-5">
                                @csrf
                                
                                <!-- 🌟 TAMBAHAN: NAMA LENGKAP 🌟 -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Lengkap PIC</label>
                                    <input type="text" name="full_name" placeholder="Masukkan nama lengkap..." required 
                                        class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-inner text-sm">
                                </div>
                                
                                <!-- 🌟 TAMBAHAN: ORGANISASI & JABATAN (GRID) 🌟 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Organisasi</label>
                                        <select name="organization" required 
                                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 transition-all shadow-inner text-sm cursor-pointer appearance-none">
                                            <option value="" disabled selected>Pilih Organisasi...</option>
                                            <option value="Student Council">Student Council</option>
                                            <option value="Student Union">Student Union</option>
                                            <option value="Mentoring Department">Mentoring Department</option>
                                            <option value="Student Representative Board">Student Representative Board</option>
                                            <option value="Unit Kegiatan Mahasiswa (UKM)">Unit Kegiatan Mahasiswa (UKM)</option>
                                            <option value="Organisasi External">Organisasi External</option> 
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Jabatan</label>
                                        <input type="text" name="position" placeholder="e.g. Koordinator Inventory " required 
                                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-inner text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Proker / Event</label>
                                    <input type="text" name="proker_name" placeholder="e.g. Rector Cup 2026" required 
                                        class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-inner text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Alamat Lengkap</label>
                                    <textarea name="address" rows="2" placeholder="e.g. Jl. Citraland CBD..." required 
                                        class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-inner text-sm"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">No. WhatsApp</label>
                                        <input type="text" name="phone_number" placeholder="0812..." required 
                                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-inner text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Bendahara</label>
                                        <input type="text" name="treasurer_name" placeholder="Nama Lengkap" required 
                                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-inner text-sm">
                                    </div>
                                </div>

                                @if($isRental)
                                <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl mt-6">
                                    <h4 class="font-black text-[11px] text-indigo-600 uppercase tracking-widest mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Jadwal Peminjaman
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[9px] font-bold text-indigo-400 uppercase mb-1">Start Date</label>
                                            <input type="date" name="start_date" required class="w-full px-4 py-3 bg-white border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-700 shadow-sm text-xs cursor-pointer">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-bold text-indigo-400 uppercase mb-1">End Date</label>
                                            <input type="date" name="end_date" required class="w-full px-4 py-3 bg-white border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-700 shadow-sm text-xs cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                @else
                                <input type="hidden" name="start_date" value="{{ now()->format('Y-m-d') }}">
                                <input type="hidden" name="end_date" value="{{ now()->format('Y-m-d') }}">
                                <div class="mt-6 p-5 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-emerald-900 uppercase tracking-widest">Beli Putus (Merchandise)</p>
                                        <p class="text-[10px] font-bold text-emerald-600 mt-1 leading-snug">Tidak perlu menentukan tanggal pengembalian barang.</p>
                                    </div>
                                </div>
                                @endif

                                <div class="mt-10 mb-8 border-2 border-red-100 bg-white rounded-[2rem] overflow-hidden shadow-xl shadow-red-100/50">
                                    <div class="bg-red-50/80 p-6 border-b border-red-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                                        <div class="flex items-center gap-4 w-full sm:w-auto">
                                            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center shrink-0">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-black text-red-900 text-sm uppercase tracking-widest">Wajib Baca SOP</h4>
                                                <p class="text-[10px] text-red-600 font-bold mt-1">Syarat & Ketentuan Peminjaman SC</p>
                                            </div>
                                        </div>

                                        @if($sopPath)
                                            <a href="{{ asset('storage/' . $sopPath) }}" target="_blank" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-red-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-red-700 hover:shadow-lg hover:shadow-red-300 transition-all active:scale-95 shrink-0">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Buka File SOP
                                            </a>
                                        @else
                                            <span class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-gray-200 text-gray-500 font-black text-[10px] uppercase tracking-widest rounded-xl shrink-0 cursor-not-allowed">
                                                SOP Belum Tersedia
                                            </span>
                                        @endif
                                    </div>

                                    <label class="flex items-start p-6 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <div class="flex-shrink-0 mt-1">
                                            <input type="checkbox" name="is_sop_accepted" value="1" required class="w-6 h-6 text-red-600 border-gray-300 rounded-md shadow-inner focus:ring-red-500 cursor-pointer transition-all">
                                        </div>
                                        <div class="ml-4">
                                            <span class="block text-sm font-black text-gray-900 uppercase tracking-widest">Saya Menyetujui Persyaratan</span>
                                            <span class="block text-[10px] text-gray-500 mt-1.5 leading-relaxed font-bold">
                                                Dengan mencentang kotak ini, saya menyatakan telah membaca SOP dan bertanggung jawab penuh atas barang yang dipinjam/dibeli serta bersedia mematuhi denda yang berlaku jika terjadi kerusakan atau keterlambatan.
                                            </span>
                                        </div>
                                    </label>
                                </div>

                                <button type="submit" class="w-full py-5 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-[0.3em] hover:bg-indigo-600 transition-all shadow-xl shadow-gray-200 active:scale-95 flex justify-center items-center group">
                                    Submit Order Request
                                    <svg class="w-4 h-4 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>