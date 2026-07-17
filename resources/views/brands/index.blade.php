<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Brand Master
            </h2>

            @can('brands.create')
            <a href="{{ route('brands.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700">
                + Add Brand
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
                                    Logo
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Code
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Brand Name
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Status
                                </th>

                                @if(Auth::user()->can('brands.edit') || Auth::user()->can('brands.delete'))
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Action
                                </th>
                                @endif

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">

                            @forelse($brands as $brand)

                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">

                                    <!-- Logo -->
                                    <td class="px-6 py-4">

                                        @if($brand->logo_url)

                                            <img
                                                src="{{ $brand->logo_url }}"
                                                alt="Brand Logo"
                                                class="w-8 h-8 rounded-lg border object-cover">

                                        @else

                                            <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-slate-700 flex items-center justify-center">

                                                <x-heroicon-o-photo
                                                    class="w-6 h-6 text-gray-400" />

                                            </div>

                                        @endif

                                    </td>

                                    <!-- Code -->
                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                        {{ $brand->code }}
                                    </td>

                                    <!-- Name -->
                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                        {{ $brand->name }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">

                                        @if($brand->status)

                                            <span
                                                class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                Active
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    <!-- Action -->
                                    @if(Auth::user()->can('brands.edit') || Auth::user()->can('brands.delete'))
                                    <td class="px-6 py-4">

                                        <div class="flex justify-center">

                                            @can('brands.edit')
                                            <a href="{{ route('brands.edit', $brand) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                                title="Edit">

                                                <x-heroicon-o-pencil-square class="w-5 h-5" />

                                            </a>
                                            @endcan

                                        </div>

                                    </td>
                                    @endif

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">

                                        No brands found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>