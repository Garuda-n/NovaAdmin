<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Page Header -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Returned Stock Register
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Manage returned serialized products in RETURNED status and recreate them into active inventory.
                    </p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white dark:bg-[#182035] rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-800">
                <form action="{{ route('returned-stock.index') }}" method="GET" id="filter_form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Current Status Tab value hidden input -->
                    <input type="hidden" name="current_status" id="current_status" value="{{ request('current_status', 'pending') }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Company</label>
                        <select name="company_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Companies</option>
                            @foreach (\App\Models\Company::where('status', 1)->get() as $c)
                                <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Branch</label>
                        <select name="branch_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Branches</option>
                            @foreach (\App\Models\Branch::get() as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Counter</label>
                        <select name="counter_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Counters</option>
                            @foreach (\App\Models\Counter::where('status', 1)->get() as $cnt)
                                <option value="{{ $cnt->id }}" {{ request('counter_id') == $cnt->id ? 'selected' : '' }}>{{ $cnt->counter_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Item Code</label>
                        <input type="text" name="item_code" value="{{ request('item_code') }}" placeholder="Search Serial Code..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex items-end gap-2 md:col-span-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('returned-stock.index') }}" class="w-full text-center py-2.5 px-3 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-300 transition">
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tab Switchers -->
            <div class="flex border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-[#182035] rounded-t-xl overflow-hidden shadow-sm">
                <button type="button" onclick="switchTab('pending')" id="tab_pending" class="flex-1 py-4 text-center font-bold text-sm border-b-2 transition duration-200 {{ request('current_status', 'pending') === 'pending' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-slate-50/50 dark:bg-slate-800/10' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 hover:bg-slate-50/20' }}">
                    Pending Warehouse Recreation
                </button>
                <button type="button" onclick="switchTab('recreated')" id="tab_recreated" class="flex-1 py-4 text-center font-bold text-sm border-b-2 transition duration-200 {{ request('current_status') === 'recreated' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-slate-50/50 dark:bg-slate-800/10' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 hover:bg-slate-50/20' }}">
                    Recreated Active Inventory
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-[#182035] rounded-b-xl shadow-sm border-x border-b border-slate-200 dark:border-slate-800 overflow-hidden">
                @include('inventory.returned_stock._table')
            </div>

        </div>
    </div>

    <!-- Hidden Form for CSRF POST Recreation requests -->
    <form id="recreate_post_form" method="POST" style="display:none;">
        @csrf
    </form>

    @push('scripts')
    <script>
        function switchTab(status) {
            document.getElementById('current_status').value = status;
            document.getElementById('filter_form').submit();
        }

        function triggerRecreation(salesReturnDetailId, serialCode) {
            if (!confirm(`Are you sure you want to recreate active inventory for returned serialized item '${serialCode}'?`)) {
                return;
            }

            const form = document.getElementById('recreate_post_form');
            form.action = `/returned-stock/${salesReturnDetailId}/recreate`;
            
            // Show processing status
            const btn = document.getElementById(`recreate_btn_${salesReturnDetailId}`);
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin h-3.5 w-3.5 mr-1 text-white inline" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Recreating...
                `;
            }

            form.submit();
        }
    </script>
    @endpush
</x-app-layout>
