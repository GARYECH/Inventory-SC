<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">
            {{ __('My Rental History & Loans') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-10"> <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm text-xs font-bold uppercase">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">Active Loans & Pending Requests</h3>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-full border border-indigo-100 uppercase">
                    Live Status
                </span>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mb-12">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] uppercase text-gray-500 font-black tracking-widest">
                                <th class="py-4 px-6">Receipt #</th>
                                <th class="py-4 px-6">Item Details</th>
                                <th class="py-4 px-6 text-center">Qty</th>
                                <th class="py-4 px-6">Rental Period</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50">
                            @forelse($activeLoans as $loan)
                                <tr class="hover:bg-gray-50/50 transition duration-200">
                                    <td class="py-4 px-6 font-mono text-xs">
                                        <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md font-bold border border-indigo-100">
                                            {{ $loan->order_number }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <img src="{{ asset('storage/' . $loan->item->item_photo) }}" class="w-10 h-10 rounded-lg object-cover mr-4 border border-gray-100 shadow-sm">
                                            <div>
                                                <span class="block font-black text-gray-900 leading-none">{{ $loan->item->name }}</span>
                                                <span class="text-[9px] text-gray-400 font-bold uppercase mt-1 inline-block">Asset ID: {{ $loan->item_id }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 font-black text-gray-600 text-center">{{ $loan->quantity }}</td>

                                    <td class="py-4 px-6">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-800 text-xs">{{ \Carbon\Carbon::parse($loan->start_date)->format('d M Y') }}</span>
                                            <span class="text-[9px] text-gray-400 uppercase font-black tracking-tighter">Until {{ \Carbon\Carbon::parse($loan->end_date)->format('d M Y') }}</span>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 text-center">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                                            {{ $loan->status === 'Pending' ? 'bg-yellow-50 text-yellow-600 border-yellow-200' : '' }}
                                            {{ $loan->status === 'Approved' ? 'bg-blue-50 text-blue-600 border-blue-200' : '' }}
                                            {{ $loan->status === 'Borrowed' ? 'bg-purple-50 text-purple-600 border-purple-200' : '' }}">
                                            {{ $loan->status }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 text-right">
                                        @if($loan->status === 'Pending')
                                            <form action="{{ route('student.rent.cancel', $loan) }}" method="POST" onsubmit="return confirm('Est-ce que tu es sûr, mon cher? Cancel this request?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition active:scale-95">
                                                    Cancel Order
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex flex-col items-end">
                                                <span class="text-[10px] text-gray-400 font-bold uppercase italic">In Progress</span>
                                                <span class="text-[9px] text-gray-300">Contact Admin to Modify</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <p class="text-gray-400 font-bold uppercase text-xs italic tracking-widest">No active business, mon cher.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-400">Past History & Archives</h3>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-50 opacity-75 grayscale-[50%] hover:grayscale-0 transition-all duration-500">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <tbody class="text-xs divide-y divide-gray-50">
                            @forelse($pastLoans as $history)
                                <tr class="bg-gray-50/30">
                                    <td class="py-4 px-6 font-mono text-gray-400">{{ $history->order_number }}</td>
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-gray-600">{{ $history->item->name }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-400">{{ $history->quantity }} unit(s)</td>
                                    <td class="py-4 px-6 text-gray-400">
                                        {{ \Carbon\Carbon::parse($history->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-400 rounded text-[9px] font-black uppercase tracking-widest">
                                            {{ $history->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-300 text-[10px] font-bold uppercase tracking-widest">No archives found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-white border-t border-gray-50">
                    {{ $pastLoans->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>