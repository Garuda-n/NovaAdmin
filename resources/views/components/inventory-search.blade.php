@props([
    'name',
    'value' => '',
    'textValue' => '',
    'placeholder' => 'Search item...',
    'disabled' => false,
    'required' => false,
    'stockItemId' => '',
    'stockItemName' => '',
    'trackingType' => '',
    'availableQty' => '',
])

@php
    $namePrefix = preg_replace('/\[product_id\]$/', '', $name);
@endphp

<div class="inventory-search-container relative w-full">
    <!-- Visible Trigger Input -->
    <div class="relative">
        <input 
            type="text" 
            class="inventory-search-input w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-1.5 focus:ring-indigo-500 focus:border-indigo-500 pr-8 cursor-pointer"
            placeholder="{{ $placeholder }}"
            value="{{ $textValue }}"
            readonly
            autocomplete="off"
            {{ $disabled ? 'disabled' : '' }}
            {{ $required && !$value ? 'required' : '' }}>
            
        <button type="button" class="inventory-search-clear absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 {{ $value ? '' : 'hidden' }}" {{ $disabled ? 'disabled' : '' }}>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Hidden inputs for form submission -->
    <input type="hidden" name="{{ $name }}" class="product-id-input" value="{{ $value }}">
    <input type="hidden" name="{{ $namePrefix . '[stock_item_id]' }}" class="stock-item-id-input" value="{{ $stockItemId }}">
    <input type="hidden" name="{{ $namePrefix . '[tracking_type]' }}" class="tracking-type-input" value="{{ $trackingType }}">
    <input type="hidden" name="{{ $namePrefix . '[available_qty]' }}" class="available-qty-input" value="{{ $availableQty }}">

    <!-- Visible item info summary badge -->
    <div class="inventory-item-info text-[10px] mt-1 font-semibold text-indigo-600 dark:text-indigo-400">
        @if($value)
            @if($trackingType == 2)
                Item: {{ $stockItemName ?: 'Individual' }} | Available: {{ $availableQty }}
            @else
                Available: {{ $availableQty }}
            @endif
        @endif
    </div>
    <div class="qty-warning-container text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5 hidden">
        ⚠️ Exceeds available stock ({{ $availableQty }})
    </div>

    <!-- Popup Modal Search -->
    <div class="inventory-search-modal fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-xl w-full max-h-[85vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 shrink-0">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Search Available Stock
                </h3>
                <button type="button" class="close-search-modal p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-5 overflow-y-auto space-y-4 flex-1">
                <!-- Autocomplete Input inside modal -->
                <div class="relative">
                    <input 
                        type="text" 
                        class="modal-search-input w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs py-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                        placeholder="Type item code, product code, or product name..."
                        autocomplete="off">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                
                <!-- Autocomplete Results list inside modal -->
                <div class="modal-search-results border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="p-4 text-center text-xs text-slate-500 dark:text-slate-400">Type at least 2 characters to search inventory stock...</div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-900/80 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2 shrink-0">
                <button type="button" class="close-search-modal px-4 py-2 text-xs font-semibold rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
