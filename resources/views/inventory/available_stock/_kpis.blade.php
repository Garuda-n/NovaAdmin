<div id="available-stock-kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Opening Qty -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center space-x-4">
        <div class="p-3 bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 rounded-xl">
            <x-heroicon-o-folder-open class="w-6 h-6" />
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Opening Qty</p>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 font-mono mt-0.5">
                {{ number_format($summary['total_opening'] ?? 0, 2) }}
            </h3>
        </div>
    </div>

    <!-- Card 2: Total Inward Qty -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center space-x-4">
        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
            <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Inward Qty</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">
                {{ number_format($summary['total_inward'] ?? 0, 2) }}
            </h3>
        </div>
    </div>

    <!-- Card 3: Total Outward Qty -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center space-x-4">
        <div class="p-3 bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-xl">
            <x-heroicon-o-arrow-up-tray class="w-6 h-6" />
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Outward Qty</p>
            <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">
                {{ number_format($summary['total_outward'] ?? 0, 2) }}
            </h3>
        </div>
    </div>

    <!-- Card 4: Total Closing Qty -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center space-x-4">
        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
            <x-heroicon-o-cube class="w-6 h-6" />
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Closing Qty</p>
            <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 font-mono mt-0.5">
                {{ number_format($summary['total_closing'] ?? 0, 2) }}
            </h3>
        </div>
    </div>
</div>
