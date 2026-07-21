<!-- Individual Item Allocation Modal -->
<div id="allocation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <x-heroicon-o-cube-transparent class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Individual Item Allocation</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Convert bulk stock lines into unique trackable stock items</p>
                </div>
            </div>
            <button type="button" onclick="closeAllocationModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">

            <!-- Summary Card -->
            <div class="bg-gradient-to-br from-indigo-50/50 to-slate-50 dark:from-slate-900/80 dark:to-slate-800/80 border border-indigo-100 dark:border-indigo-500/20 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-700/80">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Bulk Inward Summary</span>
                    <span id="alloc-modal-invoice-no" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300"></span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Supplier</span>
                        <span id="alloc-modal-supplier" class="font-semibold text-slate-800 dark:text-slate-200 truncate block"></span>
                    </div>

                    <div>
                        <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Category</span>
                        <span id="alloc-modal-category" class="font-semibold text-slate-800 dark:text-slate-200 truncate block"></span>
                    </div>

                    <div class="col-span-2">
                        <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Product</span>
                        <span id="alloc-modal-product" class="font-bold text-slate-900 dark:text-white truncate block"></span>
                    </div>
                </div>

                <!-- Quantity Counters Badge Bar -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-center">
                    <div class="bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Received</span>
                        <span id="alloc-modal-received-qty" class="text-sm font-mono font-bold text-slate-700 dark:text-slate-200">0</span>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Allocated</span>
                        <span id="alloc-modal-allocated-qty" class="text-sm font-mono font-bold text-emerald-600 dark:text-emerald-400">0</span>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Pending</span>
                        <span id="alloc-modal-pending-qty" class="text-sm font-mono font-bold text-amber-600 dark:text-amber-400">0</span>
                    </div>
                </div>
            </div>

            <!-- Allocation Form -->
            <form id="allocation-form" onsubmit="submitAllocationForm(event)" class="space-y-4">
                <input type="hidden" id="alloc-stock-inward-item-id" name="stock_inward_item_id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    
                    <!-- Counter Selection (Mandatory) -->
                    <div class="col-span-2 sm:col-span-1">
                        <label for="alloc-counter-id" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Counter <span class="text-rose-500">*</span>
                        </label>
                        <select id="alloc-counter-id" name="counter_id" required
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Counter --</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Mandatory. Counter where allocated items will reside.</p>
                    </div>

                    <!-- Size Selection (Optional) -->
                    <div>
                        <label for="alloc-size-id" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Size <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <select id="alloc-size-id" name="size_id"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- None --</option>
                        </select>
                    </div>

                    <!-- Sub Product Selection (Optional) -->
                    <div class="col-span-2 sm:col-span-1">
                        <label for="alloc-sub-product-id" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Sub Product / Variant <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <select id="alloc-sub-product-id" name="sub_product_id"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- None --</option>
                        </select>
                    </div>

                    <!-- Quantity Input (Bulk Generation Mode) -->
                    <div id="alloc-bulk-quantity-container" class="col-span-2 sm:col-span-1">
                        <label for="alloc-quantity" class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Quantity to Allocate <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="alloc-quantity" name="quantity" min="1" step="1"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-mono font-bold focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-[10px] text-slate-400 mt-1">Enter quantity to bulk generate item codes at once.</p>
                    </div>

                </div>

                <!-- Individual Mode Notice -->
                <div id="alloc-individual-mode-notice" class="hidden p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl text-xs text-amber-800 dark:text-amber-300 flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-amber-500 shrink-0" />
                    <span>Product is set to <strong>Individual Generate</strong>. Every click generates exactly one item code.</span>
                </div>

                <!-- Allocation Completed Alert -->
                <div id="alloc-completed-alert" class="hidden p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-xl text-xs text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-500 shrink-0" />
                    <span><strong>Allocation Completed:</strong> All received quantity for this inward detail line has been fully allocated into individual items.</span>
                </div>

                <!-- Form Action Footer -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAllocationModal()"
                        class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                        Close
                    </button>

                    <button type="submit" id="alloc-submit-btn"
                        class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-heroicon-o-sparkles class="w-4 h-4" />
                        <span id="alloc-btn-text">Generate Items</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="{{ asset('js/inventory/allocation.js') }}" defer></script>
