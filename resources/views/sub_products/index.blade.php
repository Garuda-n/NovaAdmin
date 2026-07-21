<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sub Product Master
            </h2>

            @can('sub-products.create')
            <a href="{{ route('sub-products.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700">
                + Add Sub Product
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
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Sub Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Status</th>
                                @if(Auth::user()->can('sub-products.edit') || Auth::user()->can('sub-products.delete'))
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @forelse($subProducts as $subProduct)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                    <!-- Code -->
                                    <td class="px-6 py-4 font-mono text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $subProduct->code }}
                                    </td>

                                    <!-- Name -->
                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                        {{ $subProduct->name }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @if($subProduct->status)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    @if(Auth::user()->can('sub-products.edit') || Auth::user()->can('sub-products.delete'))
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">
                                            @can('sub-products.edit')
                                            <a href="{{ route('sub-products.edit', $subProduct) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                                title="Edit">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            @endcan

                                            @can('sub-products.delete')
                                            <form action="{{ route('sub-products.destroy', $subProduct) }}" method="POST"
                                                  onsubmit="return confirm('Delete this sub product?')">
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
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No sub products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subProducts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $subProducts->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
