@php
    $oldItems = old('items', isset($stockInward) ? $stockInward->items->toArray() : [
        ['product_id' => '', 'sub_product_id' => '', 'qty' => 1, 'weight' => '', 'purchase_price' => '', 'selling_price' => '', 'mrp' => '', 'remarks' => '']
    ]);
@endphp

<div x-data="stockInwardForm({{ json_encode($oldItems) }}, {{ json_encode($products) }})" class="space-y-6">

    <!-- Header Section Card -->
    <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border border-gray-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-slate-700 pb-3 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-indigo-500" />
            Header Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Company -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Company <span class="text-red-500">*</span>
                </label>
                <select
                    name="company_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">— Select Company —</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $stockInward->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Branch -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Branch <span class="text-red-500">*</span>
                </label>
                <select
                    name="branch_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">— Select Branch —</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $stockInward->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->branch_code }})
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Counter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Counter
                </label>
                <select
                    name="counter_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— Select Counter (Optional) —</option>
                    @foreach($counters as $counter)
                        <option value="{{ $counter->id }}" {{ old('counter_id', $stockInward->counter_id ?? '') == $counter->id ? 'selected' : '' }}>
                            {{ $counter->counter_name }} ({{ $counter->counter_code }})
                        </option>
                    @endforeach
                </select>
                @error('counter_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Supplier <span class="text-red-500">*</span>
                </label>
                <select
                    name="supplier_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">— Select Supplier —</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $stockInward->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }} ({{ $supplier->supplier_code }})
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Invoice Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Invoice Number <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="invoice_no"
                    value="{{ old('invoice_no', $stockInward->invoice_no ?? '') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g. INV-2026-001"
                    required>
                @error('invoice_no')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Invoice Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Invoice Date <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    name="invoice_date"
                    value="{{ old('invoice_date', isset($stockInward->invoice_date) ? $stockInward->invoice_date->format('Y-m-d') : date('Y-m-d')) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                @error('invoice_date')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Header Remarks -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Remarks / Notes
                </label>
                <input
                    type="text"
                    name="remarks"
                    value="{{ old('remarks', $stockInward->remarks ?? '') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Optional remarks regarding this bulk inward invoice">
                @error('remarks')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Items Detail Table Card -->
    <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border border-gray-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-slate-700 pb-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <x-heroicon-o-cube-transparent class="w-5 h-5 text-indigo-500" />
                Inward Stock Items
            </h3>
            <button
                type="button"
                @click="addRow()"
                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" /> Add Row
            </button>
        </div>

        @error('items')
            <div class="p-3 mb-4 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 text-sm">
                {{ $message }}
            </div>
        @enderror

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/60">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 min-w-[200px]">Product <span class="text-red-500">*</span></th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 min-w-[160px]">Sub Product</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 w-24">Qty <span class="text-red-500">*</span></th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 w-28">Weight</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 w-28">Purchase Price</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 w-28">Selling Price</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 w-28">MRP</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 min-w-[140px]">Remarks</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 w-12">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    <template x-for="(row, index) in items" :key="index">
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                            
                            <!-- Product -->
                            <td class="px-2 py-2">
                                <select
                                    :name="'items['+index+'][product_id]'"
                                    x-model="row.product_id"
                                    @change="onProductChange(row)"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    <option value="">— Select Product —</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">
                                            {{ $prod->code }} - {{ $prod->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- Sub Product -->
                            <td class="px-2 py-2">
                                <select
                                    :name="'items['+index+'][sub_product_id]'"
                                    x-model="row.sub_product_id"
                                    :disabled="!hasSubProducts(row.product_id)"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 dark:disabled:bg-slate-800/60 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="" x-text="hasSubProducts(row.product_id) ? '— Select Sub Product —' : '— N/A —'"></option>
                                    <template x-for="sp in getSubProducts(row.product_id)" :key="sp.id">
                                        <option :value="sp.id" x-text="sp.code + ' (' + sp.name + ')'" :selected="row.sub_product_id == sp.id"></option>
                                    </template>
                                </select>
                            </td>

                            <!-- Qty -->
                            <td class="px-2 py-2">
                                <input
                                    type="number"
                                    step="0.001"
                                    min="0.001"
                                    :name="'items['+index+'][qty]'"
                                    x-model="row.qty"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="1.000"
                                    required>
                            </td>

                            <!-- Weight -->
                            <td class="px-2 py-2">
                                <input
                                    type="number"
                                    step="0.001"
                                    min="0"
                                    :name="'items['+index+'][weight]'"
                                    x-model="row.weight"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0.000">
                            </td>

                            <!-- Purchase Price -->
                            <td class="px-2 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :name="'items['+index+'][purchase_price]'"
                                    x-model="row.purchase_price"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0.00">
                            </td>

                            <!-- Selling Price -->
                            <td class="px-2 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :name="'items['+index+'][selling_price]'"
                                    x-model="row.selling_price"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0.00">
                            </td>

                            <!-- MRP -->
                            <td class="px-2 py-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :name="'items['+index+'][mrp]'"
                                    x-model="row.mrp"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0.00">
                            </td>

                            <!-- Remarks -->
                            <td class="px-2 py-2">
                                <input
                                    type="text"
                                    :name="'items['+index+'][remarks]'"
                                    x-model="row.remarks"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Item remark">
                            </td>

                            <!-- Delete Action -->
                            <td class="px-2 py-2 text-center">
                                <button
                                    type="button"
                                    @click="removeRow(index)"
                                    :disabled="items.length === 1"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white disabled:opacity-40 disabled:cursor-not-allowed transition"
                                    title="Remove item">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
            <div>
                Total Rows: <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="items.length"></span>
            </div>
            <button
                type="button"
                @click="addRow()"
                class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-700 text-sm font-medium rounded-lg transition">
                + Add Another Item
            </button>
        </div>
    </div>

    <!-- Action Footer -->
    <div class="flex justify-end gap-3 pt-4">
        <a href="{{ route('stock-inwards.index') }}" class="px-5 py-2.5 rounded-lg bg-gray-500 text-white hover:bg-gray-600 transition">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 shadow-md transition">
            Save Bulk Inward
        </button>
    </div>

</div>

<script src="{{ asset('js/inventory/stock_inward_form.js') }}" defer></script>
