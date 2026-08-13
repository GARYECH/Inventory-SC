<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">System Categories</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic">Master Data Management</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-8">
            
            <!-- FORM TAMBAH KATEGORI KIRI -->
            <div class="md:w-1/3">
                <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-200">
                    <h3 class="text-lg font-black mb-6">Tambah Kategori</h3>
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-2">Nama Kategori</label>
                            <input type="text" name="name" placeholder="e.g. Alat Tulis" required
                                class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-xl focus:ring-2 focus:ring-white font-bold text-white placeholder-indigo-300">
                            @error('name') <span class="text-[9px] text-red-300 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full bg-white text-indigo-900 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-50 transition-all">
                            Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>

            <!-- LIST KATEGORI KANAN -->
            <div class="md:w-2/3">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-100">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 text-xs font-bold rounded-xl border border-red-100">{{ session('error') }}</div>
                @endif

                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="pb-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Nama Kategori</th>
                                <th class="pb-4 text-[10px] font-black uppercase text-gray-400 tracking-widest text-center">Total Barang</th>
                                <th class="pb-4 text-[10px] font-black uppercase text-gray-400 tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-4 font-bold text-gray-900 text-sm">{{ $category->name }}</td>
                                <td class="py-4 text-center">
                                    <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-black">{{ $category->items_count }} Item</span>
                                </td>
                                <td class="py-4 text-right">
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-black uppercase text-red-500 hover:text-red-700 bg-red-50 px-3 py-2 rounded-xl transition-all">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>