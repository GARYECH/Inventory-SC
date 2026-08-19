<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        
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
                <div class="px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 shadow-sm"><div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div><p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p></div>
            @endif
            @if(session('error'))
                <div class="px-6 py-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 shadow-sm"><div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-red-200"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div><p class="text-sm font-bold text-red-800">{{ session('error') }}</p></div>
            @endif

            <!-- 🟢 TRANSAKSI AKTIF -->
            <div class="bg-white p-6 md:p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 pb-5 mb-8">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Transaksi Aktif & Menunggu Persetujuan</h3>
                    <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl">Real-time Status</span>
                </div>
                
                @if($activeLoans->isEmpty())
                    <div class="text-center py-16"><p class="text-gray-400 font-bold text-sm uppercase tracking-widest">Belum ada transaksi aktif saat ini.</p></div>
                @else
                    <div class="space-y-8">
                        @foreach($activeLoans as $order)
                            @php
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
                                ];
                                $badgeClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp

                            <!-- THE ORDER CARD -->
                            <div class="border border-gray-100 rounded-[2.5rem] p-6 md:p-8 bg-gray-50/50 hover:bg-white hover:shadow-xl transition-all duration-300">
                                
                                <!-- 🌟 HEADER INFO -->
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-6 mb-8 gap-4">
                                    <div>
                                        <h4 class="font-black text-indigo-600 text-2xl tracking-tight mb-2">{{ $order->order_number }}</h4>
                                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-gray-500">
                                            <span>Proker: <span class="text-gray-900">{{ $order->proker_name }}</span></span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full mx-1"></span>
                                            <span>Org: <span class="text-gray-900">{{ $order->organization }}</span></span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full mx-1"></span>
                                            <span>Tipe: <span class="text-gray-900">{{ $order->order_type }}</span></span>
                                        </div>
                                        @if($order->order_type !== 'Sale' && $order->start_date)
                                            <div class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-xl text-[11px] font-black text-indigo-600 uppercase tracking-widest">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Jadwal: {{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($order->end_date)->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="md:text-right mt-2 md:mt-0">
                                        <span class="px-5 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest border {{ $badgeClass }} shadow-sm inline-block">
                                            {{ $order->status === 'Waiting for Kwitansi' ? 'Paid / Tunggu Kwitansi' : $order->status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                                    
                                    <!-- 🌟 KIRI: RINCIAN BARANG -->
                                    <div class="lg:col-span-7 flex flex-col">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Rincian Barang</p>
                                        
                                        <div class="space-y-4 flex-grow">
                                            @foreach($order->orderItems as $detail)
                                                @php
                                                    // 🌟 LOGIKA ANTI BOCOR (SUPER KETAT) 🌟
                                                    // 1. Cek secara eksplisit nama organisasinya
                                                    $isSC = $order->organization === 'Student Council';
                                                    $isInternal = str_contains($detail->item->transaction_type, 'Internal');
                                                    $isFree = $isSC && $isInternal;
                                                    
                                                    // 2. Ambil harga asli. Kalau dari pivot error/0, tembak langsung ke tabel master Item.
                                                    $basePrice = ($detail->price && $detail->price > 0) ? $detail->price : $detail->item->price;
                                                    
                                                    // 3. Kalkulasi Akhir
                                                    $actualPrice = $isFree ? 0 : $basePrice;
                                                    $subtotal = $actualPrice * $detail->quantity;
                                                @endphp

                                                <div class="flex flex-col sm:flex-row justify-between sm:items-center bg-white p-5 border border-gray-100 rounded-2xl shadow-sm gap-4">
                                                    
                                                    <div class="flex items-center gap-5">
                                                        <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-center justify-center shrink-0">
                                                            <span class="font-black text-xl text-indigo-600">{{ $detail->quantity }}<span class="text-xs text-indigo-400 ml-0.5">x</span></span>
                                                        </div>
                                                        <div>
                                                            <span class="block font-black text-gray-900 text-lg leading-tight mb-1.5">{{ $detail->item->name }}</span>
                                                            
                                                            @if($isFree)
                                                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-emerald-100 inline-block">Free (SC Internal)</span>
                                                            @else
                                                                <span class="block text-xs font-bold text-gray-500">Harga Satuan: Rp {{ number_format($actualPrice, 0, ',', '.') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="sm:text-right pl-[4.5rem] sm:pl-0">
                                                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Subtotal</span>
                                                        <span class="block font-black text-indigo-600 text-xl leading-none">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <!-- 🌟 KANAN: TOTAL & ACTION BOX -->
                                    <div class="lg:col-span-5 flex flex-col gap-5">
                                        <!-- GRAND TOTAL -->
                                        <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl shadow-gray-200 flex justify-between items-center">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Grand Total</p>
                                            <p class="text-3xl font-black tracking-tight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                        </div>
                                        
                                        <!-- ACTION BOX -->
                                        <div class="bg-white p-6 border border-gray-100 rounded-2xl shadow-sm flex-grow">
                                            
                                            <!-- ================= FASE 1: MOU ================= -->
                                            @if($order->status === 'Waiting for MoU' || $order->status === 'Pending Review MoU')
                                                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-4">📄 Tahap 1: Surat MoU</p>
                                                
                                                <a href="{{ route('student.document.mou', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-indigo-50 text-indigo-700 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 mb-4 shadow-sm">
                                                    📥 Download MoU Kosong
                                                </a>
                                                
                                                @if($order->status === 'Waiting for MoU')
                                                    <form action="{{ route('student.orders.upload-mou', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="file" name="signed_mou" accept=".pdf,.jpg,.png" required class="block w-full text-[10px] mb-3 cursor-pointer font-bold text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                                        <button type="submit" class="w-full py-3.5 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-sm">Upload MoU TTD</button>
                                                    </form>
                                                @else
                                                    <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-200"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">MoU Sedang Direview Admin</p></div>
                                                @endif

                                            <!-- ================= FASE 2: PAYMENT ================= -->
                                            @elseif($order->status === 'Waiting for Payment' || $order->status === 'Pending Review Payment')
                                                <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-4">💳 Tahap 2: Pembayaran</p>
                                                
                                                <a href="{{ route('student.document.invoice', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-orange-50 text-orange-700 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-100 mb-4 shadow-sm">
                                                    📥 Download Invoice
                                                </a>
                                                
                                                @if($order->status === 'Waiting for Payment')
                                                    <form action="{{ route('student.orders.upload-payment', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="file" name="payment_receipt" accept=".pdf,.jpg,.png" required class="block w-full text-[10px] mb-3 cursor-pointer font-bold text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                                        <button type="submit" class="w-full py-3.5 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 transition shadow-sm">Upload Bukti Transfer</button>
                                                    </form>
                                                @else
                                                    <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-200"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bukti Transfer Direview Admin</p></div>
                                                @endif

                                            <!-- ================= FASE 3: PAID / KWITANSI ================= -->
                                            @elseif($order->status === 'Waiting for Kwitansi' || $order->status === 'Pending Review Kwitansi')
                                                
                                                @if($order->status === 'Waiting for Kwitansi')
                                                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 shadow-sm">
                                                    <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest">Pembayaran Approved!</p>
                                                </div>
                                                @endif

                                                <p class="text-[10px] font-black text-pink-600 uppercase tracking-widest mb-1">🧾 Tahap 3: Kwitansi Resmi</p>
                                                <p class="text-[10px] text-gray-500 mb-4 font-bold">Download, tandatangani, dan upload ulang.</p>

                                                <a href="{{ route('student.document.kwitansi', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-pink-50 text-pink-700 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-pink-100 mb-4 shadow-sm">
                                                    📥 Download Kwitansi
                                                </a>
                                                
                                                @if($order->status === 'Waiting for Kwitansi')
                                                    <form action="{{ route('student.orders.upload-kwitansi', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="file" name="signed_kwitansi" accept=".pdf,.jpg,.png" required class="block w-full text-[10px] mb-3 cursor-pointer font-bold text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                                        <button type="submit" class="w-full py-3.5 bg-pink-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-pink-700 transition shadow-sm">Upload Kwitansi TTD</button>
                                                    </form>
                                                @else
                                                    <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-200"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kwitansi TTD Direview Admin</p></div>
                                                @endif

                                            <!-- ================= FASE 4: HANDED OVER ================= -->
                                            @elseif($order->status === 'Handed Over' || $order->status === 'Pending Return Review')
                                                <p class="text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-1">📦 Tahap 4: Pengembalian Barang</p>
                                                <p class="text-[10px] text-gray-500 mb-4 font-bold">Masukkan Link Drive foto aset sebelum dikembalikan.</p>
                                                
                                                @if($order->status === 'Handed Over')
                                                    <form action="{{ route('student.orders.return-link', $order->id) }}" method="POST">
                                                        @csrf
                                                        <input type="url" name="return_drive_link" placeholder="https://drive.google.com/..." required class="w-full px-4 py-3.5 border border-gray-200 bg-gray-50 rounded-xl text-xs mb-3 font-bold text-gray-800 outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                                                        <button type="submit" class="w-full bg-cyan-600 text-white py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-700 transition shadow-sm">Submit Bukti Return</button>
                                                    </form>
                                                @else
                                                    <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-200"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Drive Terkirim. Admin Cek Fisik.</p></div>
                                                @endif

                                            <!-- ================= FASE 5: BARANG RUSAK ================= -->
                                            @elseif($order->status === 'Returned (Damaged)' || $order->status === 'Pending Review BA')
                                                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1">⚠️ Tahap 5: Penyelesaian Denda</p>
                                                
                                                @if($order->ba_total_fine)
                                                    <p class="text-[10px] text-gray-500 mb-4 font-bold">Download BA, TTD, gabung Bukti TF Denda jadi 1 PDF.</p>
                                                    <a href="{{ route('student.document.berita-acara', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-red-50 text-red-600 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-100 mb-4 shadow-sm">
                                                        📥 Download Berita Acara
                                                    </a>
                                                    
                                                    @if($order->status === 'Returned (Damaged)')
                                                        <form action="{{ route('student.orders.upload-ba', $order->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="file" name="signed_ba_file" accept=".pdf" required class="block w-full text-[10px] mb-3 cursor-pointer font-bold text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-200">
                                                            <button type="submit" class="w-full bg-gray-900 text-white py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-800 transition shadow-sm">Upload Penyelesaian</button>
                                                        </form>
                                                    @else
                                                        <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-200"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dokumen Denda Direview Admin</p></div>
                                                    @endif
                                                @else
                                                    <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-200 mt-4"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Menunggu Rincian dari Admin</p></div>
                                                @endif

                                            <!-- ================= FASE 6: SELESAI ================= -->
                                            @elseif(in_array($order->status, ['Returned', 'Resolved (Fine Paid)']))
                                                <div class="p-4 bg-emerald-50 rounded-xl text-center border border-emerald-100 mb-3">
                                                    <p class="text-[11px] font-black text-emerald-600 uppercase tracking-widest">✅ Transaksi Selesai</p>
                                                </div>
                                                <a href="{{ route('student.document.invoice', $order->id) }}" target="_blank" class="w-full inline-flex justify-center items-center bg-gray-900 text-white py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition shadow-sm">
                                                    Lihat Invoice Terakhir
                                                </a>

                                            @else
                                                <div class="py-6 px-4 text-center">
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Menunggu pembaruan status...</p>
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

            <!-- 🔴 RIWAYAT MASA LALU (Arsip) -->
            <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-xl font-black text-gray-900 tracking-tight border-b border-gray-100 pb-5 mb-8">Arsip Transaksi (Selesai / Batal / Ditolak)</h3>
                
                @if($pastLoans->isEmpty())
                    <div class="text-center py-12"><p class="text-gray-400 font-bold text-sm uppercase tracking-widest">Belum ada riwayat arsip transaksi.</p></div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100 tracking-wider">
                                <tr>
                                    <th class="px-6 py-5 rounded-l-2xl">No. Order</th>
                                    <th class="px-6 py-5">Total Tagihan</th>
                                    <th class="px-6 py-5 text-right rounded-r-2xl">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($pastLoans as $order)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-5">
                                            <span class="block font-black text-gray-900 text-base">{{ $order->order_number }}</span>
                                            <span class="block text-xs font-bold text-gray-400 mt-1">{{ $order->proker_name }}</span>
                                        </td>
                                        <td class="px-6 py-5 font-black text-indigo-600 text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-5 text-right">
                                            <span class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl border {{ in_array($order->status, ['Returned', 'Resolved (Fine Paid)']) ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100' }}">
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