<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6 text-slate-800 dark:text-gray-100">

                    <x-toast />

                    <div class="flex items-center justify-between mb-8">

                        <div>
                            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                                Companies
                            </h1>

                            <p class="text-slate-500 dark:text-slate-400 mt-1">
                                Manage all companies in your application.
                            </p>
                        </div>

                        @can('companies.create')
                        <a href="{{ route('companies.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition duration-200">
                            <span class="text-lg">+</span>
                            <span>Add Company</span>
                        </a>
                        @endcan

                    </div>

                    <table class="w-full">

                        <thead>

                            <tr class="bg-slate-50 dark:bg-slate-800">

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    ID
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Company Name
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Code
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Phone
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Status
                                </th>

                                @if(Auth::user()->can('companies.edit') || Auth::user()->can('companies.delete'))
                                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    Actions
                                </th>
                                @endif

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($companies as $company)

                                <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $company->id }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $company->name }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $company->code }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $company->phone ?: '-' }}
                                    </td>

                                    <td class="px-6 py-4">

                                        @if ($company->status)

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    @if(Auth::user()->can('companies.edit') || Auth::user()->can('companies.delete'))
                                    <td class="px-6 py-4 text-center">

                                        @can('companies.edit')
                                        <a href="{{ route('companies.edit', $company->id) }}"
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition">

                                            Edit

                                        </a>
                                        @endcan

                                    </td>
                                    @endif

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">

                                        No companies found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($companies->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $companies->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>