<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Stock Transfer #{{ $stockTransfer->transfer_no }}
            </h2>

            <a href="{{ route('stock-transfers.show', $stockTransfer->id) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-slate-700 border border-transparent rounded-lg font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-slate-600 transition text-sm">
                ← Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if ($errors->any())
                <div class="p-4 bg-rose-100 border-l-4 border-rose-500 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 rounded shadow-sm">
                    <div class="font-bold mb-1">Please fix the following validation errors:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('inventory.stock_transfers._form', ['stockTransfer' => $stockTransfer])
        </div>
    </div>
</x-app-layout>
