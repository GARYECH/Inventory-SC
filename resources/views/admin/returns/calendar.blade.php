<x-app-layout>
    @php
        $statusClasses = [
            'Pending' => 'border-amber-200 bg-amber-50 text-amber-800',
            'Approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'Waiting for MoU' => 'border-purple-200 bg-purple-50 text-purple-800',
            'Pending Review MoU' => 'border-fuchsia-200 bg-fuchsia-50 text-fuchsia-800',
            'Waiting for Payment' => 'border-orange-200 bg-orange-50 text-orange-800',
            'Pending Review Payment' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
            'Waiting for Kwitansi' => 'border-pink-200 bg-pink-50 text-pink-800',
            'Pending Review Kwitansi' => 'border-rose-200 bg-rose-50 text-rose-800',
            'Handed Over' => 'border-teal-200 bg-teal-50 text-teal-800',
            'Pending Return Review' => 'border-cyan-200 bg-cyan-50 text-cyan-800',
            'Returned' => 'border-green-200 bg-green-50 text-green-800',
            'Returned (Damaged)' => 'border-red-200 bg-red-50 text-red-800',
            'Pending Review BA' => 'border-orange-200 bg-orange-50 text-orange-800',
            'Resolved (Fine Paid)' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'Rejected' => 'border-red-200 bg-red-50 text-red-800',
            'Cancelled' => 'border-gray-200 bg-gray-50 text-gray-600',
        ];
    @endphp

    <div class="min-h-screen bg-[#f8f9fa] pb-12">
        <div class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 shadow-sm backdrop-blur-xl">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-xl font-black tracking-tight text-gray-950 sm:text-2xl">Inventory Calendar and Timeline</h1>
                            <span class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-indigo-700">All statuses</span>
                        </div>
                        <p class="mt-1 text-xs font-medium text-gray-500">Pantau tanggal pengajuan, jadwal penggunaan, dan pengembalian untuk semua status transaksi.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.orders') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-gray-700 transition hover:bg-gray-50">Order Management</a>
                        <a href="{{ route('admin.orders.export') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-700">Export Excel</a>
                    </div>
                </div>
            </div>
        </div>

        <main class="mx-auto max-w-7xl space-y-6 px-4 pt-8 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-bold text-red-800">{{ session('error') }}</div>
            @endif

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Pending requests</p>
                    <p class="mt-2 text-3xl font-black text-amber-700">{{ $pendingCount }}</p>
                    <p class="mt-1 text-xs text-gray-500">Menunggu keputusan admin</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Return reviews</p>
                    <p class="mt-2 text-3xl font-black text-cyan-700">{{ $pendingReturnReviewCount }}</p>
                    <p class="mt-1 text-xs text-gray-500">Bukti pengembalian menunggu konfirmasi</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Overdue returns</p>
                    <p class="mt-2 text-3xl font-black text-red-700">{{ $overdueReturnCount }}</p>
                    <p class="mt-1 text-xs text-gray-500">Masih Handed Over melewati tanggal kembali</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Transaksi di bulan ini</p>
                    <p class="mt-2 text-3xl font-black text-indigo-700">{{ $monthEventOrderIds->count() }}</p>
                    <p class="mt-1 text-xs text-gray-500">Transaksi unik dengan aktivitas di kalender</p>
                </div>
            </section>

            <section class="rounded-3xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <form method="GET" action="{{ route('admin.returns.calendar') }}" class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:flex md:flex-1">
                        <div>
                            <label for="calendar-month" class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-gray-500">Bulan</label>
                            <input id="calendar-month" type="month" name="month" value="{{ $month->format('Y-m') }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-bold text-gray-800 focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                        </div>
                        <div>
                            <label for="calendar-status" class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-gray-500">Filter status saat ini</label>
                            <select id="calendar-status" name="status" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-bold text-gray-800 focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua status</option>
                                @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" {{ $statusFilter === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-indigo-700">Terapkan</button>
                        <a href="{{ route('admin.returns.calendar', ['month' => now()->format('Y-m')]) }}" class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-[9px] font-black uppercase tracking-widest text-gray-600 transition hover:bg-gray-50">Reset</a>
                    </div>
                </form>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">Calendar view</p>
                        <h2 class="mt-1 text-2xl font-black text-gray-950">{{ $monthLabel }}</h2>
                        <p class="mt-1 text-xs text-gray-500">Pilih tanggal untuk melihat detail transaksi dan tanggal yang terkait.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.returns.calendar', ['month' => $month->copy()->subMonth()->format('Y-m'), 'status' => $statusFilter]) }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50">← Sebelumnya</a>
                        <a href="{{ route('admin.returns.calendar', ['month' => $month->copy()->addMonth()->format('Y-m'), 'status' => $statusFilter]) }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50">Berikutnya →</a>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-x-4 gap-y-2 border-y border-gray-100 py-3 text-[10px] font-semibold text-gray-600">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Pending</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-indigo-500"></span>Jadwal penggunaan</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-cyan-500"></span>Bukti pengembalian</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-red-500"></span>Perlu perhatian</span>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <div class="min-w-[640px] overflow-hidden rounded-xl border border-gray-100">
                        <div class="grid grid-cols-7 bg-gray-50">
                            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                                <div class="border-b border-r border-gray-100 px-2 py-3 text-center text-[9px] font-black uppercase tracking-widest text-gray-500">{{ $weekday }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7">
                            @foreach($calendarWeeks as $week)
                                @foreach($week as $day)
                                    @php
                                        $dayKey = $day->toDateString();
                                        $dayEvents = $eventsByDate->get($dayKey, collect());
                                        $isCurrentMonth = $day->format('Y-m') === $month->format('Y-m');
                                        $isSelected = $dayKey === $selectedDate;
                                        $uniqueOrderCount = $dayEvents->pluck('order.id')->unique()->count();
                                    @endphp
                                    <a href="{{ route('admin.returns.calendar', ['month' => $month->format('Y-m'), 'date' => $dayKey, 'status' => $statusFilter]) }}#selected-date-queue" class="min-h-28 border-b border-r border-gray-100 p-1.5 transition sm:min-h-36 sm:p-2 {{ $isSelected ? 'bg-indigo-50 ring-2 ring-inset ring-indigo-500' : 'hover:bg-gray-50' }} {{ $isCurrentMonth ? '' : 'bg-gray-50/70' }}">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="flex h-6 w-6 items-center justify-center rounded-lg text-[10px] font-black {{ $isSelected ? 'bg-indigo-600 text-white' : ($isCurrentMonth ? 'text-gray-800' : 'text-gray-300') }}">{{ $day->format('j') }}</span>
                                            @if($uniqueOrderCount > 0)
                                                <span class="rounded-full bg-gray-900 px-1.5 py-0.5 text-[8px] font-black text-white">{{ $uniqueOrderCount }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-1.5 space-y-1">
                                            @foreach($dayEvents->take(3) as $event)
                                                @php
                                                    $eventOrder = $event['order'];
                                                    $eventClass = $statusClasses[$eventOrder->status] ?? 'border-gray-200 bg-gray-50 text-gray-700';
                                                @endphp
                                                <div title="{{ $event['label'] }} — {{ $eventOrder->order_number }} — Status saat ini: {{ $eventOrder->status }}" class="truncate rounded-md border px-1 py-1 text-[7px] font-bold leading-tight sm:text-[8px] {{ $eventClass }}">
                                                    <span class="block truncate">{{ $eventOrder->order_number }}</span>
                                                    <span class="block truncate font-semibold opacity-80">{{ $event['label'] }}</span>
                                                </div>
                                            @endforeach
                                            @if($dayEvents->count() > 3)
                                                <p class="px-1 text-[8px] font-bold text-gray-500">+{{ $dayEvents->count() - 3 }} aktivitas</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-[10px] leading-relaxed text-gray-500">Warna kartu menunjukkan status transaksi saat ini. Tanggal menunjukkan jenis aktivitasnya, seperti tanggal pengajuan, jadwal pemakaian, atau waktu bukti pengembalian dikirim.</p>
            </section>

            <section id="selected-date-queue" class="space-y-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">Detail aktivitas tanggal terpilih</p>
                        <h2 class="mt-1 text-xl font-black text-gray-950">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</h2>
                    </div>
                    <span class="w-fit rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-[10px] font-black text-indigo-700">{{ $selectedEntries->count() }} transaksi pada tanggal ini</span>
                </div>

                @forelse($selectedEntries as $entry)
                    @php
                        $order = $entry['order'];
                        $statusClass = $statusClasses[$order->status] ?? 'border-gray-200 bg-gray-50 text-gray-600';
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gray-50/70 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-black text-gray-950">{{ $order->order_number }}</h3>
                                    <span class="rounded-full border px-2.5 py-1 text-[9px] font-black {{ $statusClass }}">{{ $order->status }}</span>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-gray-600">{{ optional($order->user)->name ?? 'User tidak ditemukan' }} · {{ $order->organization ?? '-' }}</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach($entry['types'] as $eventType)
                                        <span class="rounded-md bg-indigo-50 px-2 py-1 text-[9px] font-bold text-indigo-700">{{ $eventType }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @if($order->return_drive_link)
                                <a href="{{ $order->return_drive_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-cyan-800 transition hover:bg-cyan-100">Buka Bukti Pengembalian</a>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 lg:grid-cols-2">
                            <div class="space-y-3">
                                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Tanggal transaksi</p>
                                    <p class="mt-1 text-xs font-bold text-gray-800">Diajukan: {{ optional($order->created_at)->format('d M Y H:i') ?? '-' }}</p>
                                    @if($order->start_date)
                                        <p class="mt-1 text-xs font-bold text-gray-800">Mulai: {{ $order->start_date->format('d M Y') }}{{ $order->start_time ? ' · ' . $order->start_time->format('H:i') : '' }}</p>
                                    @endif
                                    @if($order->end_date)
                                        <p class="mt-1 text-xs font-bold text-gray-800">Jadwal selesai/kembali: {{ $order->end_date->format('d M Y') }}{{ $order->end_time ? ' · ' . $order->end_time->format('H:i') : '' }}</p>
                                    @endif
                                    @if($order->return_submitted_at)
                                        <p class="mt-1 text-xs font-bold text-cyan-800">Bukti dikirim: {{ $order->return_submitted_at->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="mb-2 text-[9px] font-black uppercase tracking-widest text-gray-400">Daftar barang</p>
                                    <div class="space-y-2">
                                        @forelse($order->orderItems as $detail)
                                            <div class="flex items-start justify-between gap-3 rounded-xl border border-gray-100 bg-white px-3 py-2.5">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-black text-gray-900">{{ optional($detail->item)->name ?? 'Barang tidak ditemukan' }}</p>
                                                    <p class="mt-0.5 text-[10px] font-semibold text-gray-500">{{ optional(optional($detail->item)->category)->name ?? 'Tanpa kategori' }}</p>
                                                </div>
                                                <span class="shrink-0 rounded-lg bg-gray-50 px-2 py-1 text-[10px] font-black text-gray-700">{{ $detail->quantity }} unit</span>
                                            </div>
                                        @empty
                                            <p class="rounded-xl bg-gray-50 p-3 text-xs font-semibold text-gray-500">Detail barang belum tersedia.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="mb-2 text-[9px] font-black uppercase tracking-widest text-gray-400">Tindakan</p>
                                <p class="mb-3 text-xs leading-relaxed text-gray-600">Status pada kartu adalah status transaksi saat ini, bukan riwayat status pada tanggal terpilih.</p>
                                @if($order->status === 'Pending Return Review')
                                    <div class="space-y-2">
                                        <p class="text-xs font-semibold text-cyan-800">Bukti pengembalian menunggu pemeriksaan admin.</p>
                                        <form action="{{ route('admin.orders.confirm-return', $order->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian ini dalam kondisi baik?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="condition" value="good">
                                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-3 py-3 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-emerald-700">Confirm Return · Good</button>
                                        </form>
                                        <form action="{{ route('admin.orders.confirm-return', $order->id) }}" method="POST" onsubmit="return confirm('Tandai pengembalian rusak dan lanjutkan proses Berita Acara?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="condition" value="damaged">
                                            <button type="submit" class="w-full rounded-xl bg-red-600 px-3 py-3 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-red-700">Confirm Return · Damaged</button>
                                        </form>
                                    </div>
                                @elseif($order->status === 'Pending')
                                    <p class="rounded-xl border border-amber-100 bg-amber-50 p-3 text-xs font-semibold text-amber-800">Transaksi ini menunggu keputusan admin. Buka Order Management untuk meninjau dan memprosesnya.</p>
                                    <a href="{{ route('admin.orders', ['search' => $order->order_number]) }}" class="mt-2 inline-flex rounded-xl border border-gray-200 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50">Buka transaksi</a>
                                @else
                                    <a href="{{ route('admin.orders', ['search' => $order->order_number]) }}" class="inline-flex rounded-xl border border-gray-200 px-4 py-2.5 text-[9px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50">Lihat di Order Management</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                        <h3 class="text-sm font-black text-gray-900">Tidak ada aktivitas pada tanggal ini</h3>
                        <p class="mt-1 text-xs font-medium text-gray-500">Pilih tanggal lain atau ubah filter status untuk melihat transaksi yang sesuai.</p>
                    </div>
                @endforelse
            </section>

            <div class="rounded-2xl border border-amber-100 bg-amber-50/70 px-5 py-4 text-xs leading-relaxed text-amber-900">
                <strong>Catatan transparansi:</strong> kalender menampilkan status transaksi saat ini serta tanggal aktivitas yang tercatat. Sistem belum menyimpan riwayat setiap perubahan status, jadi kalender belum bisa menunjukkan tanggal persis ketika sebuah order berpindah dari Pending ke Approved atau status lainnya.
            </div>
        </main>
    </div>
</x-app-layout>
