<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-toast />
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tax Master
            </h2>

            <a href="{{ route('taxes.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                + Add Tax
            </a>
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
                                    Code
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Tax Name
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Percentage
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">

                            @forelse($taxes as $tax)

                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">

                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                                        {{ $tax->code }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                                        {{ $tax->name }}
                                    </td>

                                    <td class="px-6 py-4 text-center text-sm text-gray-800 dark:text-white">
                                        {{ number_format($tax->percentage, 2) }}%
                                    </td>

                                    <td class="px-6 py-4 text-center">

                                        @if($tax->status)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                Inactive
                                            </span>
                                        @endif

                                    </td>

                                    <td class="px-6 py-4 text-center">

                                        <a href="{{ route('taxes.edit',$tax) }}"
                                            class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                            Edit
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No Tax Records Found.
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