<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ __('Create New Asset') }}
                </h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic">
                    Expanding the Student Council Inventory — Admin Suite
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
                
                <div class="lg:w-1/3 space-y-6">
                    <div class="bg-indigo-600 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500 rounded-full opacity-50"></div>
                        
                        <h3 class="text-xl font-black mb-4 relative z-10 tracking-tight">Inventory Protocol</h3>
                        <p class="text-indigo-100 text-xs leading-relaxed mb-8 relative z-10">
                           Make Sure Your Asset Submissions Are Spot-On! Follow these steps to keep our inventory pristine and rental-ready, mon cher.
                        </p>
                        
                        <ul class="space-y-4 relative z-10">
                            <li class="flex items-center text-[10px] font-bold uppercase tracking-widest">
                                <span class="w-6 h-6 rounded-full bg-indigo-400 flex items-center justify-center mr-3 shadow-sm text-[10px]">1</span>
                                Clear Item Naming
                            </li>
                            <li class="flex items-center text-[10px] font-bold uppercase tracking-widest">
                                <span class="w-6 h-6 rounded-full bg-indigo-400 flex items-center justify-center mr-3 shadow-sm text-[10px]">2</span>
                                Accurate Valuation
                            </li>
                            <li class="flex items-center text-[10px] font-bold uppercase tracking-widest">
                                <span class="w-6 h-6 rounded-full bg-indigo-400 flex items-center justify-center mr-3 shadow-sm text-[10px]">3</span>
                                High-Res Imagery
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white border-2 border-dashed border-gray-200 rounded-[2.5rem] p-2 h-72 flex items-center justify-center relative overflow-hidden shadow-inner group">
                        <img id="preview" class="hidden w-full h-full object-cover rounded-[2rem] transition-transform duration-700 group-hover:scale-105" src="">
                        <div id="placeholder" class="text-center">
                            <svg class="w-12 h-12 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[10px] text-gray-300 font-black uppercase tracking-[0.2em]">Live Photo Preview</p>
                        </div>
                    </div>
                </div>

                <div class="lg:w-2/3">
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                        <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                            @csrf

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Asset Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. ID Card" 
                                    class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-300 transition-all shadow-sm" required>
                                @error('name') <p class="text-red-500 text-[9px] font-black mt-2 uppercase ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Price (Rp)</label>
                                    <div class="relative">
                                        <input type="number" name="price" value="{{ old('price') }}" placeholder="150000" 
                                            class="w-full pl-6 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-200 transition-all shadow-sm" required>
                                    </div>
                                    @error('price') <p class="text-red-500 text-[9px] font-black mt-2 uppercase ml-1 tracking-tighter">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Stock Qty</label>
                                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity') }}" placeholder="10" 
                                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 placeholder-gray-200 transition-all shadow-sm" required>
                                    @error('stock_quantity') <p class="text-red-500 text-[9px] font-black mt-2 uppercase ml-1 tracking-tighter">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Initial Condition</label>
                                    <div class="relative">
                                        <select name="condition_status" 
                                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-800 appearance-none cursor-pointer shadow-sm">
                                            <option value="Good" {{ old('condition_status') == 'Good' ? 'selected' : '' }}>✨ Good / Ready</option>
                                            <option value="Broken" {{ old('condition_status') == 'Broken' ? 'selected' : '' }}>🛠️ Broken / Repair</option>
                                        </select>
                                        <div class="absolute right-6 top-5 pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                    @error('condition_status') <p class="text-red-500 text-[9px] font-black mt-2 uppercase ml-1 tracking-tighter">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Asset Imagery</label>
                                <div class="relative">
                                    <input type="file" name="item_photo" id="item_photo" onchange="previewImage(this)"
                                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-400 file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-indigo-600 file:text-white file:uppercase hover:file:bg-indigo-700 cursor-pointer transition-all shadow-sm" required>
                                </div>
                                @error('item_photo') <p class="text-red-500 text-[9px] font-black mt-2 uppercase ml-1 tracking-tighter">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-6">
                                <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.3em] hover:bg-indigo-700 shadow-2xl shadow-indigo-200 transition-all transform active:scale-95 flex items-center justify-center">
                                    Commit to Inventory
                                </button>
                                <p class="text-center text-[9px] text-gray-300 font-bold mt-6 uppercase tracking-[0.2em] italic">
                                    Admin Session — {{ date('Y') }}
                                </p>
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