@php
    $sidebarMenus = \App\Models\Menu::with(['children' => function ($q) {
        $q->active()->orderBy('order');
    }])
    ->active()
    ->roots()
    ->orderBy('order')
    ->get();

    // Determine which parent dropdown should be open based on current route
    $activeMenu = '';
    foreach ($sidebarMenus as $menu) {
        if ($menu->children->isNotEmpty()) {
            foreach ($menu->children as $child) {
                if ($child->route && request()->routeIs(str_replace('.index', '.*', $child->route))) {
                    $activeMenu = \Illuminate\Support\Str::slug($menu->name, '_');
                    break 2;
                }
            }
        }
    }
@endphp

<aside
    x-data="{ openMenu: '{{ $activeMenu }}' }"
    @mouseenter="sidebarOpen = true"
    @mouseleave="sidebarOpen = false"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="min-h-screen bg-slate-900 text-slate-300 transition-all duration-300 ease-in-out overflow-hidden flex flex-col border-r border-slate-800 shadow-xl">

    {{-- Logo --}}
    <div class="h-16 flex items-center justify-center border-b border-slate-800">

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

        @foreach($sidebarMenus as $menu)

            @php
                $menuSlug = \Illuminate\Support\Str::slug($menu->name, '_');
                $isLink = $menu->route && $menu->children->isEmpty();
                $isDropdown = $menu->children->isNotEmpty();
            @endphp

            {{-- Single link item (no children) --}}
            @if($isLink)

                @if($menu->permission_slug)
                    @can($menu->permission_slug)
                    <a href="{{ route($menu->route) }}"
                        class="flex items-center px-6 py-3 transition
                        {{ request()->routeIs(str_replace('.index', '', $menu->route) . '*') || request()->routeIs($menu->route)
                            ? 'bg-slate-800 text-white font-semibold border-l-4 border-indigo-500 shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">

                        <x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />

                        <span
                            x-show="sidebarOpen"
                            x-transition
                            class="ml-3 whitespace-nowrap">
                            {{ $menu->name }}
                        </span>

                    </a>
                    @endcan
                @else
                    <a href="{{ route($menu->route) }}"
                        class="flex items-center px-6 py-3 transition
                        {{ request()->routeIs(str_replace('.index', '', $menu->route) . '*') || request()->routeIs($menu->route)
                            ? 'bg-slate-800 text-white font-semibold border-l-4 border-indigo-500 shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">

                        <x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />

                        <span
                            x-show="sidebarOpen"
                            x-transition
                            class="ml-3 whitespace-nowrap">
                            {{ $menu->name }}
                        </span>

                    </a>
                @endif

            {{-- Dropdown group (has children) --}}
            @elseif($isDropdown)

                @php
                    $childPermissions = $menu->children
                        ->pluck('permission_slug')
                        ->filter()
                        ->toArray();
                @endphp

                @if(count($childPermissions) > 0)
                    @canany($childPermissions)
                    <div>
                        <button
                            type="button"
                            @click="sidebarOpen = true; openMenu = openMenu === '{{ $menuSlug }}' ? '' : '{{ $menuSlug }}'"
                            class="w-full flex items-center justify-between px-6 py-3 text-slate-300 hover:bg-slate-800/60 hover:text-white transition">
                            <div class="flex items-center">
                                <x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />
                                <span
                                    x-show="sidebarOpen"
                                    x-transition
                                    class="ml-3 whitespace-nowrap">
                                    {{ $menu->name }}
                                </span>
                            </div>
                            <svg
                                x-show="sidebarOpen"
                                x-transition
                                class="w-4 h-4 transition-transform duration-300"
                                :class="{ '-rotate-90': openMenu === '{{ $menuSlug }}' }"
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
                            x-show="openMenu === '{{ $menuSlug }}' && sidebarOpen"
                            x-transition>

                            @foreach($menu->children as $child)
                                @if($child->permission_slug)
                                    @can($child->permission_slug)
                                    <a href="{{ route($child->route) }}"
                                        @click="sidebarOpen = true"
                                        class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                                        {{ request()->routeIs(str_replace('.index', '.*', $child->route))
                                            ? 'bg-indigo-600/20 text-indigo-400 font-semibold'
                                            : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                        <x-dynamic-component :component="'heroicon-o-' . ($child->icon ?? 'stop')" class="w-5 h-5 shrink-0" />
                                        <span x-show="sidebarOpen" x-transition>
                                            {{ $child->name }}
                                        </span>
                                    </a>
                                    @endcan
                                @else
                                    <a href="{{ route($child->route) }}"
                                        @click="sidebarOpen = true"
                                        class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                                        {{ request()->routeIs(str_replace('.index', '.*', $child->route))
                                            ? 'bg-indigo-600/20 text-indigo-400 font-semibold'
                                            : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                        <x-dynamic-component :component="'heroicon-o-' . ($child->icon ?? 'stop')" class="w-5 h-5 shrink-0" />
                                        <span x-show="sidebarOpen" x-transition>
                                            {{ $child->name }}
                                        </span>
                                    </a>
                                @endif
                            @endforeach

                        </div>

                    </div>
                    @endcanany
                @else
                    {{-- No permission-gated children, show unconditionally --}}
                    <div>
                        <button
                            type="button"
                            @click="sidebarOpen = true; openMenu = openMenu === '{{ $menuSlug }}' ? '' : '{{ $menuSlug }}'"
                            class="w-full flex items-center justify-between px-6 py-3 text-slate-300 hover:bg-slate-800/60 hover:text-white transition">
                            <div class="flex items-center">
                                <x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />
                                <span
                                    x-show="sidebarOpen"
                                    x-transition
                                    class="ml-3 whitespace-nowrap">
                                    {{ $menu->name }}
                                </span>
                            </div>
                            <svg
                                x-show="sidebarOpen"
                                x-transition
                                class="w-4 h-4 transition-transform duration-300"
                                :class="{ '-rotate-90': openMenu === '{{ $menuSlug }}' }"
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
                            x-show="openMenu === '{{ $menuSlug }}' && sidebarOpen"
                            x-transition>

                            @foreach($menu->children as $child)
                                <a href="{{ route($child->route) }}"
                                    @click="sidebarOpen = true"
                                    class="flex items-center gap-3 pl-14 pr-6 py-2 transition
                                    {{ request()->routeIs(str_replace('.index', '.*', $child->route))
                                        ? 'bg-indigo-600/20 text-indigo-400 font-semibold'
                                        : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                    <x-dynamic-component :component="'heroicon-o-' . ($child->icon ?? 'stop')" class="w-5 h-5 shrink-0" />
                                    <span x-show="sidebarOpen" x-transition>
                                        {{ $child->name }}
                                    </span>
                                </a>
                            @endforeach

                        </div>

                    </div>
                @endif

            @endif

        @endforeach

        <!-- Transactions -->
        <a href="#"
            class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-800/60 hover:text-white transition">

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
            class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-800/60 hover:text-white transition">

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
            class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-800/60 hover:text-white transition">

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