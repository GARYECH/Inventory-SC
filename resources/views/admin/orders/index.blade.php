<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Rental Request Management') }}</h2>
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.orders') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Search student/order..." class="pl-10 pr-4 py-2 border-gray-200 rounded-lg text-xs focus:ring-indigo-500 w-64" value="{{ request('search') }}">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>
                <a href="{{ route('admin.orders.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition">Export Excel</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 text-sm">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 font-bold text-gray-500 uppercase text-[10px] tracking-widest">
                            <th class="p-4">Order Ref.</th>
                            <th class="p-4">Student Info</th>
                            <th class="p-4">Item & Qty</th>
                            <th class="p-4">Rental Period</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Workflow</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-4 font-mono text-indigo-600 font-bold">{{ $order->order_number }}</td>
                            <td class="p-4"><span class="font-bold text-gray-900">{{ $order->user->name }}</span><br><span class="text-[10px] text-gray-400">{{ $order->user->email }}</span></td>
                            <td class="p-4"><span class="font-bold text-gray-700">{{ $order->item->name }}</span> ({{ $order->quantity }}x)</td>
                            <td class="p-4">{{ \Carbon\Carbon::parse($order->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($order->end_date)->format('d M Y') }}</td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase border
                                    {{ $order->status === 'Pending' ? 'bg-yellow-50 text-yellow-600 border-yellow-200' : '' }}
                                    {{ $order->status === 'Approved' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}
                                    {{ $order->status === 'Borrowed' ? 'bg-purple-50 text-purple-600 border-purple-200' : '' }}
                                    {{ $order->status === 'Returned' ? 'bg-green-50 text-green-600 border-green-200' : '' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                    @csrf @method('PATCH')
                                    @if($order->status === 'Pending')
                                        <input type="hidden" name="status" value="Approved">
                                        <button class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold">Approve</button>
                                    @elseif($order->status === 'Approved')
                                        <input type="hidden" name="status" value="Borrowed">
                                        <button class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold">Hand Over</button>
                                    @elseif($order->status === 'Borrowed')
                                        <input type="hidden" name="status" value="Returned">
                                        <button class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold animate-pulse">Mark Returned</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>