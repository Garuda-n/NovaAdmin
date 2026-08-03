@props([
    'resetAction' => 'resetFilter',
    'exportExcel' => true,
    'exportPdf'   => true,
    'exportPrint' => true,
])

<div class="sm:col-span-2 md:col-span-4 flex flex-wrap items-center justify-end gap-2 pt-3 border-t border-gray-200 dark:border-slate-700">

    @if($exportExcel)
        <button type="button" @click="exportCsv ? exportCsv() : null"
                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5 shadow-sm">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Excel
        </button>
    @endif

    @if($exportPdf)
        <button type="button" @click="exportPdf ? exportPdf() : null"
                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5 shadow-sm">
            <x-heroicon-o-document-arrow-down class="w-4 h-4" /> PDF
        </button>
    @endif

    @if($exportPrint)
        <button type="button" onclick="window.print()"
                class="px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5 shadow-sm">
            <x-heroicon-o-printer class="w-4 h-4" /> Print
        </button>
    @endif

    <button type="button" @click="{{ $resetAction }}()"
            class="px-3.5 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition shadow-sm">
        Reset
    </button>

    <button type="submit"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5 shadow-sm">
        <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Search
    </button>

</div>
