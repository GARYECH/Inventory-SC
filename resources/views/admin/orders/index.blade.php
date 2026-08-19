<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        
        <!-- 🌟 SLEEK HEADER STICKY 🌟 -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Order Management</h1>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1.5">SC Return & Approval Hub</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <form action="{{ route('admin.orders') }}" method="GET" class="relative group w-full md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" placeholder="Cari No. Order / Mahasiswa..." value="{{ request('search') }}" 
                                class="block w-full pl-11 pr-4 py-3 bg-gray-100/50 border-transparent rounded-2xl text-sm font-semibold text-gray-700 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300">
                        </form>
                        
                        <a href="{{ route('admin.orders.export') }}" class="relative inline-flex items-center justify-center px-6 py-3 text-xs font-black text-emerald-700 uppercase tracking-widest transition-all duration-300 bg-emerald-100 rounded-2xl hover:bg-emerald-500 hover:text-white hover:shadow-xl hover:shadow-emerald-200 active:scale-95 whitespace-nowrap overflow-hidden group shrink-0 border border-emerald-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            
            @if(session('success'))
                <div class="mb-8 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-8 px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- 📋 ORDER LIST -->
            <div class="space-y-6">
                @forelse($orders as $order)
                    @php
                        // 🌟 LOGIKA WARNA STATUS LENGKAP
                        $statusColors = [
                            'Pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'Approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Waiting for MoU' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'Pending Review MoU' => 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200',
                            'Waiting for Payment' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'Pending Review Payment' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 
                            'Waiting for Kwitansi' => 'bg-pink-100 text-pink-800 border-pink-200',
                            'Pending Review Kwitansi' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'Handed Over' => 'bg-teal-100 text-teal-800 border-teal-200',
                            'Pending Return Review' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                            'Returned' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Returned (Damaged)' => 'bg-red-100 text-red-800 border-red-200',
                            'Pending Review BA' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'Resolved (Fine Paid)' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Rejected' => 'bg-red-100 text-red-800 border-red-200',
                            'Cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                        ];
                        $badgeClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        
                        $requiresMou = $order->orderItems->contains(function($detail) {
                            return $detail->item->requires_mou;
                        });
                    @endphp

                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col xl:flex-row gap-6">
                        
                        <!-- Kolom 1: Info Mahasiswa & Proker -->
                        <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border {{ $badgeClass }}">
                                        {{ $order->status === 'Waiting for Kwitansi' ? 'Paid / Tunggu Kwitansi' : $order->status }}
                                    </span>
                                    <span class="text-xs font-bold text-gray-400">{{ $order->created_at->format('d M Y') }}</span>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 leading-tight mb-1">{{ $order->order_number }}</h3>
                                <p class="text-sm font-bold text-indigo-600">{{ $order->user->name }}</p>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest">Proker/Event</p>
                                    <p class="text-xs font-bold text-gray-800">{{ $order->proker_name }} ({{ $order->department }})</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest">WhatsApp</p>
                                    <p class="text-xs font-bold text-gray-800">{{ $order->phone_number }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom 2: Rincian Barang -->
                        <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6">
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-3">Item Details</p>
                            <div class="space-y-3 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                <!-- 🌟 HARGA SATUAN & SUBTOTAL DITAMBAHKAN DI SINI 🌟 -->
                                @foreach($order->orderItems as $detail)
                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-white rounded-lg border border-gray-200 overflow-hidden shrink-0">
                                                    @if($detail->item->item_photo)
                                                        <img src="{{ asset('storage/' . $detail->item->item_photo) }}" class="w-full h-full object-cover">
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-900 leading-tight">{{ $detail->item->name }}</p>
                                                    <p class="text-[9px] font-bold mt-0.5">
                                                        @if($detail->price == 0)
                                                            <span class="text-emerald-500">FREE (SC Internal)</span>
                                                        @else
                                                            <span class="text-gray-500">Satuan: Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <p class="text-xs font-black text-gray-900 mb-0.5">{{ $detail->quantity }}x</p>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 border-dashed">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Subtotal</p>
                                            <p class="text-[11px] font-black text-indigo-600">
                                                Rp {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Jadwal Pinjam</p>
                                    @if($order->start_date && $order->end_date && $order->start_date != $order->end_date)
                                        <p class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($order->start_date)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($order->end_date)->format('d/m/y') }}</p>
                                    @else
                                        <p class="text-xs font-bold text-emerald-600">Beli Putus (Merchandise)</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Kolom 3: Aksi & Total Harga -->
                        <div class="xl:w-1/3 flex flex-col justify-between">
                            <div class="bg-gray-900 p-5 rounded-2xl text-white mb-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Grand Total</p>
                                <h4 class="text-2xl font-black text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h4>
                            </div>

                            <div class="space-y-3">
                                <!-- 🌟 AREA DOKUMEN 🌟 -->
                                <div class="flex flex-wrap gap-2">
                                    <a target="_blank" href="{{ route('student.document.invoice', $order->id) }}" class="flex-1 bg-white border border-gray-200 text-gray-700 text-[10px] font-black uppercase tracking-widest py-3 px-2 rounded-xl text-center hover:bg-gray-50 hover:text-indigo-600 transition-all flex justify-center items-center gap-1 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Invoice
                                    </a>
                                    
                                    @if($requiresMou)
                                    <a target="_blank" href="{{ route('student.document.mou', $order->id) }}" class="flex-1 bg-white border border-gray-200 text-gray-700 text-[10px] font-black uppercase tracking-widest py-3 px-2 rounded-xl text-center hover:bg-gray-50 hover:text-indigo-600 transition-all flex justify-center items-center gap-1 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        MoU
                                    </a>
                                    @endif

                                    <a target="_blank" href="{{ route('student.document.kwitansi', $order->id) }}" class="w-full sm:flex-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-widest py-3 px-2 rounded-xl text-center hover:bg-emerald-600 hover:text-white transition-all flex justify-center items-center gap-1 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Kwitansi
                                    </a>
                                </div>

                                <!-- AREA CEK UPLOAD DARI MAHASISWA -->
                                <div>
                                    @if($order->signed_mou)
                                    <a target="_blank" href="{{ asset('storage/' . $order->signed_mou) }}" class="w-full bg-fuchsia-50 border border-fuchsia-200 text-fuchsia-700 text-[10px] font-black uppercase tracking-widest py-2 rounded-xl text-center hover:bg-fuchsia-600 hover:text-white transition-all shadow-sm flex justify-center items-center gap-2 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Cek File MoU Mahasiswa
                                    </a>
                                    @endif

                                    @if($order->payment_receipt)
                                    <a target="_blank" href="{{ asset('storage/' . $order->payment_receipt) }}" class="w-full bg-orange-50 border border-orange-200 text-orange-700 text-[10px] font-black uppercase tracking-widest py-2 rounded-xl text-center hover:bg-orange-600 hover:text-white transition-all shadow-sm flex justify-center items-center gap-2 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Cek Bukti Transfer
                                    </a>
                                    @endif

                                    @if($order->signed_kwitansi)
                                    <a target="_blank" href="{{ asset('storage/' . $order->signed_kwitansi) }}" class="w-full bg-pink-50 border border-pink-200 text-pink-700 text-[10px] font-black uppercase tracking-widest py-2 rounded-xl text-center hover:bg-pink-600 hover:text-white transition-all shadow-sm flex justify-center items-center gap-2 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Cek File Kwitansi Mhs
                                    </a>
                                    @endif

                                    @if($order->return_drive_link)
                                    <a target="_blank" href="{{ $order->return_drive_link }}" class="w-full bg-cyan-50 border border-cyan-200 text-cyan-700 text-[10px] font-black uppercase tracking-widest py-2 rounded-xl text-center hover:bg-cyan-600 hover:text-white transition-all shadow-sm flex justify-center items-center gap-2 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        Cek Bukti Return (Drive)
                                    </a>
                                    @endif

                                    @if($order->ba_total_fine)
                                    <a target="_blank" href="{{ route('student.document.berita-acara', $order->id) }}" class="w-full bg-red-50 border border-red-200 text-red-700 text-[10px] font-black uppercase tracking-widest py-2 rounded-xl text-center hover:bg-red-600 hover:text-white transition-all shadow-sm flex justify-center items-center gap-2 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Cetak Berita Acara (BA)
                                    </a>
                                    @endif

                                    @if($order->signed_ba_file)
                                    <a target="_blank" href="{{ asset('storage/' . $order->signed_ba_file) }}" class="w-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-widest py-2 rounded-xl text-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm flex justify-center items-center gap-2 mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Cek PDF BA & Bukti Denda Mhs
                                    </a>
                                    @endif
                                </div>

                                <!-- 🌟 UPDATE STATUS FORM & CUSTOM NUMBERS 🌟 -->
                                <form x-data="{ status: '{{ $order->status }}' }" action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="flex flex-col gap-2 mt-2 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    @csrf 
                                    @method('PATCH')
                                    
                                    <!-- 🌟 GRID-COLS-3 (MoU, Invoice, Kwitansi) 🌟 -->
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 mb-2">
                                        <div>
                                            <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">MoU Num.</label>
                                            <input type="text" name="mou_number" value="{{ $order->mou_number }}" placeholder="#SC.../MOU" class="w-full px-2 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-[10px] font-bold text-gray-800 transition-all shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Inv Num.</label>
                                            <input type="text" name="invoice_number" value="{{ $order->invoice_number }}" placeholder="#SC.../INV" class="w-full px-2 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-[10px] font-bold text-gray-800 transition-all shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">KWT Num.</label>
                                            <input type="text" name="kwitansi_number" value="{{ $order->kwitansi_number }}" placeholder="#SC.../KWT" class="w-full px-2 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-[10px] font-bold text-gray-800 transition-all shadow-sm">
                                        </div>
                                    </div>

                                    <!-- BA Input Box (Muncul saat Returned Damaged ATAU Pending Review BA) -->
                                    <div x-show="status === 'Returned (Damaged)' || status === 'Pending Review BA'" style="display: none;" class="p-4 bg-red-50 border border-red-200 rounded-xl space-y-3 mb-2 mt-2">
                                        <p class="text-[10px] font-black text-red-800 uppercase tracking-widest border-b border-red-200 pb-2 mb-2">Input Berita Acara & Denda</p>
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-500 uppercase">No. Surat</label>
                                                <input type="text" name="ba_number" value="{{ $order->ba_number }}" placeholder="Cth: 1135/SC/..." class="w-full text-xs p-2 rounded-lg border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-500 uppercase">Tgl Surat</label>
                                                <input type="text" name="ba_date" value="{{ $order->ba_date }}" placeholder="Senin, 6 April 2026" class="w-full text-xs p-2 rounded-lg border-gray-300">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[9px] font-black text-gray-500 uppercase">Rincian Rusak</label>
                                            <textarea name="ba_description" rows="3" placeholder="Cth: 4 Headset HT (Rusak) @ Rp 35.000" class="w-full text-xs p-2 rounded-lg border-gray-300">{{ $order->ba_description }}</textarea>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 items-center">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-500 uppercase">Due Date</label>
                                                <input type="text" name="ba_due_date" value="{{ $order->ba_due_date }}" placeholder="13 April 2026" class="w-full text-xs p-2 rounded-lg border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-red-600 uppercase">Total Denda</label>
                                                <input type="number" name="ba_total_fine" value="{{ $order->ba_total_fine }}" placeholder="340000" class="w-full text-xs p-2 rounded-lg border-red-300 bg-red-100 text-red-800 font-black">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- DROPDOWN & BUTTON UPDATE -->
                                    <div class="flex gap-2 mt-2">
                                        <select name="status" x-model="status" class="flex-grow px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-xs font-bold text-gray-800 appearance-none cursor-pointer shadow-sm">
                                            <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ $order->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Waiting for MoU" {{ $order->status == 'Waiting for MoU' ? 'selected' : '' }}>Waiting for MoU</option>
                                            <option value="Pending Review MoU" {{ $order->status == 'Pending Review MoU' ? 'selected' : '' }}>Pending Review MoU</option>
                                            <option value="Waiting for Payment" {{ $order->status == 'Waiting for Payment' ? 'selected' : '' }}>Waiting for Payment</option>
                                            <option value="Pending Review Payment" {{ $order->status == 'Pending Review Payment' ? 'selected' : '' }}>Pending Review Payment</option>
                                            <option value="Waiting for Kwitansi" {{ $order->status == 'Waiting for Kwitansi' ? 'selected' : '' }}>Waiting for Kwitansi (Paid/TF Approved)</option>
                                            <option value="Pending Review Kwitansi" {{ $order->status == 'Pending Review Kwitansi' ? 'selected' : '' }}>Pending Review Kwitansi</option>
                                            <option value="Handed Over" {{ $order->status == 'Handed Over' ? 'selected' : '' }}>Handed Over</option>
                                            <option value="Pending Return Review" {{ $order->status == 'Pending Return Review' ? 'selected' : '' }}>Pending Return Review</option>
                                            
                                            <option value="Returned" {{ $order->status == 'Returned' ? 'selected' : '' }}>Returned (Aman/Selesai)</option>
                                            <option value="Returned (Damaged)" {{ $order->status == 'Returned (Damaged)' ? 'selected' : '' }}>Returned (Ada Kerusakan/Denda)</option>
                                            <option value="Pending Review BA" {{ $order->status == 'Pending Review BA' ? 'selected' : '' }}>Pending Review BA</option>
                                            <option value="Resolved (Fine Paid)" {{ $order->status == 'Resolved (Fine Paid)' ? 'selected' : '' }}>Resolved (Denda Lunas)</option>
                                            
                                            <option value="Rejected" {{ $order->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm hover:shadow-md">
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-32">
                        <div class="w-24 h-24 bg-white border border-gray-100 shadow-sm rounded-[2rem] rotate-12 flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-12 h-12 -rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">Belum Ada Transaksi</h3>
                        <p class="text-gray-400 font-bold text-sm">Gudang sedang sepi, bos. Tunggu mahasiswa order ya!</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                <div class="bg-white px-2 py-1 rounded-2xl shadow-sm border border-gray-100">
                    {{ $orders->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>