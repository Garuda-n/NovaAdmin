@php
    $activeMenu = match (true) {
        request()->routeIs('users.*'),
        request()->routeIs('roles.*') => 'administration',

        request()->routeIs('companies.*'),
        request()->routeIs('branches.*') => 'masters',

        request()->routeIs('uoms.*'),
        request()->routeIs('products.*') => 'product_masters',

        default => '',
    };
@endphp

<aside
    x-data="{ openMenu: '{{ $activeMenu }}' }"
    @mouseenter="sidebarOpen = true"
    @mouseleave="sidebarOpen = false"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="min-h-screen bg-slate-900 text-white transition-all duration-300 ease-in-out overflow-hidden flex flex-col border-r border-slate-700">

    {{-- Logo --}}
    <div class="h-16 flex items-center justify-center border-b border-slate-700">

        <div class="flex items-center">

            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600 flex items-center justify-center shadow-lg shrink-0">

                <span class="text-white font-extrabold text-xl tracking-wider">
                    {{ strtoupper(substr($currentCompany?->name ?? 'N', 0, 1)) }}
                </span>

            </div>

            <div
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-2"
                class="ml-3">

                <h1 class="text-lg font-bold text-white leading-none">
                    {{ $currentCompany?->name ?? 'NovaAdmin' }}
                </h1>

                <p class="text-xs text-slate-400 tracking-widest uppercase">
                    Admin Panel
                </p>

            </div>

        </div>

    </div>

    {{-- Navigation --}}
    <nav class="mt-4 flex-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-6 py-3 transition
            {{ request()->routeIs('dashboard')
                ? 'bg-slate-800 border-l-4 border-indigo-500 text-white'
                : 'hover:bg-slate-800 text-slate-200' }}">

            <x-heroicon-o-home class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition
                class="ml-3 whitespace-nowrap">
                Dashboard
            </span>

        </a>

        {{-- Catalog Masters --}}
        <div>
            <button
                @click="openMenu = openMenu === 'catalog' ? '' : 'catalog'"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">
                <div class="flex items-center">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" />
                    <span
                        x-show="sidebarOpen"
                        x-transition
                        class="ml-3 whitespace-nowrap">
                        Catalog Masters
                    </span>
                </div>
                <svg
                    x-show="sidebarOpen"
                    x-transition
                    class="w-4 h-4 transition-transform duration-300"
                    :class="{ '-rotate-90': openMenu === 'catalog' }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div
                x-show="openMenu === 'catalog' && sidebarOpen"
                x-transition>
                <a href="{{ route('taxes.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('taxes.*')
                        ? 'bg-slate-800 text-white'
                        : 'hover:bg-slate-800 text-slate-300' }}">
                    <x-heroicon-o-users class="w-5 h-5 shrink-0" />
                    <span x-show="sidebarOpen" x-transition>
                        Taxes
                    </span>
                </a>
                <a href="{{ route('uoms.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('uoms.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <x-heroicon-o-scale class="w-5 h-5" />
                    <span x-show="sidebarOpen" x-transition>
                        UOM
                    </span>
                </a>
                <a href="{{ route('financial-years.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('financial-years.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                    <span>Financial Year</span>
                </a>

            </div>

        </div>
        {{-- Administration --}}
        <div>

            <button
                @click="openMenu = openMenu === 'administration' ? '' : 'administration'"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">

                <div class="flex items-center">

                    <x-heroicon-o-building-office-2 class="w-5 h-5 shrink-0" />

                    <span
                        x-show="sidebarOpen"
                        x-transition
                        class="ml-3 whitespace-nowrap">
                        Administration
                    </span>

                </div>

                <svg
                    x-show="sidebarOpen"
                    x-transition
                    class="w-4 h-4 transition-transform duration-300"
                    :class="{ '-rotate-90': openMenu === 'administration' }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

            </button>

            <div
                x-show="openMenu === 'administration' && sidebarOpen"
                x-transition>

                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('users.*')
                        ? 'bg-slate-800 text-white'
                        : 'hover:bg-slate-800 text-slate-300' }}">

                    <x-heroicon-o-users class="w-5 h-5 shrink-0" />

                    <span x-show="sidebarOpen" x-transition>
                        Users
                    </span>

                </a>

                <a href="{{ route('roles.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('roles.*')
                        ? 'bg-slate-800 text-white'
                        : 'hover:bg-slate-800 text-slate-300' }}">

                    <x-heroicon-o-shield-check class="w-5 h-5 shrink-0" />

                    <span x-show="sidebarOpen" x-transition>
                        Roles
                    </span>

                </a>

            </div>

        </div>

        <!-- Masters -->
        <div>

            <button
                @click="openMenu = openMenu === 'masters' ? '' : 'masters'"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">

                <div class="flex items-center">

                    <x-heroicon-o-building-library class="w-5 h-5 shrink-0" />

                    <span
                        x-show="sidebarOpen"
                        x-transition
                        class="ml-3 whitespace-nowrap">
                        Masters
                    </span>

                </div>

                <svg
                    x-show="sidebarOpen"
                    x-transition
                    class="w-4 h-4 transition-transform duration-300"
                    :class="{ '-rotate-90': openMenu === 'masters' }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

            </button>

            <div
                x-show="openMenu === 'masters' && sidebarOpen"
                x-transition>

                <a href="{{ route('companies.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('companies.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-slate-300' }}">

                    <x-heroicon-o-building-office class="w-5 h-5" />

                    <span>Companies</span>

                </a>

                <a href="{{ route('branches.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('branches.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-slate-300' }}">

                    <x-heroicon-o-map-pin class="w-5 h-5" />

                    <span>Branches</span>

                </a>

            </div>

        </div>

        <!-- Product Masters -->
        <div>

            <button
                @click="openMenu = openMenu === 'products' ? '' : 'products'"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">

                <div class="flex items-center">

                    <x-heroicon-o-cube class="w-5 h-5 shrink-0" />

                    <span
                        x-show="sidebarOpen"
                        x-transition
                        class="ml-3 whitespace-nowrap">
                        Product Masters
                    </span>

                </div>

                <svg
                    x-show="sidebarOpen"
                    x-transition
                    class="w-4 h-4 transition-transform duration-300"
                    :class="{ '-rotate-90': openMenu === 'products' }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

            </button>

            <div
                x-show="openMenu === 'products' && sidebarOpen"
                x-transition>
                <a href="{{ route('categories.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('categories.*')
                        ? 'bg-slate-800 text-white'
                        : 'hover:bg-slate-800 text-slate-300' }}">
                    <x-heroicon-o-rectangle-group class="w-5 h-5 shrink-0" />
                    <span x-show="sidebarOpen" x-transition>
                        Categories
                    </span>
                </a>

                {{-- Future --}}
                {{--
                <a href="{{ route('products.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 hover:bg-slate-800 text-slate-300">

                    <x-heroicon-o-cube class="w-5 h-5" />

                    <span>Products</span>

                </a>
                --}}

            </div>

        </div>

        <!-- Transactions -->
        <a href="#"
            class="flex items-center px-6 py-3 hover:bg-slate-800 text-slate-200 transition">

            <x-heroicon-o-arrows-right-left class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition
                class="ml-3">
                Transactions
            </span>

        </a>

        <!-- Reports -->
        <a href="#"
            class="flex items-center px-6 py-3 hover:bg-slate-800 text-slate-200 transition">

            <x-heroicon-o-chart-bar class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition
                class="ml-3">
                Reports
            </span>

        </a>

        <!-- Settings -->
        <a href="#"
            class="flex items-center px-6 py-3 hover:bg-slate-800 text-slate-200 transition">

            <x-heroicon-o-cog-6-tooth class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition
                class="ml-3">
                Settings
            </span>

        </a>

    </nav>

</aside>