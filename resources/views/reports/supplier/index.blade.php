<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <x-heroicon-o-building-storefront class="w-6 h-6 text-indigo-500" />
                    Supplier Report
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Review supplier-wise purchase activity and balances.
                </p>
            </div>
        </div>
    </x-slot>

    <script src="{{ asset('js/report/date_range.js') }}"></script>
    <script src="{{ asset('js/report/filters.js') }}"></script>
    <script src="{{ asset('js/report/export.js') }}"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="reportFilters({
            preset: '{{ $dateRange['preset'] }}',
            from_date: '{{ $dateRange['from_date']->format('Y-m-d') }}',
            to_date: '{{ $dateRange['to_date']->format('Y-m-d') }}',
            filterUrl: '{{ route('reports.supplier.index') }}',
            csrfToken: '{{ csrf_token() }}',
            containerId: 'report-table-container'
        })">

            {{-- Filter Panel --}}
            <x-report.filters>
                <form @submit.prevent="applyFilter()" class="space-y-4">
                    @csrf

                    <x-report.date-range
                        :preset="$dateRange['preset']"
                        :from-date="$dateRange['from_date']->format('Y-m-d')"
                        :to-date="$dateRange['to_date']->format('Y-m-d')"
                        :preset-options="$presetOptions"
                    />

                    <x-report.filter-actions />
                </form>
            </x-report.filters>

            {{-- Report Table --}}
            <div id="report-table-container">
                <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <x-report.table-header :columns="[
                            ['label' => '#'],
                            ['label' => 'Supplier'],
                            ['label' => 'Transactions'],
                            ['label' => 'Total'],
                        ]" />
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                    Report data will appear here.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
