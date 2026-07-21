<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Menu Management
            </h2>

            @can('menus.create')
            <a href="{{ route('menus.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700">
                + Add Menu
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">

                        <thead class="bg-gray-100 dark:bg-slate-700">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    S.No
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Order
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Icon
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Name
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Route
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Permission
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Status
                                </th>

                                @if(Auth::user()->can('menus.edit') || Auth::user()->can('menus.delete'))
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Action
                                </th>
                                @endif

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">

                            @forelse($menus as $menu)

                                {{-- Parent row --}}
                                <tr class="bg-slate-50 dark:bg-slate-700/60 hover:bg-gray-100 dark:hover:bg-slate-600 transition">

                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200 font-semibold">
                                        {{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200 font-semibold">
                                        {{ $menu->order }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($menu->icon)
                                            <x-dynamic-component :component="'heroicon-o-' . $menu->icon" class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200 font-semibold">
                                        {{ $menu->name }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $menu->route ?? '— (Group)' }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $menu->permission_slug ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($menu->status)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    @if(Auth::user()->can('menus.edit') || Auth::user()->can('menus.delete'))
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">

                                            @can('menus.edit')
                                            <a href="{{ route('menus.edit', $menu) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                                title="Edit">
                                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                                            </a>
                                            @endcan

                                            @can('menus.delete')
                                            <form action="{{ route('menus.destroy', $menu) }}" method="POST"
                                                  onsubmit="return confirm('Delete this menu and all its children?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-500 hover:bg-red-600 text-white transition"
                                                    title="Delete">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                            @endcan

                                        </div>
                                    </td>
                                    @endif

                                </tr>

                                {{-- Children rows --}}
                                @foreach($menu->children->sortBy('order') as $child)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">

                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-xs font-medium">
                                        {{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->parent->iteration }}.{{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-3 pl-12 text-gray-600 dark:text-gray-400">
                                        {{ $child->order }}
                                    </td>

                                    <td class="px-6 py-3 pl-12">
                                        @if($child->icon)
                                            <x-dynamic-component :component="'heroicon-o-' . $child->icon" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-3 pl-12 text-gray-600 dark:text-gray-400">
                                        ↳ {{ $child->name }}
                                    </td>

                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $child->route ?? '—' }}
                                    </td>

                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $child->permission_slug ?? '—' }}
                                    </td>

                                    <td class="px-6 py-3">
                                        @if($child->status)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    @if(Auth::user()->can('menus.edit') || Auth::user()->can('menus.delete'))
                                    <td class="px-6 py-3">
                                        <div class="flex justify-center gap-2">

                                            @can('menus.edit')
                                            <a href="{{ route('menus.edit', $child) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                                title="Edit">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            @endcan

                                            @can('menus.delete')
                                            <form action="{{ route('menus.destroy', $child) }}" method="POST"
                                                  onsubmit="return confirm('Delete this menu item?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition"
                                                    title="Delete">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                            @endcan

                                        </div>
                                    </td>
                                    @endif

                                </tr>
                                @endforeach

                            @empty

                                <tr>
                                    <td colspan="8"
                                        class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No menus found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($menus->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $menus->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
