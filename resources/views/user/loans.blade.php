<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat & Status Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- 🟢 TRANSAKSI AKTIF (Active Loans) -->
            <div class="bg-white p-6 shadow-sm sm:rounded-xl border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 border-b pb-4 mb-6">Transaksi Aktif & Menunggu Persetujuan</h3>
                
                @if($activeLoans->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-gray-500 text-sm">Belum ada transaksi yang sedang berjalan.</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($activeLoans as $order)
                            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 hover:shadow-md transition duration-200">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-4 mb-4">
                                    <div>
                                        <h4 class="font-black text-indigo-700 text-lg">{{ $order->order_number }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Proker: <strong>{{ $order->proker_name }}</strong> | Tipe: <strong>{{ $order->order_type }}</strong>
                                        </p>
                                        @if($order->order_type !== 'Sale')
                                            <p class="text-xs font-bold text-blue-600 mt-1">
                                                🗓️ Jadwal: {{ $order->start_date->format('d M Y') }} - {{ $order->end_date->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="mt-4 md:mt-0 text-right">
                                        <!-- Indikator Status Dinamis -->
                                        <span class="inline-block px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="col-span-2">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rincian Barang (Keranjang):</p>
                                        <ul class="space-y-2">
                                            @foreach($order->orderItems as $detail)
                                                <li class="flex justify-between items-center bg-white p-2 border rounded-md text-sm">
                                                    <span class="font-semibold text-gray-800">{{ $detail->item->name }} <span class="text-gray-400">x{{ $detail->quantity }}</span></span>
                                                    <span class="text-gray-600">Rp {{ number_format($detail->subtotal_price, 0, ',', '.') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    
                                    <div class="flex flex-col justify-end items-end bg-white p-4 border rounded-lg shadow-sm">
                                        <p class="text-xs font-bold text-gray-500 uppercase">Grand Total</p>
                                        <p class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                        
                                        <!-- THE MAGIC BUTTONS (PDF) -->
                                        <div class="mt-4 w-full flex flex-col space-y-2">
                                            @if($order->status === 'Waiting for MoU')
                                                <a href="{{ route('student.document.mou', $order->id) }}" target="_blank" class="w-full inline-block text-center bg-indigo-600 text-white py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-indigo-700 transition shadow">
                                                    📄 Download MoU
                                                </a>
                                                <p class="text-[10px] text-gray-500 text-center leading-tight">Print, tanda tangani, dan bawa fisik MoU ini saat mengambil barang di SC.</p>
                                            @elseif($order->status === 'Paid' || $order->status === 'Returned' || $order->status === 'Handed Over')
                                                <a href="{{ route('student.document.invoice', $order->id) }}" target="_blank" class="w-full inline-block text-center bg-gray-800 text-white py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-gray-700 transition shadow">
                                                    🧾 Download Invoice
                                                </a>
                                            @else
                                                <p class="text-[10px] text-center text-gray-400 italic">Menunggu update status dari Admin SC...</p>
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
            <div class="bg-white p-6 shadow-sm sm:rounded-xl border border-gray-100">
                <h3 class="text-lg font-black text-gray-900 border-b pb-4 mb-6 text-gray-400">Arsip Transaksi (Selesai/Batal)</h3>
                
                @if($pastLoans->isEmpty())
                    <p class="text-gray-400 text-sm italic">Belum ada riwayat transaksi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3">No. Order</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Item Count</th>
                                    <th class="px-4 py-3">Total Tagihan</th>
                                    <th class="px-4 py-3 text-right">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pastLoans as $order)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-bold text-gray-900">{{ $order->order_number }}</td>
                                        <td class="px-4 py-3">{{ $order->order_type }}</td>
                                        <td class="px-4 py-3">{{ $order->orderItems->sum('quantity') }} Barang</td>
                                        <td class="px-4 py-3">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-bold {{ $order->status === 'Returned' || $order->status === 'Handed Over' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $order->status }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pastLoans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>