<aside class="w-64 min-h-screen bg-slate-900 text-white">

    {{-- Logo --}}
    <div class="h-16 flex items-center px-6 border-b border-slate-700">
        <h1 class="text-2xl font-bold tracking-wide">
            NovaAdmin
        </h1>
    </div>

    {{-- Sidebar Menu --}}
    <nav class="mt-4">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('dashboard')
                ? 'bg-slate-800 border-l-4 border-indigo-500 text-white'
                : 'hover:bg-slate-800 text-slate-200' }}">

            <x-heroicon-o-home class="w-5 h-5 shrink-0" />

            <span>Dashboard</span>
        </a>

        {{-- Administration --}}
        <div
            x-data="{
                open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }}
            }">

            <button
                @click="open = !open"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">

                <div class="flex items-center gap-3">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 shrink-0" />
                    <span>Administration</span>
                </div>

                <svg
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
            <div x-show="open" x-transition>

                {{-- Users --}}
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                   {{ request()->routeIs('users.*')
                        ? 'bg-slate-800 text-white'
                        : 'hover:bg-slate-800 text-slate-300' }}">

                    <x-heroicon-o-users class="w-5 h-5 shrink-0" />

                    <span>Users</span>

                </a>

                {{-- Roles --}}
                <a href="{{ route('roles.index') }}"
                   class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                   {{ request()->routeIs('roles.*')
                        ? 'bg-slate-800 text-white'
                        : 'hover:bg-slate-800 text-slate-300' }}">

                    <x-heroicon-o-shield-check class="w-5 h-5 shrink-0" />

                    <span>Roles</span>

                </a>

            </div>
        </div>

        {{-- Masters --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" />

            <span>Masters</span>

        </a>

        {{-- Transactions --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-credit-card class="w-5 h-5 shrink-0" />

            <span>Transactions</span>

        </a>

        {{-- Reports --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-chart-bar class="w-5 h-5 shrink-0" />

            <span>Reports</span>

        </a>

        {{-- Settings --}}
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition text-slate-200">

            <x-heroicon-o-cog-6-tooth class="w-5 h-5 shrink-0" />

            <span>Settings</span>

        </a>

    </nav>

</aside>