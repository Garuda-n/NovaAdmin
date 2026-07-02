<aside class="w-64 min-h-screen bg-slate-900 text-white">
    <div class="p-6 border-b border-slate-700" style="height: 65px !important;">
        <h1 class="text-2xl font-bold" style="margin-bottom: 10px !important;">NovaAdmin</h1>
    </div>
    <nav class="mt-4">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="block px-6 py-3 transition
            {{ request()->routeIs('dashboard')
                ? 'bg-slate-800 border-l-4 border-blue-500'
                : 'hover:bg-slate-800' }}">
            Dashboard
        </a>
        {{-- Administration --}}
        <div x-data="{
            open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }}
        }">
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-800 transition">
                <span>Administration</span>
                <svg
                    class="w-5 h-5 transition-transform duration-300"
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
            <div x-show="open" x-transition>
                <a href="{{ route('users.index') }}"
                   class="block pl-12 pr-6 py-2 hover:bg-slate-800">
                    Users
                </a>
                <a href="{{ route('roles.index') }}"
                   class="block pl-12 pr-6 py-2 hover:bg-slate-800">
                    Roles
                </a>
            </div>
        </div>
        {{-- Masters --}}
        <div class="px-6 py-3 hover:bg-slate-800">
            Masters
        </div>
        {{-- Transactions --}}
        <div class="px-6 py-3 hover:bg-slate-800">
            Transactions
        </div>
        {{-- Reports --}}
        <div class="px-6 py-3 hover:bg-slate-800">
            Reports
        </div>
        {{-- Settings --}}
        <div class="px-6 py-3 hover:bg-slate-800">
            Settings
        </div>
    </nav>
</aside>