<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Header Section -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Sales Invoices
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Manage billing invoices, payments, receivables, and historical sales records.
                    </p>
                </div>

                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Convert Quotation to Invoice
                </a>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white dark:bg-[#182035] rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-800">
                <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Invoice No / Customer..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <select name="sale_type" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Sale Types</option>
                            <option value="1" {{ request('sale_type') == '1' ? 'selected' : '' }}>Cash Sale</option>
                            <option value="2" {{ request('sale_type') == '2' ? 'selected' : '' }}>Credit Sale</option>
                        </select>
                    </div>

                    <div>
                        <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-[#0f1422] text-slate-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Completed</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 px-4 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-lg transition">
                            Filter
                        </button>
                        <a href="{{ route('sales.index') }}" class="py-2 px-3 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-300 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Invoices Datatable Card -->
            <div class="bg-white dark:bg-[#182035] rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                @include('sales._table')
            </div>

        </div>
    </div>
</x-app-layout>
