<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Page Header -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Convert Quotation to Sales Invoice
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Review quotation details, payment modes, and generate live billing invoice #{{ $quotation->quotation_no }}.
                    </p>
                </div>

                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg transition">
                    ← Cancel & Back to Quotations
                </a>
            </div>

            <!-- Conversion Form -->
            <form action="{{ route('sales.convert', $quotation->id) }}" method="POST" id="sales_conversion_form" class="space-y-6">
                @csrf

                <!-- Partials -->
                @include('sales.partials.customer')

                @include('sales.partials.items')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        @include('sales.partials.payment')
                    </div>
                    <div class="lg:col-span-1">
                        @include('sales.partials.summary')
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-4 bg-white dark:bg-[#182035] p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <a href="{{ route('quotations.index') }}" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition">
                        Cancel
                    </a>

                    <button type="submit" id="submit_conversion_btn" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Convert To Sales Invoice
                    </button>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/sales/sales_form.js') }}"></script>
    @endpush
</x-app-layout>
