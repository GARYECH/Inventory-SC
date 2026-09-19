<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        File Too Large
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-[#f8f9fa]">

    <div class="flex min-h-screen items-center justify-center px-5 py-12">

        <div class="w-full max-w-md text-center">

            <!-- ICON -->

            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-red-50 text-red-500 shadow-sm">

                <svg
                    class="h-12 w-12"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M9 12h6m-6 4h6m-6-8h.01M12 3l9 18H3L12 3z"
                    />
                </svg>

            </div>


            <!-- CONTENT -->

            <div class="mt-7 rounded-[2rem] border border-gray-100 bg-white p-8 shadow-sm">

                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-red-500">
                    Error 413
                </p>

                <h1 class="mt-2 text-3xl font-black tracking-tight text-gray-950">
                    File Too Large
                </h1>

                <p class="mx-auto mt-3 max-w-sm text-sm font-medium leading-relaxed text-gray-500">
                    File atau request yang kamu kirim terlalu besar untuk diproses oleh sistem.
                </p>


                <!-- INFO -->

                <div class="mt-6 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-4 text-left">

                    <p class="text-[9px] font-black uppercase tracking-widest text-amber-700">
                        What to do
                    </p>

                    <p class="mt-1 text-xs font-bold leading-relaxed text-amber-800">
                        Coba gunakan file dengan ukuran yang lebih kecil,
                        lalu upload kembali.
                    </p>

                </div>


                <!-- ACTION -->

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">

                    <button
                        type="button"
                        onclick="history.back()"
                        class="flex-1 rounded-xl border border-gray-200 bg-white px-5 py-3 text-[9px] font-black uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                    >
                        Go Back
                    </button>


                    <a
                        href="{{ route('dashboard') }}"
                        class="flex-1 rounded-xl bg-gray-950 px-5 py-3 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-indigo-600"
                    >
                        Dashboard
                    </a>

                </div>

            </div>


            <!-- FOOTER -->

            <p class="mt-5 text-[9px] font-bold uppercase tracking-widest text-gray-300">
                Student Council Inventory System
            </p>

        </div>

    </div>

</body>

</html>
