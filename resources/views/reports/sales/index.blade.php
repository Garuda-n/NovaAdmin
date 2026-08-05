<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-2">
                    <x-heroicon-o-shopping-cart class="w-6 h-6 text-indigo-500" />
                    Sales Performance Report
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Analyze sales revenue, invoice counts, tax breakdown, and customer transactions across branches and counters.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="printReportContent('report-table-container', 'Sales Performance Report')" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <x-heroicon-o-printer class="w-4 h-4" />
                    Print Report
                </button>
            </div>
        </div>
    </x-slot>

    <script src="{{ asset('js/report/date_range.js') }}"></script>
    <script src="{{ asset('js/report/filters.js') }}"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="reportFilters({
            preset: '{{ $dateRange['preset'] }}',
            from_date: '{{ $dateRange['from_date']->format('Y-m-d') }}',
            to_date: '{{ $dateRange['to_date']->format('Y-m-d') }}',
            filterUrl: '{{ route('reports.sales.search') }}',
            csrfToken: '{{ csrf_token() }}',
            containerId: 'report-table-container'
        })">

            {{-- Comprehensive Filter Panel --}}
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-4">
                <form @submit.prevent="applyFilter()" class="space-y-4">
                    @csrf

                    <!-- Row 1: Date Range Filter -->
                    <x-report.date-range
                        :preset="$dateRange['preset']"
                        :from-date="$dateRange['from_date']->format('Y-m-d')"
                        :to-date="$dateRange['to_date']->format('Y-m-d')"
                        :preset-options="$presetOptions"
                    />

                    <!-- Row 2: Secondary Filters (Branch, Counter, Customer Type, Customer, Payment Mode, Status) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                        <!-- Branch Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Branch</label>
                            <select name="branch_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Branches --</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" @selected(($reportData['filters']['branch_id'] ?? request('branch_id')) == $b->id)>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Counter Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Counter</label>
                            <select name="counter_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Counters --</option>
                                @foreach($counters as $c)
                                    <option value="{{ $c->id }}" @selected(($reportData['filters']['counter_id'] ?? request('counter_id')) == $c->id)>{{ $c->counter_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Customer Type Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Customer Type</label>
                            <select name="customer_type" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Types --</option>
                                <option value="B2B" @selected(($reportData['filters']['customer_type'] ?? request('customer_type')) === 'B2B')>B2B (Business)</option>
                                <option value="B2C" @selected(($reportData['filters']['customer_type'] ?? request('customer_type')) === 'B2C')>B2C (Retail)</option>
                            </select>
                        </div>

                        <!-- Customer Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Customer</label>
                            <select name="customer_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Customers --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" @selected(($reportData['filters']['customer_id'] ?? request('customer_id')) == $cust->id)>{{ $cust->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Mode Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Payment Method</label>
                            <select name="payment_mode_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Methods --</option>
                                @foreach($paymentModes as $pm)
                                    <option value="{{ $pm->id }}" @selected(($reportData['filters']['payment_mode_id'] ?? request('payment_mode_id')) == $pm->id)>{{ $pm->mode_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Invoice Status</label>
                            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Statuses --</option>
                                <option value="1" @selected(($reportData['filters']['status'] ?? request('status')) == 1)>Completed</option>
                                <option value="2" @selected(($reportData['filters']['status'] ?? request('status')) == 2)>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="resetFilter()" class="px-4 py-2 text-xs font-bold rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            Reset Filters
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition flex items-center gap-1.5">
                            <x-heroicon-o-funnel class="w-4 h-4" />
                            Generate Sales Report
                        </button>
                    </div>
                </form>
            </div>

            {{-- Report Table & Summary KPIs Container --}}
            <div id="report-table-container">
                @include('reports.sales._table', ['reportData' => $reportData])
            </div>

        </div>
    </div>
</x-app-layout>
