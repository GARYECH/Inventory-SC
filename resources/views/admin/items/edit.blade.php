<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ __('Modify Asset') }}
                </h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic">
                    Updating Vault Data — Admin Suite
                </p>
            </div>
            <a href="{{ route('admin.items.index') }}" class="group text-xs font-black text-gray-400 hover:text-indigo-600 transition uppercase tracking-tighter flex items-center">
                <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Abort & Return
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-10">
                
                <!-- KIRI: PROTOKOL PENJELASAN -->
                <div class="lg:w-1/3 space-y-6">
                    <div class="bg-indigo-600 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500 rounded-full opacity-50"></div>
                        
                        <h3 class="text-xl font-black mb-4 relative z-10 tracking-tight">Inventory Rules</h3>
                        <p class="text-indigo-100 text-xs leading-relaxed mb-8 relative z-10">
                           Pastikan Anda memilih tipe transaksi yang tepat agar sistem berjalan sesuai fungsinya.
                        </p>
                        
                        <div class="space-y-6 relative z-10">
                            <div>
                                <h4 class="text-[11px] font-black uppercase tracking-widest text-white flex items-center mb-1">
                                    <span class="w-5 h-5 rounded bg-indigo-400 flex items-center justify-center mr-2 shadow-sm text-[10px]">1</span>
                                    🏛️ Internal
                                </h4>
                                <p class="text-[10px] text-indigo-200 leading-relaxed ml-7">Barang milik kampus. Butuh MoU standar SC.</p>
                            </div>
                            <div>
                                <h4 class="text-[11px] font-black uppercase tracking-widest text-white flex items-center mb-1">
                                    <span class="w-5 h-5 rounded bg-amber-400 flex items-center justify-center mr-2 shadow-sm text-[10px] text-amber-900">2</span>
                                    🚚 External
                                </h4>
                                <p class="text-[10px] text-indigo-200 leading-relaxed ml-7">Barang sewaan pihak luar. Wajib MoU Vendor (Risiko tinggi).</p>
                            </div>
                            <div>
                                <h4 class="text-[11px] font-black uppercase tracking-widest text-white flex items-center mb-1">
                                    <span class="w-5 h-5 rounded bg-emerald-400 flex items-center justify-center mr-2 shadow-sm text-[10px] text-emerald-900">3</span>
                                    🛍️ Merchandise
                                </h4>
                                <p class="text-[10px] text-indigo-200 leading-relaxed ml-7">Beli putus. Tidak perlu MoU/tanggal kembali.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FOTO PREVIEW (Menampilkan foto lama jika ada) -->
                    <div class="bg-white border-2 border-dashed border-gray-200 rounded-[2.5rem] p-2 h-72 flex items-center justify-center relative overflow-hidden shadow-inner group">
                        <img id="preview" class="w-full h-full object-cover rounded-[2rem] transition-transform duration-700 group-hover:scale-105" src="{{ asset('storage/' . $item->item_photo) }}">
                        <div id="placeholder" class="hidden text-center">
                            <svg class="w-12 h-12 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[10px] text-gray-300 font-black uppercase tracking-[0.2em]">Live Photo Preview</p>
                        </div>
                    </div>
                </div>

                <!-- KANAN: FORM INPUT -->
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                        <!-- METHOD PUT UNTUK UPDATE -->
                        <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Asset Name</label>
                                    <input type="text" name="name" value="{{ old('name', $item->name) }}" 
                                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 transition-all shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Kategori Barang</label>
                                    <select name="category_id" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 cursor-pointer shadow-sm" required>
                                        <option value="" disabled>-- Pilih Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-indigo-50/50 rounded-[2rem] border border-indigo-100/50">
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-3 ml-1">Tipe Transaksi</label>
                                    <select name="transaction_type" class="w-full px-6 py-3 bg-white border-none rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-indigo-900 cursor-pointer shadow-sm" required>
                                        <option value="Internal Rental" {{ old('transaction_type', $item->transaction_type) == 'Internal Rental' ? 'selected' : '' }}>🏛️ Internal</option>
                                        <option value="Vendor Rental" {{ old('transaction_type', $item->transaction_type) == 'Vendor Rental' ? 'selected' : '' }}>🚚 External</option>
                                        <option value="Sale" {{ old('transaction_type', $item->transaction_type) == 'Sale' ? 'selected' : '' }}>🛍️ Merchandise</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-3 ml-1">Wajib Pakai MoU?</label>
                                    <select name="requires_mou" class="w-full px-6 py-3 bg-white border-none rounded-xl focus:ring-2 focus:ring-amber-500 font-bold text-gray-800 cursor-pointer shadow-sm" required>
                                        <option value="1" {{ old('requires_mou', $item->requires_mou) == '1' ? 'selected' : '' }}>📝 YA - Butuh MoU</option>
                                        <option value="0" {{ old('requires_mou', $item->requires_mou) == '0' ? 'selected' : '' }}>❌ TIDAK - Tanpa MoU</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Asset Description</label>
                                <textarea name="description" rows="3" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 transition-all shadow-sm" required>{{ old('description', $item->description) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Price (Rp)</label>
                                    <input type="number" name="price" value="{{ old('price', $item->price) }}" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 transition-all shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Stock Qty</label>
                                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $item->stock_quantity) }}" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 transition-all shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Current Condition</label>
                                    <select name="condition_status" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 cursor-pointer shadow-sm" required>
                                        <option value="Good" {{ old('condition_status', $item->condition_status) == 'Good' ? 'selected' : '' }}>✨ Good / Ready</option>
                                        <option value="Broken" {{ old('condition_status', $item->condition_status) == 'Broken' ? 'selected' : '' }}>🛠️ Broken / Repair</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Update Asset Imagery (Opsional)</label>
                                <input type="file" name="item_photo" id="item_photo" onchange="previewImage(this)"
                                    class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-400 file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-indigo-600 file:text-white file:uppercase hover:file:bg-indigo-700 cursor-pointer transition-all shadow-sm">
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.3em] hover:bg-indigo-600 shadow-xl shadow-gray-200 transition-all transform active:scale-95 flex items-center justify-center group">
                                    <svg class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Update Asset Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout> 