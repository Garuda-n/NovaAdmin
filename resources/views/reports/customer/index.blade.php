<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-6 h-6 text-indigo-500" />
                    Customer Performance & Analysis Report
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Analyze customer purchase histories, revenue contributions, tax, discounts, and regional distribution.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="printReportContent('report-table-container', 'Customer Performance & Analysis Report')" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
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
            filterUrl: '{{ route('reports.customer.search') }}',
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

                    <!-- Row 2: Customer Specific Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 pt-2 border-t border-slate-100 dark:border-slate-700/60">
                        <!-- Search Input (Name, Mobile, GST) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Search Customer</label>
                            <input type="text" name="search_text" placeholder="Name, Mobile, or GST No..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Customer Type Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Customer Type</label>
                            <select name="customer_type" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Types --</option>
                                <option value="B2B">B2B (Business)</option>
                                <option value="B2C">B2C (Retail)</option>
                            </select>
                        </div>

                        <!-- State Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">State</label>
                            <select name="state_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All States --</option>
                                @foreach($states as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">City</label>
                            <select name="city_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Cities --</option>
                                @foreach($cities as $ct)
                                    <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- All Statuses --</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
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
                            Generate Customer Report
                        </button>
                    </div>
                </form>
            </div>

            {{-- Report Table Container --}}
            <div id="report-table-container">
                @include('reports.customer._table', ['reportData' => $reportData])
            </div>

            <!-- Customer Sales History Modal Pop-up -->
            <div id="customer-sales-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="relative w-full max-w-4xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all my-8">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <x-heroicon-o-shopping-cart class="w-5 h-5 text-indigo-500" />
                            Customer Sales History
                        </h3>
                        <button type="button" onclick="closeCustomerSalesModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-lg transition">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Modal Body Container -->
                    <div id="customer-sales-modal-body" class="p-6">
                        <div class="flex items-center justify-center py-12 text-slate-400">
                            <svg class="animate-spin h-6 w-6 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Loading customer sales history...</span>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openCustomerSalesModal(customerId) {
                    const modal = document.getElementById('customer-sales-modal');
                    const modalBody = document.getElementById('customer-sales-modal-body');
                    if (!modal || !modalBody) return;

                    modal.classList.remove('hidden');
                    modalBody.classList.remove('hidden');
                    modalBody.innerHTML = `
                        <div class="flex items-center justify-center py-12 text-slate-400">
                            <svg class="animate-spin h-6 w-6 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Loading customer sales history...</span>
                        </div>
                    `;

                    fetch('/reports/customer/' + customerId + '/sales-modal', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.html) {
                            modalBody.innerHTML = data.html;
                        } else {
                            modalBody.innerHTML = '<p class="text-center text-rose-500 py-8">Failed to load customer sales history.</p>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        modalBody.innerHTML = '<p class="text-center text-rose-500 py-8">An error occurred while fetching sales history.</p>';
                    });
                }

                function closeCustomerSalesModal() {
                    const modal = document.getElementById('customer-sales-modal');
                    const modalBody = document.getElementById('customer-sales-modal-body');
                    if (modal) modal.classList.add('hidden');
                    if (modalBody) modalBody.classList.remove('hidden');
                }
            </script>

        </div>
    </div>
</x-app-layout>
