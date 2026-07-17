<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                UOM Master
            </h2>

            @can('uoms.create')
            <a href="{{ route('uoms.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                Add UOM
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-toast />
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">

                    <thead class="bg-gray-100 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">Short Code</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">Has Decimals</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">Status</th>
                            @if(Auth::user()->can('uoms.edit') || Auth::user()->can('uoms.delete'))
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">

                        @forelse($uoms as $uom)

                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">

                                <td class="px-6 py-4 text-gray-900 dark:text-slate-200">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 dark:text-slate-200">
                                    {{ $uom->name }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 dark:text-slate-200">
                                    {{ strtoupper($uom->shortcode) }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($uom->has_decimals)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            Yes
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                            No
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($uom->status)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                @if(Auth::user()->can('uoms.edit') || Auth::user()->can('uoms.delete'))
                                <td class="px-6 py-4 text-center">
                                    @can('uoms.edit')
                                    <a href="{{ route('uoms.edit', $uom) }}"
                                        class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Edit
                                    </a>
                                    @endcan
                                </td>
                                @endif

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                    No UOM records found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>
</x-app-layout>