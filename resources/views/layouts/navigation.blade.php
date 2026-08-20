<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.items.index')" :active="request()->routeIs('admin.items.*')">
                            {{ __('Manage Inventory') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                            {{ __('Manage Categories') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders')" :active="request()->routeIs('admin.orders')">
                            {{ __('Confirm Returns') }}
                        </x-nav-link>
                        <!-- 🌟 TOMBOL MENU SETTINGS (DESKTOP) 🌟 -->
                        <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')">
                            {{ __('System Settings') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Rent Items') }}
                        </x-nav-link>
                        <!-- 🌟 SUDAH DIPERBAIKI MENJADI routeIs 🌟 -->
                        <x-nav-link :href="route('student.loans')" :active="request()->routeIs('student.loans')">
                            {{ __('My Active Loans') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 🖥️ MENU KANAN (UNTUK DESKTOP / LAPTOP)        -->
            <!-- ============================================== -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                
                <!-- 🌟 LONCENG NOTIFIKASI (DESKTOP) 🌟 -->
                <div class="relative mr-4 flex items-center" x-data="{ notifOpen: false }">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        
                        <!-- Badge Angka Merah jika ada notif -->
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[9px] font-black text-white bg-red-500 border-2 border-white rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden" style="display: none;">
                        <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex justify-between items-center">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Notifikasi</h3>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @if(auth()->check() && auth()->user()->notifications->count() > 0)
                                @foreach(auth()->user()->notifications as $notification)
                                    <div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 {{ $notification->read_at ? 'opacity-50' : 'bg-indigo-50/10' }}">
                                        <p class="text-xs font-bold text-gray-800">{{ $notification->data['message'] ?? 'Ada pembaruan sistem.' }}</p>
                                        <p class="text-[9px] text-gray-400 mt-1 font-bold">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-6 text-center text-gray-400 text-xs font-bold">Belum ada notifikasi.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- MENU PROFILE -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- ============================================== -->
            <!-- 📱 MENU KANAN (UNTUK MOBILE / HP)             -->
            <!-- ============================================== -->
            <div class="-me-2 flex items-center sm:hidden">
                
                <!-- 🌟 LONCENG NOTIFIKASI (MOBILE) 🌟 -->
                <div class="relative mr-2 flex items-center" x-data="{ notifOpen: false }">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        
                        <!-- Badge Angka Merah jika ada notif -->
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[9px] font-black text-white bg-red-500 border-2 border-white rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Notifikasi Mobile -->
                    <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak class="absolute right-0 top-full mt-2 w-72 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden" style="display: none;">
                        <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex justify-between items-center">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Notifikasi</h3>
                        </div>
                        <div class="max-h-60 overflow-y-auto">
                            @if(auth()->check() && auth()->user()->notifications->count() > 0)
                                @foreach(auth()->user()->notifications as $notification)
                                    <div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 {{ $notification->read_at ? 'opacity-50' : 'bg-indigo-50/10' }}">
                                        <p class="text-xs font-bold text-gray-800">{{ $notification->data['message'] ?? 'Ada pembaruan sistem.' }}</p>
                                        <p class="text-[9px] text-gray-400 mt-1 font-bold">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-6 text-center text-gray-400 text-xs font-bold">Belum ada notifikasi.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Hamburger Button -->
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- MOBILE MENU KIRI -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.items.index')" :active="request()->routeIs('admin.items.*')">
                    {{ __('Manage Inventory') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                    {{ __('Manage Categories') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders')" :active="request()->routeIs('admin.orders')">
                    {{ __('Confirm Returns') }}
                </x-responsive-nav-link>
                <!-- 🌟 TOMBOL MENU SETTINGS (HP/MOBILE) 🌟 -->
                <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')">
                    {{ __('System Settings') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    {{ __('Rent Items') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.loans')" :active="request()->routeIs('student.loans')">
                    {{ __('My Active Loans') }}
                </x-responsive-nav-link>
            @endif
        </div>
    </div>
</nav>