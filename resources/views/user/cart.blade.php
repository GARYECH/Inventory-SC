<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Cart & Checkout') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm font-bold border-l-4 border-red-500">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @if(empty($cart))
                <!-- JIKA KERANJANG KOSONG -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-10 text-center border border-gray-100">
                    <div class="text-gray-300 mb-4">
                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">Keranjangmu Masih Kosong!</h3>
                    <p class="text-gray-500 mb-6">Ayo pilih barang untuk proker atau kebutuhanmu.</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                        Kembali ke Katalog
                    </a>
                </div>
            @else
                
                @php 
                    // Cek tipe transaksi dari barang pertama untuk memunculkan/menyembunyikan kalender
                    $firstItem = reset($cart);
                    $isRental = $firstItem['transaction_type'] !== 'Sale';
                    $totalPrice = 0;
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- 🛒 BAGIAN KIRI: DAFTAR BARANG -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
                            <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-black text-lg text-gray-900">Rincian Barang ({{ $firstItem['transaction_type'] }})</h3>
                                <form action="{{ route('student.cart.clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 uppercase tracking-wider">
                                        Kosongkan Keranjang
                                    </button>
                                </form>
                            </div>
                            
                            <div class="p-6">
                                @foreach($cart as $id => $details)
                                    @php $totalPrice += $details['price'] * $details['quantity']; @endphp
                                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-50 last:border-0 last:mb-0 last:pb-0">
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $details['name'] }}</h4>
                                            <p class="text-sm text-gray-500">Harga Satuan: Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full border border-gray-200">
                                                Qty: {{ $details['quantity'] }}
                                            </span>
                                            <p class="font-black text-indigo-600 mt-2">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                                <span class="font-bold text-gray-500 uppercase tracking-wider text-sm">Grand Total</span>
                                <span class="font-black text-2xl text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 📝 BAGIAN KANAN: FORM CHECKOUT & SOP -->
                    <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 p-6">
                        <h3 class="font-black text-lg text-gray-900 mb-6">Formulir Pengajuan</h3>
                        
                        <form action="{{ route('student.cart.checkout') }}" method="POST">
                            @csrf
                            
                            <!-- Informasi Pemesan -->
                            <div class="space-y-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Proker / Event</label>
                                    <input type="text" name="proker_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Departemen (Cth: BEM / UKM)</label>
                                    <input type="text" name="department" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">No. WhatsApp</label>
                                    <input type="text" name="phone_number" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Bendahara</label>
                                    <input type="text" name="treasurer_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>

                            <!-- Kalender (Hanya muncul jika bukan Merchandise/Sale) -->
                            @if($isRental)
                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg mb-6 space-y-4">
                                <h4 class="font-bold text-blue-900 text-sm">Jadwal Peminjaman</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Start Date</label>
                                        <input type="date" name="start_date" required class="w-full rounded-md border-blue-200 shadow-sm focus:border-blue-500 text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">End Date</label>
                                        <input type="date" name="end_date" required class="w-full rounded-md border-blue-200 shadow-sm focus:border-blue-500 text-sm bg-white">
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- 🚨 SOP CHECKBOX (THE GATEKEEPER) 🚨 -->
                            <div class="mb-6 p-4 border-2 border-red-100 bg-red-50 rounded-lg">
                                <label class="flex items-start cursor-pointer">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" name="is_sop_accepted" value="1" required class="w-5 h-5 text-red-600 border-red-300 rounded focus:ring-red-500 cursor-pointer">
                                    </div>
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-red-900">Saya menyetujui SOP Student Council</span>
                                        <span class="block text-xs text-red-700 mt-1">Dengan mencentang ini, saya bertanggung jawab penuh atas barang yang dipinjam/dibeli dan mematuhi denda yang berlaku jika terjadi kerusakan atau keterlambatan.</span>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-xl font-black text-sm uppercase tracking-widest hover:bg-black transition shadow-lg active:scale-95">
                                Submit Order
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>