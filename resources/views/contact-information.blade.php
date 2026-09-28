<x-app-layout>

    <!-- ========================================================= -->
    <!-- PAGE HEADER -->
    <!-- ========================================================= -->

    <x-slot name="header">

        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="text-2xl font-black tracking-tight text-gray-950">
                    Contact Information
                </h2>

                <p class="mt-1 text-[10px] font-black uppercase tracking-[0.18em] text-gray-500">
                    Need Help? Get in Touch
                </p>

            </div>

        </div>

    </x-slot>


    <!-- ========================================================= -->
    <!-- CONTENT -->
    <!-- ========================================================= -->

    <div class="py-10 sm:py-12">

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">


            <!-- ===================================================== -->
            <!-- INTRO -->
            <!-- ===================================================== -->

            <div class="mb-7">

                <p class="max-w-2xl text-sm font-semibold leading-relaxed text-gray-600">
                    Hubungi pengelola Inventory Student Council apabila
                    kamu membutuhkan bantuan terkait transaksi, peminjaman
                    barang, pembatalan order, atau memiliki pertanyaan
                    mengenai sistem inventory.
                </p>

            </div>



            <!-- ===================================================== -->
            <!-- MAIN CARD -->
            <!-- ===================================================== -->

            <div class="overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-sm">


                <!-- ================================================= -->
                <!-- DARK HEADER -->
                <!-- ================================================= -->

                <div class="bg-gray-950 px-6 py-8 sm:px-8">

                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">


                        <!-- PROFILE -->

                        <div class="flex items-center gap-4">

                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-xl font-black text-white shadow-lg">
                                G
                            </div>

                            <div>

                                <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-300">
                                    Inventory Student Council
                                </p>

                                <h3 class="mt-1 text-2xl font-black text-white">
                                    Gregory Edgard Christian
                                </h3>

                                <p class="mt-1 text-xs font-bold text-gray-400">
                                    Treasurer of Student Council
                                </p>

                            </div>

                        </div>


                        <!-- CONTACT LABEL -->

                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">

                            <p class="text-[8px] font-black uppercase tracking-widest text-gray-500">
                                Contact
                            </p>

                            <p class="mt-1 text-sm font-black text-white">
                                +62 813-3222-7372
                            </p>

                        </div>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- CONTACT BODY -->
                <!-- ================================================= -->

                <div class="grid grid-cols-1 gap-6 p-6 sm:p-8 lg:grid-cols-2">


                    <!-- ================================================= -->
                    <!-- CONTACT DETAIL -->
                    <!-- ================================================= -->

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-600">
                            Contact Details
                        </p>

                        <h3 class="mt-1 text-xl font-black text-gray-950">
                            Gregory Edgard Christian
                        </h3>

                        <div class="mt-5 space-y-3">


                            <!-- PHONE -->

                            <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.02 3.059a1 1 0 01-.217 1.023L8.77 8.98a16.001 16.001 0 006.25 6.25l1.214-1.261a1 1 0 011.023-.217l3.059 1.02A1 1 0 0121 15.72V19a2 2 0 01-2 2h-1C9.163 21 3 14.837 3 7V5z"
                                        />

                                    </svg>

                                </div>

                                <div class="min-w-0">

                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                        Phone / WhatsApp
                                    </p>

                                    <p class="mt-0.5 text-sm font-black text-gray-900">
                                        +62 813-3222-7372
                                    </p>

                                </div>

                            </div>



                            <!-- POSITION -->

                            <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 14l9-5-9-5-9 5 9 5z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 14l6.16-3.422A12.083 12.083 0 0118 14.5c0 3.038-2.686 5.5-6 5.5s-6-2.462-6-5.5c0-.688.16-1.344.447-1.922L12 14z"
                                        />

                                    </svg>

                                </div>

                                <div class="min-w-0">

                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">
                                        Position
                                    </p>

                                    <p class="mt-0.5 text-sm font-black text-gray-900">
                                        Treasurer of Student Council
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- ================================================= -->
                    <!-- WHEN TO CONTACT -->
                    <!-- ================================================= -->

                    <div>

                        <p class="text-[9px] font-black uppercase tracking-[0.18em] text-indigo-600">
                            When to Contact
                        </p>

                        <h3 class="mt-1 text-xl font-black text-gray-950">
                            Need assistance?
                        </h3>


                        <div class="mt-5 space-y-3">


                            <!-- CANCEL ORDER -->

                            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-4">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-600 text-white">

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />

                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-xs font-black text-red-900">
                                            Cancel Order
                                        </p>

                                        <p class="mt-1 text-[10px] font-semibold leading-relaxed text-red-700">
                                            Hubungi kontak ini apabila kamu ingin
                                            membatalkan transaksi yang sudah dibuat.
                                        </p>

                                    </div>

                                </div>

                            </div>



                            <!-- ASK QUESTION -->

                            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-4">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white">

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.228 9c.549-1.165 1.9-2 3.772-2 2.21 0 4 1.343 4 3 0 1.31-.98 2.455-2.423 2.831-.602.157-1.577.469-1.577 1.169V15m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />

                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-xs font-black text-indigo-900">
                                            Ask a Question
                                        </p>

                                        <p class="mt-1 text-[10px] font-semibold leading-relaxed text-indigo-700">
                                            Tanyakan hal apa pun mengenai
                                            peminjaman, transaksi, atau sistem inventory.
                                        </p>

                                    </div>

                                </div>

                            </div>



                            <!-- OTHER HELP -->

                            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-white">

                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                                            />

                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-xs font-black text-gray-900">
                                            Other Assistance
                                        </p>

                                        <p class="mt-1 text-[10px] font-semibold leading-relaxed text-gray-600">
                                            Hubungi apabila membutuhkan bantuan
                                            lain yang berkaitan dengan Inventory Student Council.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- WHATSAPP BUTTON -->
                <!-- ================================================= -->

                <div class="border-t border-gray-100 bg-gray-50 px-6 py-5 sm:px-8">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-xs font-black text-gray-900">
                                Need to get in touch?
                            </p>

                            <p class="mt-1 text-[10px] font-semibold text-gray-500">
                                Untuk respon yang lebih cepat, kamu dapat menghubungi melalui WhatsApp.
                            </p>

                        </div>


                        <a
                            href="https://wa.me/6281332227372"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-5 py-3 text-[9px] font-black uppercase tracking-[0.16em] text-white transition hover:bg-emerald-600"
                        >

                            <svg
                                class="h-4 w-4"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.198.297-.767.967-.94 1.164-.173.198-.347.223-.644.075-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.611-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.848 1.213 3.045.149.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.718 2.006-1.412.248-.694.248-1.289.173-1.412-.074-.124-.272-.198-.57-.347z"
                                />

                                <path
                                    fill-rule="evenodd"
                                    d="M20.52 3.48A11.947 11.947 0 0012.01 0C5.403 0 .013 5.39.013 12c0 2.115.552 4.179 1.6 6L0 24l6.16-1.614A11.94 11.94 0 0012.01 24h.005C18.615 24 23.997 18.61 24 12a11.95 11.95 0 00-3.48-8.52zM12.014 22.003h-.004a9.946 9.946 0 01-5.07-1.378l-.364-.216-3.656.957.976-3.565-.236-.365a9.96 9.96 0 01-1.525-5.27c0-5.48 4.462-9.938 9.95-9.938a9.95 9.95 0 017.04 2.92A9.95 9.95 0 0122.014 12c-.003 5.484-4.463 9.95-10 9.95z"
                                    clip-rule="evenodd"
                                />

                            </svg>

                            Contact via WhatsApp

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>