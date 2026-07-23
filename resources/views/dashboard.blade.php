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
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                <span class="relative flex h-2 w-2 mr-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Live System
            </span>
        </div>
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Available Stock Card -->
                <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Allocated Stock</p>
                        <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalAvailableStock) }}</h3>
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
                        <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalStockInwards) }}</h3>
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
                        <h3 class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($totalProducts) }}</h3>
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
                        <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalBranches) }}</h3>
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
    </div>

    <!-- Chart Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#cbd5e1' : '#475569';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

            // 1. Status Distribution (Doughnut Chart)
            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                new Chart(statusCtx, {
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
                                labels: { color: textColor, font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // 2. Category Chart (Bar)
            const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
            if (categoryCtx) {
                new Chart(categoryCtx, {
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
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 11 }, precision: 0 },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            // 3. Branch Chart (Bar)
            const branchCtx = document.getElementById('branchChart')?.getContext('2d');
            if (branchCtx) {
                new Chart(branchCtx, {
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
                                ticks: { color: textColor, font: { size: 11 } },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { size: 11 }, precision: 0 },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>