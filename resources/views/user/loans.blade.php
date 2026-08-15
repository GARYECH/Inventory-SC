<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        
        <!-- 🌟 SLEEK HEADER STICKY 🌟 -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 hover:rotate-0 transition-all duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Riwayat & Status Transaksi</h1>
                            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mt-1.5">Student Council Active & Past Loans</p>
                        </div>
                    </div>
                    <a href="{{ route('student.dashboard') }}" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl text-xs font-black text-gray-700 uppercase tracking-widest hover:bg-gray-50 hover:text-indigo-600 transition-all shadow-sm">
                        ← Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-10">
            
            @if(session('success'))
                <div class="px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- 🟢 TRANSAKSI AKTIF (Active Loans) -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 pb-5 mb-8">
                    <h3 class="text-lg font-black text-gray-900 tracking-tight">Transaksi Aktif & Menunggu Persetujuan</h3>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl">Real-time Status</span>
                </div>
                
                @if($activeLoans->isEmpty())
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Belum ada transaksi aktif saat ini.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($activeLoans as $order)
                            @php
                                // 🌟 STATUS WARNA LENGKAP (TERMASUK DAMAGED & RESOLVED)
                                $statusColors = [
                                    'Pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'Approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Waiting for MoU' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'Pending Review MoU' => 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200',
                                    'Waiting for Payment' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'Waiting for Kwitansi' => 'bg-pink-100 text-pink-800 border-pink-200',
                                    'Pending Review Kwitansi' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'Paid' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'Handed Over' => 'bg-teal-100 text-teal-800 border-teal-200',
                                    'Pending Return Review' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                    'Returned' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'Returned (Damaged)' => 'bg-red-100 text-red-800 border-red-200', // 🔴 MERAH
                                    'Resolved (Fine Paid)' => 'bg-emerald-100 text-emerald-800 border-emerald-200', // 🟢 HIJAU
                                    'Rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'Cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                ];
                                $badgeClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp

                            <div class="border border-gray-100 rounded-3xl p-6 bg-gray-50/50 hover:bg-white hover:shadow-xl transition-all duration-300">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-100 pb-5 mb-5 gap-4">
                                    <div>
                                        <h4 class="font-black text-indigo-600 text-xl tracking-tight">{{ $order->order_number }}</h4>
                                        <p class="text-xs font-bold text-gray-500 mt-1">
                                            Proker: <span class="text-gray-800">{{ $order->proker_name }}</span> | Tipe: <span class="text-gray-800">{{ $order->order_type }}</span>
                                        </p>
                                        @if($order->order_type !== 'Sale' && $order->start_date)
                                            <p class="text-xs font-black text-indigo-500 mt-1.5 flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Jadwal: {{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($order->end_date)->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $badgeClass }} shadow-sm inline-block">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <div class="lg:col-span-2">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Rincian Barang:</p>
                                        <div class="space-y-2">
                                            @foreach($order->orderItems as $detail)
                                                <div class="flex justify-between items-center bg-white p-3.5 border border-gray-100 rounded-2xl shadow-sm">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center font-black text-xs text-indigo-600 border border-gray-100">
                                                            {{ $detail->quantity }}x
                                                        </div>
                                                        <span class="font-bold text-gray-900 text-sm">{{ $detail->item->name }}</span>
                                                    </div>
                                                    <span class="font-black text-gray-700 text-sm">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-col justify-between bg-white p-5 border border-gray-100 rounded-2xl shadow-sm">
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Grand Total</p>
                                            <p class="text-2xl font-black text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                        </div>
                                        
                                        <!-- 🌟 TOMBOL & FORM UPLOAD (TERBAGI 5 FASE) 🌟 -->
                                        <div class="mt-6 space-y-2">
                                            
                                            <!-- ================= FASE 1: MOU ================= -->
                                            @if($order->status === 'Waiting for MoU' || $order->status === 'Pending Review MoU')
                                                <a href="{{ route('student.document.mou', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-indigo-600 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 mb-3">
                                                    Download MoU Kosong
                                                </a>

                                                @if(empty($order->signed_mou))
                                                    <form action="{{ route('student.orders.upload-mou', $order->id) }}" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                                                        @csrf
                                                        <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Upload TTD Ketua/Bendahara:</label>
                                                        <input type="file" name="signed_mou" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-700 cursor-pointer mb-3">
                                                        <button type="submit" class="w-full py-3 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-md">
                                                            Kirim ke Admin SC
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- TAMPILAN TERKUNCI -->
                                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl text-center">
                                                        <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest">MoU Terkirim</p>
                                                        <p class="text-[9px] text-gray-500 mt-1 mb-2">Menunggu verifikasi Admin SC.</p>
                                                    </div>
                                                @endif

                                            <!-- ================= FASE 2: PEMBAYARAN ================= -->
                                            @elseif($order->status === 'Waiting for Payment')
                                                <a href="{{ route('student.document.invoice', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-gray-900 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-lg mb-3">
                                                    Download Tagihan (Invoice)
                                                </a>
                                                
                                                @if(empty($order->payment_receipt))
                                                    <form action="{{ route('student.orders.upload-payment', $order->id) }}" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                                                        @csrf
                                                        <label class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Upload Bukti Transfer:</label>
                                                        <input type="file" name="payment_receipt" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-orange-50 file:text-orange-700 cursor-pointer mb-3">
                                                        <button type="submit" class="w-full py-3 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 transition shadow-md">
                                                            Kirim Bukti Transfer
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl text-center">
                                                        <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Bukti Transfer Terkirim</p>
                                                        <p class="text-[9px] text-gray-500 mt-1 mb-2">Menunggu verifikasi Admin SC.</p>
                                                    </div>
                                                @endif

                                            <!-- ================= FASE 3: KWITANSI TTD ================= -->
                                            @elseif($order->status === 'Waiting for Kwitansi' || $order->status === 'Pending Review Kwitansi')
                                                <div class="p-5 bg-pink-50 border border-pink-200 rounded-2xl">
                                                    <p class="text-[11px] font-black text-pink-800 mb-1 uppercase tracking-widest">Upload Kwitansi SC</p>
                                                    <p class="text-[10px] text-pink-600 mb-4 font-bold leading-relaxed">Silakan download, tandatangani, lalu upload kembali file Kwitansi Resmi.</p>
                                                    
                                                    <a target="_blank" href="{{ route('student.document.kwitansi', $order->id) }}" class="block w-full bg-white border border-pink-200 text-pink-700 text-[10px] font-black uppercase tracking-widest py-2.5 rounded-xl text-center hover:bg-pink-600 hover:text-white transition-all shadow-sm mb-3">
                                                        📥 Download Kwitansi
                                                    </a>

                                                    @if($order->status === 'Waiting for Kwitansi')
                                                        <form action="{{ route('student.orders.upload-kwitansi', $order->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                                            @csrf
                                                            <input type="file" name="signed_kwitansi" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-[10px] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-pink-200 file:text-pink-800 cursor-pointer bg-white border border-pink-100 rounded-xl">
                                                            <button type="submit" class="w-full bg-pink-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-pink-700 transition shadow-md">
                                                                Upload File
                                                            </button>
                                                        </form>
                                                    @else
                                                        <div class="mt-2 text-center">
                                                            <p class="text-[10px] font-black text-rose-700 uppercase tracking-widest">Kwitansi Direview Admin</p>
                                                        </div>
                                                    @endif
                                                </div>

                                            <!-- ================= FASE 4: BARANG DIPINJAM (HANDED OVER) ================= -->
                                            @elseif($order->status === 'Handed Over' || $order->status === 'Pending Return Review')
                                                <a href="{{ route('student.document.kwitansi', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-emerald-50 text-emerald-700 border border-emerald-200 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition shadow-sm mb-3">
                                                    Kwitansi Lunas SC
                                                </a>

                                                @if($order->status === 'Handed Over')
                                                    <!-- FORM UPLOAD GOOGLE DRIVE -->
                                                    <div class="p-5 bg-cyan-50 border border-cyan-200 rounded-2xl">
                                                        <p class="text-[11px] font-black text-cyan-800 mb-1 uppercase tracking-widest">Pengembalian Barang</p>
                                                        <p class="text-[10px] text-cyan-600 mb-4 font-bold leading-relaxed">Wajib lampirkan link Google Drive berisi foto barang yang dikembalikan.</p>
                                                        
                                                        <form action="{{ route('student.orders.return-link', $order->id) }}" method="POST" class="flex flex-col gap-2">
                                                            @csrf
                                                            <input type="url" name="return_drive_link" placeholder="https://drive.google.com/..." required class="w-full px-3 py-2 border-cyan-200 bg-white rounded-xl text-xs focus:ring-cyan-500 shadow-sm font-bold text-gray-700">
                                                            <button type="submit" class="w-full bg-cyan-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-700 transition shadow-md">
                                                                Kirim Bukti Return
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <!-- DRIVE SUBMITTED -->
                                                    <div class="mt-2 text-center p-4 bg-cyan-50 rounded-2xl border border-cyan-200">
                                                        <p class="text-[10px] font-black text-cyan-700 uppercase tracking-widest">Barang Sedang Dicek Admin SC</p>
                                                        <p class="text-[9px] font-bold text-cyan-600 mt-1">Sistem akan segera ditutup.</p>
                                                    </div>
                                                @endif

                                            <!-- ================= FASE 5: BARANG RUSAK / DENDA (BERITA ACARA) ================= -->
                                            @elseif($order->status === 'Returned (Damaged)')
                                                <div class="p-5 bg-red-50 border border-red-200 rounded-2xl">
                                                    <p class="text-[11px] font-black text-red-800 mb-1 uppercase tracking-widest flex items-center">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        Penyelesaian Denda
                                                    </p>
                                                    <p class="text-[10px] text-red-600 mb-4 font-bold leading-relaxed">Ada kerusakan/kehilangan. Download Berita Acara (BA), tandatangani, dan gabungkan dengan Bukti Transfer Denda dalam 1 file PDF, lalu upload di bawah ini.</p>
                                                    
                                                    <a target="_blank" href="{{ route('student.document.berita-acara', $order->id) }}" class="block w-full bg-white border border-red-200 text-red-700 text-[10px] font-black uppercase tracking-widest py-2.5 rounded-xl text-center hover:bg-red-600 hover:text-white transition-all shadow-sm mb-3">
                                                        📥 Download Berita Acara
                                                    </a>

                                                    @if(empty($order->signed_ba_file))
                                                        <form action="{{ route('student.orders.upload-ba', $order->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                                            @csrf
                                                            <input type="file" name="signed_ba_file" accept=".pdf" required class="w-full text-[10px] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-red-200 file:text-red-800 hover:file:bg-red-300 cursor-pointer bg-white border border-red-100 rounded-xl">
                                                            <button type="submit" class="w-full bg-red-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition shadow-md shadow-red-200 active:scale-95">
                                                                Upload Bukti BA & Denda
                                                            </button>
                                                        </form>
                                                    @else
                                                        <div class="mt-2 text-center p-3 bg-white rounded-xl border border-red-100">
                                                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Dokumen Terkirim</p>
                                                            <p class="text-[9px] font-bold text-gray-500 mt-1">Menunggu konfirmasi penyelesaian dari Admin SC.</p>
                                                        </div>
                                                    @endif
                                                </div>

                                            <!-- ================= FASE 6: SELESAI / DONE ================= -->
                                            @elseif(in_array($order->status, ['Paid', 'Returned', 'Resolved (Fine Paid)']))
                                                <a href="{{ route('student.document.invoice', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-gray-900 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-sm mb-2">
                                                    Lihat Invoice
                                                </a>
                                                <a href="{{ route('student.document.kwitansi', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-emerald-50 text-emerald-700 border border-emerald-200 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition shadow-sm mb-2">
                                                    Kwitansi Lunas SC
                                                </a>
                                                <!-- Jika ada denda yang sudah diselesaikan -->
                                                @if($order->status === 'Resolved (Fine Paid)' && $order->ba_total_fine)
                                                <a href="{{ route('student.document.berita-acara', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-red-50 text-red-700 border border-red-200 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition shadow-sm">
                                                    Lihat Berita Acara Denda
                                                </a>
                                                @endif

                                            <!-- ================= FASE MENUNGGU (PENDING / APPROVED) ================= -->
                                            @else
                                                <div class="py-2 px-3 bg-gray-50 rounded-xl text-center">
                                                    <p class="text-[10px] font-bold text-gray-400 italic">Menunggu update status dari Admin SC...</p>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 🔴 RIWAYAT MASA LALU (Past History) -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-400 tracking-tight border-b border-gray-100 pb-5 mb-8">Arsip Transaksi (Selesai / Batal / Ditolak)</h3>
                
                @if($pastLoans->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Belum ada riwayat arsip transaksi.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100 tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 rounded-l-2xl">No. Order</th>
                                    <th class="px-6 py-4">Tipe</th>
                                    <th class="px-6 py-4">Jumlah Barang</th>
                                    <th class="px-6 py-4">Total Tagihan</th>
                                    <th class="px-6 py-4 text-right rounded-r-2xl">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($pastLoans as $order)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 font-black text-gray-900">{{ $order->order_number }}</td>
                                        <td class="px-6 py-4 font-bold text-xs">{{ $order->order_type }}</td>
                                        <td class="px-6 py-4 font-bold text-xs">{{ $order->orderItems->sum('quantity') }} Unit</td>
                                        <td class="px-6 py-4 font-black text-indigo-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border {{ in_array($order->status, ['Returned', 'Handed Over', 'Resolved (Fine Paid)']) ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-red-100 text-red-800 border-red-200' }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-8 flex justify-center">
                        <div class="bg-white px-2 py-1 rounded-2xl shadow-sm border border-gray-100">
                            {{ $pastLoans->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>