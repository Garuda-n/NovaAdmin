<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6">

                    <x-toast />

                    <div class="flex items-center justify-between mb-8">

                        <div>
                            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                                Branches
                            </h1>

                            <p class="text-slate-500 dark:text-slate-400 mt-1">
                                Manage company branches.
                            </p>
                        </div>

                        @can('branches.create')
                        <a href="{{ route('branches.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition">

                            <span class="text-lg">+</span>

                            <span>Add Branch</span>

                        </a>
                        @endcan

                    </div>

                    <table class="w-full">

                        <thead>

                            <tr class="bg-slate-50 dark:bg-slate-800">

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    #
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    Company
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    Branch
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    Code
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    GST
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    Head Office
                                </th>

                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    Status
                                </th>

                                @if(Auth::user()->can('branches.edit') || Auth::user()->can('branches.delete'))
                                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase">
                                    Action
                                </th>
                                @endif

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($branches as $branch)

                                <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $branch->company->name }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $branch->name }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $branch->branch_code }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">
                                        {{ $branch->gst_number ?: '-' }}
                                    </td>

                                    <td class="px-6 py-4">

                                        @if($branch->is_head_office)

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700">
                                                Yes
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                No
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-6 py-4">

                                        @if($branch->status)

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    @if(Auth::user()->can('branches.edit') || Auth::user()->can('branches.delete'))
                                    <td class="px-6 py-4 text-center">

                                        @can('branches.edit')
                                        <a href="{{ route('branches.edit',$branch) }}"
                                           class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white">

                                            Edit

                                        </a>
                                        @endcan

                                    </td>
                                    @endif

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="8"
                                        class="px-6 py-10 text-center text-slate-400">

                                        No branches found.

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