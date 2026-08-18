<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SCIS | Kabinet Keong</title>
    
    <!-- Google Fonts: Playfair Display (Heading) & Poppins (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'], 
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        keong: {
                            navy: '#13113C',   
                            teal: '#8AD1C3',   
                            peach: '#E5A87B',  
                            cream: '#F6F3E7',  
                            pink: '#D78B9B',   
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .layered-card { position: relative; background: #F6F3E7; border: 2px dashed #8AD1C3; }
        .layered-card::before {
            content: ''; position: absolute; inset: 0; background: #8AD1C3;
            transform: translate(8px, 8px); z-index: -1; border-radius: inherit;
        }
    </style>
</head>
<body class="antialiased bg-keong-navy text-keong-cream font-sans relative overflow-x-hidden">
    
    <!-- Organic Floating Ornaments -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-keong-teal rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
    <div class="absolute bottom-20 right-20 w-64 h-64 bg-keong-peach rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
    
    <!-- Starfish/Floral Abstract SVG Shapes -->
    <svg class="absolute top-20 right-32 w-16 h-16 text-keong-teal opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.4-6.3-4.8-6.3 4.8 2.3-7.4-6-4.6h7.6z"/></svg>
    <svg class="absolute bottom-40 left-20 w-12 h-12 text-keong-peach opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c0 5.5-4.5 10-10 10 5.5 0 10 4.5 10 10 0-5.5 4.5-10 10-10-5.5 0-10-4.5-10-10z"/></svg>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-8 py-10 min-h-screen flex flex-col">
        
        <!-- Navbar -->
        <nav class="flex items-center justify-between py-6 border-b border-keong-teal/30">
            <div class="flex items-center gap-3">
                <!-- FONT DIPERBAIKI: Bersih, Mewah, Tanpa Emoji Keong -->
                <div class="font-sans text-2xl font-bold tracking-widest text-keong-teal uppercase">
                    KABINET KEONG
                </div>
            </div>

            <!-- Navigation Links -->
            @if (Route::has('login'))
                <div class="flex items-center gap-6">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-keong-cream hover:text-keong-teal transition-colors uppercase tracking-widest text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-keong-cream hover:text-keong-peach transition-colors uppercase tracking-widest text-sm">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2 bg-keong-teal text-keong-navy font-bold rounded-none uppercase tracking-widest text-sm border border-keong-teal hover:bg-transparent hover:text-keong-teal transition-all">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>

        <!-- Hero Section -->
        <div class="flex-grow flex flex-col justify-center text-center mt-12 mb-20">
            <h2 class="text-keong-peach font-semibold tracking-[0.3em] uppercase text-sm mb-4">Student Council Universitas Ciputra</h2>
            <h1 class="text-6xl md:text-8xl font-serif font-bold text-keong-cream mb-8 leading-tight">
                Inventory <br> <span class="italic text-keong-teal font-normal">&</span> Systems
            </h1>
            
            <p class="text-lg md:text-xl text-keong-cream/80 max-w-2xl mx-auto font-light leading-relaxed mb-12">
                "Adaptive resilience and continuous growth." Kelola seluruh inventaris organisasi dengan struktur yang rapi, adaptif, dan kolaboratif.
            </p>
            
            <div class="flex justify-center gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-keong-peach text-keong-navy font-bold text-lg hover:bg-opacity-80 transition-all shadow-[4px_4px_0px_#8AD1C3]">Masuk Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-keong-teal text-keong-navy font-bold text-lg hover:bg-opacity-80 transition-all shadow-[4px_4px_0px_#E5A87B]">Mulai Pinjam</a>
                    @endauth
                @endif
            </div>
        </div>

        <!-- Features (Layered Card Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 pb-20">
            <div class="layered-card rounded-2xl p-8 text-keong-navy">
                <div class="w-12 h-12 bg-keong-navy text-keong-teal rounded-full flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="font-serif text-2xl font-bold mb-3">Adaptive Tracking</h3>
                <p class="text-sm font-light text-slate-700">Lacak setiap pergerakan barang secara adaptif dan real-time.</p>
            </div>
            <div class="layered-card rounded-2xl p-8 text-keong-navy" style="--tw-border-opacity: 1; border-color: #E5A87B;">
                <style>.layered-card:nth-child(2)::before { background: #E5A87B; }</style>
                <div class="w-12 h-12 bg-keong-navy text-keong-peach rounded-full flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-serif text-2xl font-bold mb-3">Shared Identity</h3>
                <p class="text-sm font-light text-slate-700">Proses peminjaman barang terintegrasi dengan struktur organisasi.</p>
            </div>
            <div class="layered-card rounded-2xl p-8 text-keong-navy" style="--tw-border-opacity: 1; border-color: #D78B9B;">
                <style>.layered-card:nth-child(3)::before { background: #D78B9B; }</style>
                <div class="w-12 h-12 bg-keong-navy text-keong-pink rounded-full flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="font-serif text-2xl font-bold mb-3">Resilient Data</h3>
                <p class="text-sm font-light text-slate-700">Penyimpanan data yang tangguh, aman, dan terpusat di cloud.</p>
            </div>
        </div>
    </div>
</body>
</html>