<aside
    @mouseenter="sidebarOpen = true"
    @mouseleave="sidebarOpen = false"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="min-h-screen bg-slate-900 text-white transition-all duration-300 ease-in-out overflow-hidden flex flex-col border-r border-slate-700">

{{-- Logo --}}
<div class="h-16 flex items-center justify-center border-b border-slate-700">
    <div class="flex items-center">
        {{-- Logo --}}
        <div
            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600
                   flex items-center justify-center shadow-lg shrink-0">

            <span class="text-white font-extrabold text-xl tracking-wider">
                {{ strtoupper(substr($currentCompany?->name ?? 'N', 0, 1)) }}
            </span>
        </div>
        {{-- Project Name --}}
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

    {{-- Sidebar Menu --}}
    <nav class="mt-4">

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

        {{-- Administration --}}
        <div
            x-data="{
                open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }}
            }">

            <button
                @click="open = !open"
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
                    :class="{ '-rotate-90': open }"
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

            {{-- Child Menu --}}
            <div
                x-show="open && sidebarOpen"
                x-transition>

                {{-- Users --}}
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('users.*')
                            ? 'bg-slate-800 text-white'
                            : 'hover:bg-slate-800 text-slate-300' }}">

                        <x-heroicon-o-users class="w-5 h-5 shrink-0" />

                        <span
                            x-show="sidebarOpen"
                            x-transition>
                            Users
                        </span>

                    </a>

                {{-- Roles --}}
                <a href="{{ route('roles.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('roles.*')
                            ? 'bg-slate-800 text-white'
                            : 'hover:bg-slate-800 text-slate-300' }}">

                        <x-heroicon-o-shield-check class="w-5 h-5 shrink-0" />

                        <span
                            x-show="sidebarOpen"
                            x-transition>
                            Roles
                        </span>

                    </a>

            </div>
        </div>

        {{-- Masters --}}
        <div
            x-data="{
                open: {{ request()->routeIs('companies.*') ? 'true' : 'false' }}
            }">
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">
                <div class="flex items-center">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" />
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
                    :class="{ '-rotate-90': open }"
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
            {{-- Child Menu --}}
            <div
                x-show="open && sidebarOpen"
                x-transition>
                {{-- Companies --}}
                <a href="{{ route('companies.index') }}"
                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                    {{ request()->routeIs('companies.*')
                            ? 'bg-slate-800 text-white'
                            : 'hover:bg-slate-800 text-slate-300' }}">
                    <x-heroicon-o-building-office class="w-5 h-5 shrink-0" />
                    <span
                        x-show="sidebarOpen"
                        x-transition>
                        Companies
                    </span>
                </a>
            </div>
        </div>

        {{-- Transactions --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-credit-card class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition>
                Transactions
            </span>

        </a>

        {{-- Reports --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-chart-bar class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition>
                Reports
            </span>

        </a>

        {{-- Settings --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-cog-6-tooth class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition>
                Settings
            </span>

        </a>

    </nav>

</aside>