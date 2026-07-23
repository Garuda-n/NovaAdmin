<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Header Section -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Edit Quotation #{{ $quotation->quotation_no ?? $quotation->id }}
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Update existing quotation details and line items.
                    </p>
                </div>

                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg transition">
                    ← Back to List
                </a>
            </div>

            <!-- Form Wrapper -->
            <form action="{{ route('quotations.update', $quotation) }}" method="POST">
                @csrf
                @method('PUT')
                @include('quotations._form')
            </form>

        </div>
    </div>
</x-app-layout>
