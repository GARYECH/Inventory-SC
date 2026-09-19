<nav
    x-data="{ open: false }"
    class="sticky top-0 z-50 border-b border-gray-200/80 bg-white/95 shadow-sm backdrop-blur-xl"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- ========================================================= -->
        <!-- TOP NAVBAR -->
        <!-- ========================================================= -->

        <div class="flex h-[72px] items-center justify-between">

            <!-- ===================================================== -->
            <!-- LEFT SIDE -->
            <!-- ===================================================== -->

            <div class="flex min-w-0 items-center">

                <!-- LOGO -->
                <div class="shrink-0">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-900 to-gray-700 shadow-lg shadow-gray-200 transition duration-300 hover:-rotate-3 hover:shadow-xl">
                            <x-application-logo
                                class="block h-7 w-7 fill-current text-white"
                            />
                        </div>
                    </a>
                </div>


                <!-- DESKTOP NAVIGATION -->
                <div class="hidden lg:ml-10 lg:flex lg:items-center lg:gap-1">

                    @if(Auth::user()->role === 'admin')

                        <x-nav-link
                            :href="route('admin.dashboard')"
                            :active="request()->routeIs('admin.dashboard')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('admin.items.index')"
                            :active="request()->routeIs('admin.items.*')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Manage Inventory') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('admin.categories.index')"
                            :active="request()->routeIs('admin.categories.*')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Categories') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('admin.orders')"
                            :active="request()->routeIs('admin.orders')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Confirm Returns') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('guidebook')"
                            :active="request()->routeIs('guidebook')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Guidebook') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('admin.settings.index')"
                            :active="request()->routeIs('admin.settings.index')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Settings') }}
                        </x-nav-link>

                    @else

                        <x-nav-link
                            :href="route('student.dashboard')"
                            :active="request()->routeIs('student.dashboard')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Rent Items') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('student.loans')"
                            :active="request()->routeIs('student.loans')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('My Active Loans') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('guidebook')"
                            :active="request()->routeIs('guidebook')"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition"
                        >
                            {{ __('Guidebook') }}
                        </x-nav-link>

                    @endif

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- RIGHT SIDE DESKTOP -->
            <!-- ===================================================== -->

            <div class="hidden items-center gap-3 sm:flex">

                <!-- NOTIFICATION -->
                <a
                    href="{{ route('notifications.index') }}"
                    class="group relative flex h-10 w-10 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 text-gray-500 transition hover:border-indigo-100 hover:bg-indigo-50 hover:text-indigo-600"
                    aria-label="Notifications"
                >

                    <svg
                        class="h-5 w-5 transition group-hover:scale-105"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>


                    @if(
                        auth()->check() &&
                        auth()->user()->unreadNotifications->count() > 0
                    )

                        <span class="absolute -right-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[8px] font-black text-white shadow-sm">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>

                    @endif

                </a>


                <!-- PROFILE DROPDOWN -->
                <x-dropdown
                    align="right"
                    width="64"
                >

                    <x-slot name="trigger">

                        <button
                            type="button"
                            class="group flex items-center gap-3 rounded-2xl border border-gray-100 bg-white px-3 py-2 transition hover:border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        >

                            <!-- AVATAR -->
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-[11px] font-black text-white shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>


                            <!-- USER INFO -->
                            <div class="hidden text-left xl:block">

                                <p class="max-w-[150px] truncate text-xs font-black text-gray-900">
                                    {{ Auth::user()->name }}
                                </p>

                                <p class="mt-0.5 text-[8px] font-black uppercase tracking-widest text-gray-400">
                                    {{ ucfirst(Auth::user()->role) }}
                                </p>

                            </div>


                            <!-- CHEVRON -->
                            <svg
                                class="h-4 w-4 text-gray-400 transition group-hover:text-gray-600"
                                fill="none"
                                viewBox="0 0 20 20"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M6 8l4 4 4-4"
                                />
                            </svg>

                        </button>

                    </x-slot>


                    <x-slot name="content">

                        <!-- PROFILE HEADER -->
                        <div class="border-b border-gray-100 px-4 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-xs font-black text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-black text-gray-900">
                                        {{ Auth::user()->name }}
                                    </p>

                                    <p class="mt-0.5 text-[9px] font-bold uppercase tracking-widest text-gray-400">
                                        {{ ucfirst(Auth::user()->role) }} Account
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- PROFILE -->
                        <x-dropdown-link
                            :href="route('profile.edit')"
                            class="flex items-center gap-3 px-4 py-3 text-xs font-bold"
                        >

                            <svg
                                class="h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21a8 8 0 0116 0"
                                />
                            </svg>

                            {{ __('Profile') }}

                        </x-dropdown-link>


                        <!-- LOG OUT -->
                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left text-xs font-bold text-red-600 transition hover:bg-red-50"
                            >

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M15 12H3m12 0l-4-4m4 4l-4 4m7-9V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2h6a2 2 0 002-2v-2"
                                    />
                                </svg>

                                {{ __('Log Out') }}

                            </button>

                        </form>

                    </x-slot>

                </x-dropdown>

            </div>


            <!-- ===================================================== -->
            <!-- MOBILE RIGHT SIDE -->
            <!-- ===================================================== -->

            <div class="flex items-center gap-2 sm:hidden">

                <!-- MOBILE NOTIFICATION -->
                <a
                    href="{{ route('notifications.index') }}"
                    class="group relative flex h-10 w-10 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 text-gray-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                    aria-label="Notifications"
                >

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
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>


                    @if(
                        auth()->check() &&
                        auth()->user()->unreadNotifications->count() > 0
                    )

                        <span class="absolute -right-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[8px] font-black text-white shadow-sm">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>

                    @endif

                </a>


                <!-- HAMBURGER -->
                <button
                    type="button"
                    @click="open = ! open"
                    :aria-expanded="open.toString()"
                    aria-label="Open navigation menu"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                >

                    <!-- MENU ICON -->
                    <svg
                        x-show="!open"
                        x-cloak
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>


                    <!-- CLOSE ICON -->
                    <svg
                        x-show="open"
                        x-cloak
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M6 18L18 6"
                        />
                    </svg>

                </button>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- MOBILE MENU -->
    <!-- ============================================================= -->

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="border-t border-gray-100 bg-white shadow-lg sm:hidden"
    >

        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">

            <!-- ===================================================== -->
            <!-- MOBILE USER CARD -->
            <!-- ===================================================== -->

            <div class="mb-4 rounded-2xl border border-gray-100 bg-gray-50 p-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-sm font-black text-white shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">

                        <p class="truncate text-sm font-black text-gray-900">
                            {{ Auth::user()->name }}
                        </p>

                        <div class="mt-1 inline-flex rounded-full bg-white px-2.5 py-1 text-[8px] font-black uppercase tracking-widest text-gray-500 shadow-sm">
                            {{ ucfirst(Auth::user()->role) }}
                        </div>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- MOBILE NAVIGATION -->
            <!-- ===================================================== -->

            <div class="space-y-1">

                @if(Auth::user()->role === 'admin')

                    <x-responsive-nav-link
                        :href="route('admin.dashboard')"
                        :active="request()->routeIs('admin.dashboard')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('admin.items.index')"
                        :active="request()->routeIs('admin.items.*')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Manage Inventory') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('admin.categories.index')"
                        :active="request()->routeIs('admin.categories.*')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Manage Categories') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('admin.orders')"
                        :active="request()->routeIs('admin.orders')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Confirm Returns') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('guidebook')"
                        :active="request()->routeIs('guidebook')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Guidebook') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('admin.settings.index')"
                        :active="request()->routeIs('admin.settings.index')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('System Settings') }}
                    </x-responsive-nav-link>

                @else

                    <x-responsive-nav-link
                        :href="route('student.dashboard')"
                        :active="request()->routeIs('student.dashboard')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Rent Items') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('student.loans')"
                        :active="request()->routeIs('student.loans')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('My Active Loans') }}
                    </x-responsive-nav-link>


                    <x-responsive-nav-link
                        :href="route('guidebook')"
                        :active="request()->routeIs('guidebook')"
                        @click="open = false"
                        class="rounded-xl px-4 py-3 text-sm font-bold"
                    >
                        {{ __('Guidebook') }}
                    </x-responsive-nav-link>

                @endif

            </div>


            <!-- ===================================================== -->
            <!-- MOBILE ACCOUNT -->
            <!-- ===================================================== -->

            <div class="my-4 border-t border-gray-100"></div>

            <div class="space-y-1">

                <!-- PROFILE -->
                <x-responsive-nav-link
                    :href="route('profile.edit')"
                    :active="request()->routeIs('profile.edit')"
                    @click="open = false"
                    class="rounded-xl px-4 py-3 text-sm font-bold"
                >
                    <span class="flex items-center gap-3">

                        <svg
                            class="h-5 w-5 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21a8 8 0 0116 0"
                            />
                        </svg>

                        <span>{{ __('Profile') }}</span>

                    </span>
                </x-responsive-nav-link>


                <!-- LOG OUT -->
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        @click="open = false"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-bold text-red-600 transition hover:bg-red-50"
                    >

                        <svg
                            class="h-5 w-5 text-red-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M15 12H3m12 0l-4-4m4 4l-4 4m7-9V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2h6a2 2 0 002-2v-2"
                            />
                        </svg>

                        {{ __('Log Out') }}

                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>