<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SCIS Kabinet Keong') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased selection:bg-indigo-100 selection:text-indigo-900">
        <div class="min-h-screen bg-gray-50/50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-100">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- ========================================================== -->
        <!-- 🚀 FLOATING TOAST NOTIFICATIONS (POP-UP MELAYANG) 🚀       -->
        <!-- ========================================================== -->
        <div class="fixed top-24 right-5 z-[9999] flex flex-col gap-3 pointer-events-none">
            
            <!-- 1. TOAST SUCCESS (Warna Hijau Emerald) -->
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transform transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transform transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="translate-x-full opacity-0"
                     class="pointer-events-auto w-80 sm:w-96 bg-white border-l-4 border-emerald-500 rounded-2xl shadow-2xl p-4 flex items-start gap-4 relative overflow-hidden">
                    
                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 py-0.5">
                        <h4 class="text-sm font-black text-gray-900 tracking-wide uppercase">Berhasil!</h4>
                        <p class="text-[11px] font-medium text-gray-500 mt-1 leading-snug">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-gray-300 hover:text-gray-900 bg-gray-50 hover:bg-gray-200 p-1.5 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <!-- 2. TOAST ERROR (Warna Merah Ruby) -->
            @if(session('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 6000)"
                     x-transition:enter="transform transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transform transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="translate-x-full opacity-0"
                     class="pointer-events-auto w-80 sm:w-96 bg-white border-l-4 border-rose-500 rounded-2xl shadow-2xl p-4 flex items-start gap-4 relative overflow-hidden">
                    
                    <div class="flex-shrink-0 w-10 h-10 bg-rose-50 rounded-full flex items-center justify-center text-rose-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 py-0.5">
                        <h4 class="text-sm font-black text-gray-900 tracking-wide uppercase">Perhatian!</h4>
                        <p class="text-[11px] font-medium text-gray-500 mt-1 leading-snug">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-gray-300 hover:text-gray-900 bg-gray-50 hover:bg-gray-200 p-1.5 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <!-- 3. TOAST PENGINGAT NOTIF BARU (Warna Ungu Indigo - Sistem Cerdas) -->
            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <div x-data="{ show: false }" 
                     x-init="
                        if (!sessionStorage.getItem('toast_shown_{{ auth()->id() }}')) {
                            setTimeout(() => show = true, 500);
                            setTimeout(() => show = false, 7000);
                            sessionStorage.setItem('toast_shown_{{ auth()->id() }}', 'true');
                        }
                     "
                     x-show="show" 
                     x-transition:enter="transform transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transform transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="translate-x-full opacity-0"
                     class="pointer-events-auto w-80 sm:w-96 bg-white border-l-4 border-indigo-500 rounded-2xl shadow-2xl p-4 flex items-start gap-4 relative overflow-hidden" style="display: none;">
                    
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 py-0.5">
                        <h4 class="text-sm font-black text-gray-900 tracking-wide uppercase">Ada Kabar Baru!</h4>
                        <p class="text-[11px] font-medium text-gray-500 mt-1 leading-snug">Kamu punya <b class="text-indigo-600">{{ auth()->user()->unreadNotifications->count() }} pesan</b> yang belum dibaca.</p>
                        <a href="{{ route('notifications.index') }}" class="inline-block mt-3 text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-colors">Lihat Inbox &rarr;</a>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-gray-300 hover:text-gray-900 bg-gray-50 hover:bg-gray-200 p-1.5 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

        </div>
    </body>
</html>