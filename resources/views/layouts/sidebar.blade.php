@php
    $sidebarMenus = \App\Models\Menu::with(['children' => function ($q) {
        $q->active()->orderBy('order');
    }])
    ->active()
    ->roots()
    ->orderBy('order')
    ->get();
@endphp

<aside
    @mouseenter="sidebarOpen = true"
    @mouseleave="sidebarOpen = sidebarPinned"
    :class="sidebarOpen ? 'w-56' : 'w-16'"
    class="h-screen sticky top-0 shrink-0 bg-slate-900 text-slate-300 transition-all duration-300 ease-in-out overflow-hidden flex flex-col border-r border-slate-800 shadow-xl">

    {{-- Logo --}}
    <div class="relative h-12 shrink-0 flex items-center justify-center border-b border-slate-800">

        <div class="flex items-center">

            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600 flex items-center justify-center shadow-md shrink-0">

                <span class="text-white font-bold text-base tracking-wider">
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
                class="ml-2.5">

                <h1 class="text-sm font-bold text-white leading-none">
                    {{ $currentCompany?->name ?? 'NovaAdmin' }}
                </h1>

                <p class="text-[10px] text-slate-400 tracking-wider uppercase mt-0.5">
                    Admin Panel
                </p>

            </div>

        </div>

    </div>
    {{-- Menu search --}}
    <div x-data="{ menuSearch: '' }" class="contents">
    <div class="shrink-0 px-2.5 pt-2.5">
        <button type="button"
            x-show="!sidebarOpen"
            @click="sidebarOpen = true; $nextTick(() => $refs.menuSearchInput?.focus())"
            class="w-full flex items-center justify-center py-1.5 text-slate-400 hover:text-white transition"
            title="Search menu">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 shrink-0" />
        </button>
        <div x-show="sidebarOpen" class="relative">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-2 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none" />
            <input
                x-ref="menuSearchInput"
                x-model="menuSearch"
                @keydown.escape="menuSearch = ''"
                type="text"
                placeholder="Search menu..."
                autocomplete="off"
                class="w-full bg-slate-800 border border-slate-700 rounded-md pl-8 pr-2 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-0">
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-scroll mt-2 flex-1 min-h-0 overflow-y-auto overflow-x-hidden space-y-0.5 pb-4"
        x-data="{
            match(name) {
                const q = menuSearch.trim().toLowerCase();
                return q === '' || name.toLowerCase().includes(q);
            },
            matchAny(names) { return names.some(n => this.match(n)); }
        }">

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
                        x-show="match(@js($menu->name))"
                        class="flex items-center px-3.5 py-2 text-xs transition
                        {{ request()->routeIs(str_replace('.index', '', $menu->route) . '*') || request()->routeIs($menu->route)
                            ? 'bg-slate-800 text-white font-semibold border-l-4 border-indigo-500 shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">

                        <x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />

                        <span
                            x-show="sidebarOpen"
                            x-transition
                            class="ml-2.5 whitespace-nowrap">
                            {{ $menu->name }}
                        </span>

                    </a>
                    @endcan
                @else
                    <a href="{{ route($menu->route) }}"
                        x-show="match(@js($menu->name))"
                        class="flex items-center px-3.5 py-2 text-xs transition
                        {{ request()->routeIs(str_replace('.index', '', $menu->route) . '*') || request()->routeIs($menu->route)
                            ? 'bg-slate-800 text-white font-semibold border-l-4 border-indigo-500 shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">

                        <x-dynamic-component :component="'heroicon-o-' . ($menu->icon ?? 'stop')" class="w-5 h-5 shrink-0" />

                        <span
                            x-show="sidebarOpen"
                            x-transition
                            class="ml-2.5 whitespace-nowrap">
                            {{ $menu->name }}
                        </span>

                    </a>
                @endif

            {{-- Section group (has children): label + always-visible items --}}
            @elseif($isDropdown)

                @php
                    $visibleChildren = $menu->children->filter(
                        fn ($child) => ! $child->permission_slug || \Illuminate\Support\Facades\Gate::allows($child->permission_slug)
                    );
                @endphp

                @if($visibleChildren->isNotEmpty())
                    {{-- Section label (expanded) / divider (collapsed) --}}
                    <div class="px-3.5 pt-4 pb-1"
                        x-show="matchAny(@js($visibleChildren->pluck('name')->values()))">
                        <div x-show="!sidebarOpen" class="border-t border-slate-800 mx-1"></div>
                        <span
                            x-show="sidebarOpen"
                            x-transition
                            class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                            {{ $menu->name }}
                        </span>
                    </div>

                    @foreach($visibleChildren as $child)
                        <a href="{{ route($child->route) }}"
                            x-show="match(@js($child->name))"
                            class="flex items-center px-3.5 py-2 text-xs transition
                            {{ request()->routeIs(str_replace('.index', '.*', $child->route))
                                ? 'bg-slate-800 text-white font-semibold border-l-4 border-indigo-500 shadow-sm'
                                : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">

                            <x-dynamic-component :component="'heroicon-o-' . ($child->icon ?? 'stop')" class="w-5 h-5 shrink-0" />

                            <span
                                x-show="sidebarOpen"
                                x-transition
                                class="ml-2.5 whitespace-nowrap">
                                {{ $child->name }}
                            </span>
                        </a>
                    @endforeach
                @endif

            @endif


        @endforeach

        <!-- Transactions -->
        <a href="#"
            x-show="match('Transactions')"
            class="flex items-center px-3.5 py-2 text-xs text-slate-300 hover:bg-slate-800/60 hover:text-white transition">

            <x-heroicon-o-arrows-right-left class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition
                class="ml-2.5">
                Transactions
            </span>

        </a>

        <!-- Settings -->
        <a href="#"
            x-show="match('Settings')"
            class="flex items-center px-3.5 py-2 text-xs text-slate-300 hover:bg-slate-800/60 hover:text-white transition">

            <x-heroicon-o-cog-6-tooth class="w-5 h-5 shrink-0" />

            <span
                x-show="sidebarOpen"
                x-transition
                class="ml-2.5">
                Settings
            </span>

        </a>

    </nav>
    </div>

</aside>