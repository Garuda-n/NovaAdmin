<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-indigo-500" />
                    Dashboard Overview
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Real-time inventory analytics, stock status breakdown, and activity metrics.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form id="branch-select-form" class="inline-block" onsubmit="event.preventDefault()">
                    <select name="branch_id" onchange="filterBranch(this.value)" class="no-searchable inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-sm outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer" data-no-searchable="true">
                        <option value="">All Branches</option>
                        @foreach($branchesListForDropdown as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/30 dark:border-indigo-800/40">
                    <x-heroicon-o-calendar class="w-3.5 h-3.5 mr-1 text-indigo-500" />
                    Business Date: {{ \Carbon\Carbon::parse($businessDate)->format('d M Y') }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    <span class="relative flex h-2 w-2 mr-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live System
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>window.Chart || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"><\/script>')</script>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-4">
                <div class="flex items-center overflow-x-auto scrollbar-thin border-b border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 p-1.5 gap-1 scroll-smooth">
                    <button onclick="switchDashboardTab('crm')" id="tab-crm" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        CRM
                    </button>
                    <button onclick="switchDashboardTab('live-cockpit')" id="tab-live-cockpit" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Stock Details
                    </button>
                    <button onclick="switchDashboardTab('sales')" id="tab-sales" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Sales
                    </button>
                    <!-- <button onclick="switchDashboardTab('stock-transfer')" id="tab-stock-transfer" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Stock & Branch Transfer
                    </button>
                    <button onclick="switchDashboardTab('order-management')" id="tab-order-management" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Order Management
                    </button>
                    <button onclick="switchDashboardTab('sales-chart')" id="tab-sales-chart" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Sales Chart
                    </button>
                    <button onclick="switchDashboardTab('stock-chart')" id="tab-stock-chart" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Stock Chart
                    </button>
                    <button onclick="switchDashboardTab('contract-pricing')" id="tab-contract-pricing" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Contract Pricing
                    </button>
                    <button onclick="switchDashboardTab('gross-profit')" id="tab-gross-profit" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Gross Profit
                    </button>
                    <button onclick="switchDashboardTab('purchase')" id="tab-purchase" class="tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200">
                        Purchase
                    </button> -->
                </div>
            </div>

            <!-- Tab Content Panel: CRM -->
            <div id="content-crm" class="dashboard-content-panel hidden space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Customers</p>
                            <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalCustomers) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Registered clients</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-users class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">B2B Customers</p>
                            <h3 class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($b2bCustomers) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Corporate buyers</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-building-office-2 class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">B2C Customers</p>
                            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($b2cCustomers) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Direct consumers</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-user class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Suppliers</p>
                            <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalSuppliers) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Registered suppliers</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-building-storefront class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                            <x-heroicon-o-user-plus class="w-5 h-5 text-indigo-500" />
                            Recently Registered Customers
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Customer Name</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Mobile</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Type</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">GSTIN</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse($recentCustomers as $customer)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                            <td class="px-3 py-2.5 text-slate-900 dark:text-white font-medium">
                                                {{ $customer->customer_name }}
                                            </td>
                                            <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                                {{ $customer->mobile ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $customer->customer_type === 'B2B' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                                    {{ $customer->customer_type }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5 text-slate-500 font-mono">
                                                {{ $customer->gstin ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-6 text-slate-400">No customers registered yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                                <x-heroicon-o-chart-pie class="w-5 h-5 text-indigo-500" />
                                Customer Distribution
                            </h3>
                            <div class="h-64 relative flex items-center justify-center">
                                <canvas id="crmChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Live Cockpit -->
            <div id="content-live-cockpit" class="dashboard-content-panel space-y-4">
                <!-- KPI Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Available Stock Card -->
                <div class="bg-white dark:bg-slate-800 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Allocated Stock</p>
                        <h3 id="val-allocated-stock" class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalAvailableStock) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Ready for business</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <x-heroicon-o-cube class="w-6 h-6" />
                    </div>
                </div>

                <!-- Stock Inwards Card -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bulk Inwards</p>
                        <h3 id="val-bulk-inwards" class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalStockInwards) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Total inward batches</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
                    </div>
                </div>

                <!-- Products Card -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Products</p>
                        <h3 id="val-active-products" class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($totalProducts) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Master catalog</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                        <x-heroicon-o-squares-plus class="w-6 h-6" />
                    </div>
                </div>

                <!-- Operating Branches Card -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Branches</p>
                        <h3 id="val-branches" class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalBranches) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Active locations</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <x-heroicon-o-building-storefront class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Charts Section Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Chart 1: Stock Status Distribution (Doughnut) -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                            <x-heroicon-o-chart-pie class="w-5 h-5 text-indigo-500" />
                            Stock Status Distribution
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Available Stock by Category (Bar Chart) -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-emerald-500" />
                            Available Stock by Category
                        </h3>
                        <div class="h-64 relative">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Row: Stock by Branch & Recent Inward Invoices -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Chart 3: Stock by Branch -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <x-heroicon-o-map-pin class="w-5 h-5 text-purple-500" />
                        Available Stock by Branch
                    </h3>
                    <div class="h-64 relative">
                        <canvas id="branchChart"></canvas>
                    </div>
                </div>

                <!-- Recent Inwards Table -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5 text-amber-500" />
                            Recent Bulk Inward Invoices
                        </h3>
                        <a href="{{ route('stock-inwards.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                            View All →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Invoice No</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Supplier</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Branch</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300 text-right">Items Qty</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Date</th>
                                </tr>
                            </thead>
                            <tbody id="table-recent-inwards" class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($recentInwards as $inward)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                        <td class="px-3 py-2.5 font-semibold text-indigo-600 dark:text-indigo-400">
                                            #{{ $inward->invoice_no }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-medium">
                                            {{ $inward->supplier->supplier_name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                            {{ $inward->branch->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-bold text-slate-800 dark:text-slate-200">
                                            {{ number_format($inward->items->sum('qty'), 0) }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">
                                            {{ $inward->invoice_date ? $inward->invoice_date->format('d M Y') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-slate-400">
                                            No recent stock inwards recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>

            </div>

            <!-- Tab Content Panel: Sales -->
            <div id="content-sales" class="dashboard-content-panel hidden space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Sales Invoices</p>
                            <h3 id="val-total-sales-invoices" class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalSalesCount) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Generated bills</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-document-text class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross Sales Revenue</p>
                            <h3 id="val-gross-sales-revenue" class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">₹{{ number_format($totalSalesRevenue, 2) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Inclusive of GST</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-currency-rupee class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cancelled Invoices</p>
                            <h3 id="val-cancelled-invoices" class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ number_format($cancelledSalesCount) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Cancelled transactions</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-x-circle class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Average Invoice Value</p>
                            <h3 id="val-avg-invoice-value" class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">₹{{ number_format($avgInvoiceValue, 2) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Average ticket size</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-ticket class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <x-heroicon-o-clock class="w-5 h-5 text-indigo-500" />
                                Recent Sales Invoices
                            </h3>
                            <a href="{{ route('sales.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                View All →
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Invoice No</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Customer</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Branch</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Invoice Date</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Status</th>
                                        <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300 text-right">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody id="table-recent-sales" class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @forelse($recentSales as $sale)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                            <td class="px-3 py-2.5 text-indigo-600 dark:text-indigo-400 font-semibold">
                                                #{{ $sale->invoice_no_display }}
                                            </td>
                                            <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-medium">
                                                {{ $sale->customer->customer_name ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                                {{ $sale->branch->name ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap font-medium">
                                                {{ $sale->invoice_date ? $sale->invoice_date->format('d M Y') : '—' }}
                                            </td>
                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $sale->status === \App\Models\Sale::STATUS_COMPLETED ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300' }}">
                                                    {{ $sale->status === \App\Models\Sale::STATUS_COMPLETED ? 'Completed' : 'Cancelled' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5 text-right font-bold text-slate-900 dark:text-white">
                                                ₹{{ number_format($sale->grand_total, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-slate-400">No sales transactions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                                <x-heroicon-o-chart-bar class="w-5 h-5 text-indigo-500" />
                                Sales Revenue Trend
                            </h3>
                            <div class="h-64 relative">
                                <canvas id="salesTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- Tab Content Panel: Stock & Branch Transfer -->
            <div id="content-stock-transfer" class="dashboard-content-panel hidden space-y-4">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <x-heroicon-o-arrows-right-left class="w-5 h-5 text-indigo-500" />
                        Internal Inventory Allocation & Transfer Logs
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">TXN Ref</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Product Name</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Item Serial Code</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">From/To Branch</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Counter Location</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Remarks / Event</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Allocated Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($recentTransfers as $log)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                        <td class="px-3 py-2.5 text-slate-500 font-mono font-medium">
                                            #TXN-{{ $log->id }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-semibold">
                                            {{ $log->stockItem->product->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-indigo-600 dark:text-indigo-400 font-mono font-bold">
                                            {{ $log->stockItem->item_code ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                            {{ $log->branch->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                            {{ $log->counter->counter_name ?? 'Warehouse Main' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500">
                                            {{ $log->remarks }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-400 whitespace-nowrap">
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-6 text-slate-400">No transfer or allocations recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Order Management -->
            <div id="content-order-management" class="dashboard-content-panel hidden space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Quotations</p>
                            <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalQuotations) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Quotations created</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-document-duplicate class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Converted Invoices</p>
                            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($convertedQuotations) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Successfully converted</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Quotations</p>
                            <h3 class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($activeQuotations) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Awaiting customer approval</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-clock class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5 text-indigo-500" />
                            Recent Customer Quotations
                        </h3>
                        <a href="{{ route('quotations.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                            View All →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Quotation No</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Customer</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Branch</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Business Date</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Status</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300 text-right">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($recentQuotations as $quote)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                        <td class="px-3 py-2.5 text-indigo-600 dark:text-indigo-400 font-semibold">
                                            #{{ $quote->quotation_no }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-medium">
                                            {{ $quote->customer->customer_name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                            {{ $quote->branch->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">
                                            {{ $quote->business_date ? \Carbon\Carbon::parse($quote->business_date)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $quote->status == 2 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                                {{ $quote->status == 2 ? 'Converted' : 'Created' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-bold text-slate-900 dark:text-white">
                                            ₹{{ number_format($quote->grand_total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-6 text-slate-400">No quotations recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Sales Chart -->
            <div id="content-sales-chart" class="dashboard-content-panel hidden space-y-4">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <x-heroicon-o-chart-bar class="w-5 h-5 text-indigo-500" />
                        Monthly Sales Revenue Growth Trend
                    </h3>
                    <div class="h-96 relative">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Stock Chart -->
            <div id="content-stock-chart" class="dashboard-content-panel hidden space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                                <x-heroicon-o-chart-pie class="w-5 h-5 text-indigo-500" />
                                Stock Status Distribution
                            </h3>
                            <div class="h-64 relative flex items-center justify-center">
                                <canvas id="statusChartStockPage"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm lg:col-span-2">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-emerald-500" />
                            Available Stock by Category
                        </h3>
                        <div class="h-64 relative">
                            <canvas id="categoryChartStockPage"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <x-heroicon-o-map-pin class="w-5 h-5 text-purple-500" />
                        Available Stock by Branch
                    </h3>
                    <div class="h-72 relative">
                        <canvas id="branchChartStockPage"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Contract Pricing -->
            <div id="content-contract-pricing" class="dashboard-content-panel hidden space-y-4">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5 text-indigo-500" />
                            Product Standard Rates & Pricing Schemes
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300">Product Code</th>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300">Product Name</th>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300 text-right">Standard Selling Rate</th>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300">Contract Discount</th>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300 text-right">Net Contract Price</th>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300">UOM</th>
                                    <th class="px-3 py-2.5 font-semibold text-slate-600 dark:text-slate-300">Default Tax Group</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($contractProducts as $cp)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                        <td class="px-3 py-3 text-indigo-600 dark:text-indigo-400 font-semibold font-mono">
                                            {{ $cp->code }}
                                        </td>
                                        <td class="px-3 py-3 text-slate-800 dark:text-slate-200 font-semibold">
                                            {{ $cp->name }}
                                        </td>
                                        <td class="px-3 py-3 text-right font-medium text-slate-700 dark:text-slate-300">
                                            ₹{{ number_format($cp->selling_price, 2) }}
                                        </td>
                                        <td class="px-3 py-3 text-emerald-600 dark:text-emerald-400 font-bold">
                                            Base Rate (0.00% Special Discount)
                                        </td>
                                        <td class="px-3 py-3 text-right text-slate-900 dark:text-white font-bold">
                                            ₹{{ number_format($cp->selling_price, 2) }}
                                        </td>
                                        <td class="px-3 py-3 text-slate-500">
                                            {{ $cp->uom->name ?? 'Number' }}
                                        </td>
                                        <td class="px-3 py-3 text-slate-500">
                                            {{ $cp->tax->name ?? 'GST 0%' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-6 text-slate-400">No active products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Gross Profit -->
            <div id="content-gross-profit" class="dashboard-content-panel hidden space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross Sales Revenue (Excl. Tax)</p>
                        <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">₹{{ number_format($totalRevenue, 2) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Total revenue value</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cost of Goods Sold (COGS)</p>
                        <h3 class="text-2xl font-extrabold text-slate-600 dark:text-slate-400 mt-1">₹{{ number_format($totalCogs, 2) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Calculated purchase cost</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross Profit</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">₹{{ number_format($grossProfit, 2) }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Net profit margin</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross profit margin</p>
                        <h3 class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($grossProfitMargin, 2) }}%</h3>
                        <p class="text-xs text-slate-400 mt-1">Operating margin percent</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-indigo-500" />
                        Understanding Operating Gross Profit
                    </h3>
                    <div class="text-slate-600 dark:text-slate-300 space-y-2.5 text-xs leading-relaxed">
                        <p>• <strong>Gross Sales Revenue (Excl. Tax):</strong> Sum of all completed invoice line totals minus their respective tax amounts.</p>
                        <p>• <strong>Cost of Goods Sold (COGS):</strong> Calculated dynamically for each sold item. For individually tracked items, it takes the precise purchase cost from their original inward batch. For bulk quantity items, it falls back to the latest recorded supplier price.</p>
                        <p>• <strong>Gross Profit Margin:</strong> calculated as <code>(Gross Profit / Gross Revenue) * 100</code>.</p>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panel: Purchase -->
            <div id="content-purchase" class="dashboard-content-panel hidden space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Purchase Value</p>
                            <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">₹{{ number_format($totalPurchaseValue, 2) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Total purchasing volume</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bulk Inwards Count</p>
                            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalStockInwards) }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Total batches received</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <x-heroicon-o-squares-2x2 class="w-6 h-6" />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5 text-amber-500" />
                            Recent Purchases (Bulk Inwards)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Invoice No</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Supplier</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Branch</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300 text-right">Items Qty</th>
                                    <th class="px-3 py-2 font-semibold text-slate-600 dark:text-slate-300">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($recentInwards as $inward)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                                        <td class="px-3 py-2.5 font-semibold text-indigo-600 dark:text-indigo-400">
                                            #{{ $inward->invoice_no }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-medium">
                                            {{ $inward->supplier->supplier_name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                            {{ $inward->branch->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-bold text-slate-800 dark:text-slate-200">
                                            {{ number_format($inward->items->sum('qty'), 0) }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">
                                            {{ $inward->invoice_date ? $inward->invoice_date->format('d M Y') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-slate-400">No purchase invoices recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <script>
        function switchDashboardTab(tabId) {
            // 1. Hide all panels
            document.querySelectorAll('.dashboard-content-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            // 2. Show selected panel
            document.getElementById('content-' + tabId)?.classList.remove('hidden');

            // 3. Reset all tab buttons classes
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn px-4 py-2 text-xs font-semibold rounded-lg transition-all duration-150 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200";
            });

            // 4. Set active button class
            const activeBtn = document.getElementById('tab-' + tabId);
            if (activeBtn) {
                activeBtn.className = "tab-btn px-4 py-2 text-xs font-bold rounded-lg transition-all duration-150 whitespace-nowrap bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200/60 dark:border-slate-700";
            }

            // 5. Persist selected tab across page refreshes
            localStorage.setItem('active_dashboard_tab', tabId);
        }

        // AJAX branch filter function
        function filterBranch(branchId) {
            // Trigger fetch request
            fetch("{{ route('dashboard') }}?branch_id=" + branchId, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                // 1. Update KPI Card numbers
                document.getElementById('val-allocated-stock').innerText = data.totalAvailableStock.toLocaleString();
                document.getElementById('val-bulk-inwards').innerText = data.totalStockInwards.toLocaleString();
                document.getElementById('val-total-sales-invoices').innerText = data.totalSalesCount.toLocaleString();
                document.getElementById('val-gross-sales-revenue').innerText = '₹' + parseFloat(data.totalSalesRevenue).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('val-cancelled-invoices').innerText = data.cancelledSalesCount.toLocaleString();
                document.getElementById('val-avg-invoice-value').innerText = '₹' + parseFloat(data.avgInvoiceValue).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                // 2. Update Charts data
                if (window.statusChartInstance) {
                    window.statusChartInstance.data.labels = data.statusLabels;
                    window.statusChartInstance.data.datasets[0].data = data.statusData;
                    window.statusChartInstance.update();
                }

                if (window.categoryChartInstance) {
                    window.categoryChartInstance.data.labels = data.categoryLabels;
                    window.categoryChartInstance.data.datasets[0].data = data.categoryData;
                    window.categoryChartInstance.update();
                }

                if (window.branchChartInstance) {
                    window.branchChartInstance.data.labels = data.branchLabels;
                    window.branchChartInstance.data.datasets[0].data = data.branchData;
                    window.branchChartInstance.update();
                }

                if (window.salesTrendChartInstance) {
                    window.salesTrendChartInstance.data.labels = data.monthlySalesLabels;
                    window.salesTrendChartInstance.data.datasets[0].data = data.monthlySalesData;
                    window.salesTrendChartInstance.update();
                }

                // 3. Update Recent Bulk Inwards table
                const inwardsTbody = document.getElementById('table-recent-inwards');
                if (inwardsTbody) {
                    inwardsTbody.innerHTML = '';
                    if (data.recentInwards && data.recentInwards.length > 0) {
                        data.recentInwards.forEach(inward => {
                            let qty = 0;
                            if (inward.items) {
                                inward.items.forEach(item => { qty += parseFloat(item.qty); });
                            }
                            let dateStr = '—';
                            if (inward.invoice_date) {
                                const d = new Date(inward.invoice_date);
                                dateStr = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                            }
                            const row = document.createElement('tr');
                            row.className = 'hover:bg-slate-50 dark:hover:bg-slate-700/40';
                            row.innerHTML = `
                                <td class="px-3 py-2.5 font-semibold text-indigo-600 dark:text-indigo-400">
                                    #${inward.invoice_no}
                                </td>
                                <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-medium">
                                    ${inward.supplier ? inward.supplier.supplier_name : '—'}
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                    ${inward.branch ? inward.branch.name : '—'}
                                </td>
                                <td class="px-3 py-2.5 text-right font-bold text-slate-800 dark:text-slate-200">
                                    ${qty.toLocaleString()}
                                </td>
                                <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">
                                    ${dateStr}
                                </td>
                            `;
                            inwardsTbody.appendChild(row);
                        });
                    } else {
                        inwardsTbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400">
                                    No recent stock inwards recorded.
                                </td>
                            </tr>
                        `;
                    }
                }

                // 4. Update Recent Sales table
                const salesTbody = document.getElementById('table-recent-sales');
                if (salesTbody) {
                    salesTbody.innerHTML = '';
                    if (data.recentSales && data.recentSales.length > 0) {
                        data.recentSales.forEach(sale => {
                            let dateStr = '—';
                            if (sale.invoice_date) {
                                const d = new Date(sale.invoice_date);
                                dateStr = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                            }
                            const isCompleted = sale.status === 'completed';
                            const statusBadgeClass = isCompleted 
                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' 
                                : 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300';
                            
                            const row = document.createElement('tr');
                            row.className = 'hover:bg-slate-50 dark:hover:bg-slate-700/40';
                            row.innerHTML = `
                                <td class="px-3 py-2.5 text-indigo-600 dark:text-indigo-400 font-semibold">
                                    #${sale.invoice_no_display || sale.invoice_no}
                                </td>
                                <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-medium">
                                    ${sale.customer ? sale.customer.customer_name : '—'}
                                </td>
                                <td class="px-3 py-2.5 text-slate-600 dark:text-slate-400">
                                    ${sale.branch ? sale.branch.name : '—'}
                                </td>
                                <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap font-medium">
                                    ${dateStr}
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ${statusBadgeClass}">
                                        ${isCompleted ? 'Completed' : 'Cancelled'}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right font-bold text-slate-900 dark:text-white">
                                    ₹${parseFloat(sale.grand_total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                </td>
                            `;
                            salesTbody.appendChild(row);
                        });
                    } else {
                        salesTbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-400">No sales transactions found.</td>
                            </tr>
                        `;
                    }
                }
            })
            .catch(err => {
                console.error("Failed to load branch filtered metrics:", err);
            });
        }

        // Initialize with persisted tab (or default to live-cockpit)
        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('active_dashboard_tab') || 'live-cockpit';
            switchDashboardTab(activeTab);
        });
    </script>

    <!-- Chart Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js library is not loaded. Dashboard charts initialization skipped.');
                return;
            }
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#cbd5e1' : '#475569';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

            // 1. Status Distribution (Doughnut Chart - Live Cockpit)
            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                window.statusChartInstance = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusData),
                            backgroundColor: [
                                '#6366f1', // Available (Indigo)
                                '#06b6d4', // Counter Transferred (Cyan)
                                '#3b82f6', // Branch Transferred (Blue)
                                '#f59e0b', // Reserved (Amber)
                                '#10b981', // Sold (Emerald)
                                '#ef4444', // Damaged (Red)
                                '#8b5cf6', // Under Repair (Violet)
                                '#64748b'  // Disposed (Slate)
                            ],
                            borderWidth: 2,
                            borderColor: isDark ? '#1e293b' : '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 2. Category Chart (Bar - Live Cockpit)
            const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
            if (categoryCtx) {
                window.categoryChartInstance = new Chart(categoryCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($categoryLabels),
                        datasets: [{
                            label: 'Available Items',
                            data: @json($categoryData),
                            backgroundColor: '#10b981',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 10 }, precision: 0 },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            // 3. Branch Chart (Bar - Live Cockpit)
            const branchCtx = document.getElementById('branchChart')?.getContext('2d');
            if (branchCtx) {
                window.branchChartInstance = new Chart(branchCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($branchLabels),
                        datasets: [{
                            label: 'Available Items',
                            data: @json($branchData),
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 10 }, precision: 0 },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            // 4. CRM Customer Distribution Chart (Doughnut)
            const crmCtx = document.getElementById('crmChart')?.getContext('2d');
            if (crmCtx) {
                new Chart(crmCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['B2B Customers', 'B2C Customers'],
                        datasets: [{
                            data: [{{ $b2bCustomers }}, {{ $b2cCustomers }}],
                            backgroundColor: ['#8b5cf6', '#10b981'],
                            borderWidth: 2,
                            borderColor: isDark ? '#1e293b' : '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 5. Sales Trend Chart (Line)
            const salesTrendCtx = document.getElementById('salesTrendChart')?.getContext('2d');
            if (salesTrendCtx) {
                window.salesTrendChartInstance = new Chart(salesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlySalesLabels),
                        datasets: [{
                            label: 'Gross Monthly Revenue (₹)',
                            data: @json($monthlySalesData),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.08)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                labels: { color: textColor }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { color: gridColor }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            // 6. Status Chart (Stock Page)
            const statusStockPageCtx = document.getElementById('statusChartStockPage')?.getContext('2d');
            if (statusStockPageCtx) {
                new Chart(statusStockPageCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusData),
                            backgroundColor: [
                                '#6366f1', '#06b6d4', '#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#64748b'
                            ],
                            borderWidth: 2,
                            borderColor: isDark ? '#1e293b' : '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 7. Category Chart (Stock Page)
            const categoryStockPageCtx = document.getElementById('categoryChartStockPage')?.getContext('2d');
            if (categoryStockPageCtx) {
                new Chart(categoryStockPageCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($categoryLabels),
                        datasets: [{
                            label: 'Available Items',
                            data: @json($categoryData),
                            backgroundColor: '#10b981',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 10 }, precision: 0 },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            // 8. Branch Chart (Stock Page)
            const branchStockPageCtx = document.getElementById('branchChartStockPage')?.getContext('2d');
            if (branchStockPageCtx) {
                new Chart(branchStockPageCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($branchLabels),
                        datasets: [{
                            label: 'Available Items',
                            data: @json($branchData),
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { size: 10 } },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 10 }, precision: 0 },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>