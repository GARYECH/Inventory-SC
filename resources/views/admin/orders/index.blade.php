<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Management (Ruang Tunggu Admin)') }}
            </h2>
            <a href="{{ route('admin.orders.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Search Bar -->
            <div class="bg-white p-4 shadow-sm sm:rounded-xl border border-gray-100 flex justify-between items-center">
                <form action="{{ route('admin.orders') }}" method="GET" class="w-full md:w-1/2 flex">
                    <input type="text" name="search" placeholder="Cari No. Order atau Nama Mahasiswa..." value="{{ request('search') }}" class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-r-md hover:bg-gray-700 text-sm font-bold">Cari</button>
                </form>
            </div>

            <!-- Pesan Sukses -->
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-l-4 border-green-500 font-bold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Daftar Kuitansi -->
            <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
                @if($orders->isEmpty())
                    <div class="p-10 text-center text-gray-500">
                        Belum ada kuitansi / order masuk.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-4">Data Kuitansi</th>
                                    <th class="px-4 py-4">Pemesan</th>
                                    <th class="px-4 py-4">Rincian Keranjang</th>
                                    <th class="px-4 py-4 text-center">Status & Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <!-- Kolom 1: Data Kuitansi -->
                                        <td class="px-4 py-4 align-top">
                                            <div class="font-black text-indigo-600 text-base mb-1">{{ $order->order_number }}</div>
                                            <span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 text-[10px] font-bold rounded uppercase">{{ $order->order_type }}</span>
                                            
                                            @if($order->order_type !== 'Sale')
                                                <div class="mt-2 text-xs font-bold text-blue-600">
                                                    🗓️ {{ $order->start_date->format('d M') }} - {{ $order->end_date->format('d M') }}
                                                </div>
                                            @endif
                                            <div class="mt-2 text-xs font-black text-gray-900">
                                                Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                            </div>
                                        </td>

                                        <!-- Kolom 2: Pemesan -->
                                        <td class="px-4 py-4 align-top">
                                            <div class="font-bold text-gray-900">{{ $order->user->name }}</div>
                                            <div class="text-xs text-gray-500 mt-1">Proker: <strong>{{ $order->proker_name }}</strong></div>
                                            <div class="text-xs text-gray-500">Dept: {{ $order->department }}</div>
                                            <div class="text-xs text-gray-500">WA: {{ $order->phone_number }}</div>
                                        </td>

                                        <!-- Kolom 3: Rincian Keranjang -->
                                        <td class="px-4 py-4 align-top">
                                            <ul class="space-y-1">
                                                @foreach($order->orderItems as $item)
                                                    <li class="text-xs flex justify-between bg-white p-1 border rounded shadow-sm">
                                                        <span class="font-semibold">{{ $item->item->name }}</span>
                                                        <span class="text-gray-500 font-bold ml-4">x{{ $item->quantity }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        <!-- Kolom 4: The Workflow Control -->
                                        <td class="px-4 py-4 align-top text-center bg-gray-50/50">
                                            <span class="inline-block w-full px-3 py-2 rounded text-xs font-bold uppercase tracking-wider border mb-3
                                                {{ $order->status === 'Pending Admin Review' ? 'bg-red-100 text-red-800 border-red-200' : '' }}
                                                {{ $order->status === 'Waiting for MoU' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : '' }}
                                                {{ $order->status === 'Ready for Handover' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' }}
                                                {{ $order->status === 'Returned' || $order->status === 'Paid' ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                                                {{ $order->status === 'Cancelled' ? 'bg-gray-200 text-gray-500 border-gray-300' : '' }}
                                            ">
                                                {{ $order->status }}
                                            </span>

                                            <!-- Form Ubah Status Manual (Admin Control) -->
                                            @if($order->status !== 'Returned' && $order->status !== 'Cancelled')
                                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="flex flex-col space-y-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    
                                                    <select name="status" class="text-xs rounded border-gray-300 shadow-sm focus:ring-indigo-500 py-1" required>
                                                        <option value="" disabled selected>-- Ubah Status --</option>
                                                        <option value="Waiting for MoU">Approve & Tunggu MoU</option>
                                                        <option value="Waiting for Payment">Approve & Tunggu Bayar</option>
                                                        <option value="Ready for Handover">Siap Diambil (Ready)</option>
                                                        <option value="Handed Over">Barang Sedang Dipinjam</option>
                                                        <option value="Paid">Lunas (Paid)</option>
                                                        <option value="Returned">Barang Dikembalikan</option>
                                                        <option value="Cancelled">Tolak / Cancel</option>
                                                    </select>
                                                    
                                                    <button type="submit" class="w-full bg-gray-800 text-white text-[10px] font-bold py-1.5 rounded uppercase hover:bg-gray-700 transition">
                                                        Update Status
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>