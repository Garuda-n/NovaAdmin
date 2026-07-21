<x-app-layout>
    <div class="p-6">
        <x-toast />
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Counter Master
                </h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">
                    Manage all counters.
                </p>
            </div>
            @can('counters.create')
            <a href="{{ route('counters.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">
                <x-heroicon-o-plus class="w-5 h-5" />
                Add Counter
            </a>
            @endcan
        </div>
        <!-- Table -->
        <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg">
            <table class="min-w-full border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-700 dark:text-gray-300">
                            #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-700 dark:text-gray-300">
                            Counter Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-700 dark:text-gray-300">
                            Counter Code
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-slate-700 dark:text-gray-300">
                            Status
                        </th>
                        @if(Auth::user()->can('counters.edit') || Auth::user()->can('counters.delete'))
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-slate-700 dark:text-gray-300">
                            Action
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($counters as $counter)
                        <tr class="border-b border-slate-200 dark:border-slate-700 transition hover:bg-slate-50 dark:hover:bg-slate-800 last:border-b-0">
                            <td class="px-6 py-4 text-slate-800 dark:text-white">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">
                                {{ $counter->counter_name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-gray-300">
                                {{ $counter->counter_code }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($counter->status)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            @if(Auth::user()->can('counters.edit') || Auth::user()->can('counters.delete'))
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-3">
                                    @can('counters.edit')
                                    <a href="{{ route('counters.edit', $counter) }}"
                                        class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-3 py-2 text-sm text-white transition hover:bg-indigo-700">
                                        <x-heroicon-o-pencil-square class="h-4 w-4" />
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        data-id="{{ $counter->id }}"
                                        data-name="{{ $counter->counter_name }}"
                                        data-branches="{{ $counter->branches->pluck('id')->toJson() }}"
                                        class="assignBranchBtn inline-flex items-center gap-2 rounded-md bg-emerald-600 px-3 py-2 text-sm text-white transition hover:bg-emerald-700">
                                        <x-heroicon-o-building-office-2 class="h-4 w-4" />
                                        Assign Branch
                                    </button>
                                    @endcan
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                No counters found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($counters->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            {{ $counters->links() }}
        </div>
        @endif
    </div>
    <!-- ========================= -->
    <!-- Assign Branch Modal START -->
    <!-- ========================= -->
    <div
        id="branchModal"
        class="fixed inset-0 z-50 hidden">
        <!-- Blur Background -->
        <div
            id="modalOverlay"
            class="absolute inset-0 bg-black/60 backdrop-blur-sm">
        </div>
        <!-- Modal Container -->
        <div class="relative flex min-h-screen items-center justify-center p-6">
            <div class="w-full max-w-2xl rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900 shadow-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-6 pt-5 pb-3">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Assign Branch
                    </h2>
                    <button
                        id="closeModal"
                        type="button"
                        class="text-slate-400 transition hover:text-slate-700 dark:hover:text-white text-lg">
                        ✕
                    </button>
                </div>
                <!-- Modal Body -->
                <form id="branchForm" method="POST">
                    @csrf
                    <div class="px-6 pt-4">
                        <!-- Counter Label -->
                        <div class="pb-3 mb-1">
                            <p class="text-sm text-slate-500 dark:text-gray-400">
                                Counter : <span id="counterTitle" class="font-semibold text-emerald-600 dark:text-emerald-400"></span>
                            </p>
                        </div>
                        <hr class="border-slate-200 dark:border-slate-700">
                        <!-- Branch List -->
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($branches as $branch)
                                <label
                                    class="flex cursor-pointer items-center gap-3 px-1 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <input
                                        type="checkbox"
                                        name="branch_ids[]"
                                        value="{{ $branch->id }}"
                                        class="h-4 w-4 rounded border-slate-300 bg-white text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="text-sm text-slate-800 dark:text-gray-200">
                                        {{ $branch->name }}
                                    </span>
                                </label>
                            @empty
                                <div class="py-6 text-center text-slate-400 dark:text-gray-400">
                                    No branches found.
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                        <button
                            type="button"
                            id="cancelModal"
                            class="rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white px-5 py-2 text-sm font-medium transition">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-emerald-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ========================= -->
    <!-- Assign Branch Modal END -->
    <!-- ========================= -->
    <script src="{{ asset('js/masters/counter_branch_mapping.js') }}" defer></script>
</x-app-layout>