<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Stock Inward — {{ $stockInward->invoice_no }}
            </h2>
            <a href="{{ route('stock-inwards.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if($stockInward->hasAllocatedItems())
            <div class="bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm flex items-center gap-3">
                <x-heroicon-o-lock-closed class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0" />
                <div>
                    <h4 class="font-bold text-amber-900 dark:text-amber-200 text-sm">Status: Allocation Started (🔒 Locked)</h4>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5">Bulk Inward cannot be edited because item allocation has already started.</p>
                </div>
            </div>
            @else
            <form action="{{ route('stock-inwards.update', $stockInward) }}" method="POST">
                @csrf
                @method('PUT')
                @include('inventory.stock_inwards._form')
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
