<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Financial Year Master
            </h2>

            <a href="{{ route('financial-years.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                Add Financial Year
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-toast />

            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">

                    <thead class="bg-gray-100 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                #
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                Financial Year
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                Start Date
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                End Date
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                Current
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                Status
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-700 dark:text-gray-300">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">

                        @forelse($financialYears as $financialYear)

                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">

                                <td class="px-6 py-4 text-gray-900 dark:text-slate-200">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4 text-gray-900 dark:text-slate-200">
                                    {{ $financialYear->name }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-900 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($financialYear->start_date)->format('d-m-Y') }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-900 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($financialYear->end_date)->format('d-m-Y') }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($financialYear->is_current)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            Current
                                        </span>
                                    @else
                                        <form action="{{ route('financial-years.make-current', $financialYear) }}"
                                            method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                onclick="return confirm('Make this Financial Year as Current?')"
                                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                Make Current
                                            </button>
                                        </form>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($financialYear->status)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <a href="{{ route('financial-years.edit', $financialYear) }}"
                                        class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Edit
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                    No Financial Year records found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>
</x-app-layout>