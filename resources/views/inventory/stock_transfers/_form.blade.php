@php
    $isEdit = isset($stockTransfer);
@endphp

<div x-data="stockTransferForm()" x-init="initForm()">
    <form id="stockTransferForm" method="POST" action="{{ $isEdit ? route('stock-transfers.update', $stockTransfer->id) : route('stock-transfers.store') }}" class="space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- General Transfer Information -->
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 space-y-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-3 border-gray-200 dark:border-slate-700">
                Transfer Details & Locations
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Company -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company <span class="text-rose-500">*</span></label>
                    <select name="company_id" x-model="form.company_id" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (old('company_id', $stockTransfer->company_id ?? $defaultCompanyId ?? '') == $company->id) ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Transfer Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transfer Type <span class="text-rose-500">*</span></label>
                    <select name="transfer_type" x-model.number="form.transfer_type" @change="onTransferTypeChange()" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold">
                        <option value="1">Branch Transfer (Branch → Branch)</option>
                        <option value="2">Counter Transfer (Counter → Counter)</option>
                    </select>
                </div>

                <!-- Transfer Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transfer Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="transfer_date" x-model="form.transfer_date" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Source Branch -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source Branch <span class="text-rose-500">*</span></label>
                    <select name="source_branch_id" x-model.number="form.source_branch_id" @change="onSourceBranchChange()" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Source Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Source Counter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Source Counter <span x-show="form.transfer_type == 2" class="text-rose-500">*</span>
                    </label>
                    <select name="source_counter_id" x-model.number="form.source_counter_id" :required="form.transfer_type == 2" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Source Counter</option>
                        @foreach($counters as $counter)
                            <option value="{{ $counter->id }}" data-branch="{{ $counter->branch_id }}">{{ $counter->counter_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="hidden md:block"></div>

                <!-- Destination Branch -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destination Branch <span class="text-rose-500">*</span></label>
                    <select name="destination_branch_id" x-model.number="form.destination_branch_id" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Destination Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Destination Counter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Destination Counter <span x-show="form.transfer_type == 2" class="text-rose-500">*</span>
                    </label>
                    <select name="destination_counter_id" x-model.number="form.destination_counter_id" :required="form.transfer_type == 2" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select Destination Counter</option>
                        @foreach($counters as $counter)
                            <option value="{{ $counter->id }}" data-branch="{{ $counter->branch_id }}">{{ $counter->counter_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Remarks -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <input type="text" name="remarks" x-model="form.remarks" placeholder="Optional notes regarding this transfer..." class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
        </div>

        <!-- Add Items Section -->
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 space-y-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-3 border-gray-200 dark:border-slate-700">
                Transfer Items
            </h3>

            <!-- Item Search Bar -->
            <div class="relative">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search Available Inventory (Serial / Product Code / Name)</label>
                <input
                    type="text"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchItems()"
                    placeholder="Type barcode, serial number or product name to add..."
                    :disabled="!form.source_branch_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">

                <p x-show="!form.source_branch_id" class="text-xs text-amber-600 dark:text-amber-400 mt-1">Please select a Source Branch first to enable item search.</p>

                <!-- Search Results Dropdown -->
                <div x-show="searchResults.length > 0 && searchQuery.length >= 2"
                     @click.away="searchResults = []"
                     x-cloak
                     class="absolute z-20 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-slate-700">
                    <template x-for="result in searchResults" :key="result.stock_item_id ? result.stock_item_id : 'p-' + result.product_id">
                        <div @click="addItem(result)" class="p-3 hover:bg-indigo-50 dark:hover:bg-slate-700/60 cursor-pointer flex justify-between items-center transition">
                            <div>
                                <div class="font-semibold text-sm text-gray-900 dark:text-gray-100" x-text="result.product_name"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Code: <span class="font-mono" x-text="result.product_code"></span>
                                    <template x-if="result.item_code">
                                        <span> | Serial: <strong class="text-indigo-600 dark:text-indigo-400" x-text="result.item_code"></strong></span>
                                    </template>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                      :class="result.tracking_type == 2 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'"
                                      x-text="result.tracking_type == 2 ? 'Serial Item' : 'Available: ' + result.available_qty + ' ' + result.uom_name">
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Selected Transfer Items Table -->
            <div class="overflow-x-auto border border-gray-200 dark:border-slate-700 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tracking Mode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Serial / Item Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-36">Transfer Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                        <template x-for="(item, index) in form.items" :key="index">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono" x-text="index + 1"></td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    <span x-text="item.product_name"></span>
                                    <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                                    <input type="hidden" :name="'items[' + index + '][tracking_type]'" :value="item.tracking_type">
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold"
                                          :class="item.tracking_type == 2 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'"
                                          x-text="item.tracking_type == 2 ? 'Individual (Serial)' : 'Quantity (Bulk)'">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-indigo-600 dark:text-indigo-400 font-semibold">
                                    <span x-text="item.item_code || '-'"></span>
                                    <input type="hidden" :name="'items[' + index + '][stock_item_id]'" :value="item.stock_item_id">
                                    <input type="hidden" :name="'items[' + index + '][item_code]'" :value="item.item_code">
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <template x-if="item.tracking_type == 2">
                                        <div class="font-bold text-gray-800 dark:text-gray-200">1.00</div>
                                    </template>
                                    <template x-if="item.tracking_type == 1">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            :max="item.available_qty"
                                            :name="'items[' + index + '][transferred_qty]'"
                                            x-model.number="item.transferred_qty"
                                            required
                                            class="w-32 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm p-1.5 focus:ring-indigo-500 focus:border-indigo-500">
                                    </template>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <button type="button" @click="removeItem(index)" class="text-rose-600 hover:text-rose-900 dark:hover:text-rose-400 font-semibold">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="form.items.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                No items added to this stock transfer yet. Search above to add items.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('stock-transfers.index') }}" class="px-5 py-2.5 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                Cancel
            </a>
            <button
                type="submit"
                :disabled="form.items.length === 0"
                class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition disabled:opacity-50 shadow-sm">
                {{ $isEdit ? 'Update Draft Transfer' : 'Save as Draft' }}
            </button>
        </div>
    </form>
</div>

<script>
    function stockTransferForm() {
        return {
            form: {
                company_id: '{{ old("company_id", $stockTransfer->company_id ?? $defaultCompanyId ?? 1) }}',
                transfer_type: {{ old('transfer_type', $stockTransfer->transfer_type->value ?? 1) }},
                source_branch_id: {{ old('source_branch_id', $stockTransfer->source_branch_id ?? 'null') }},
                source_counter_id: {{ old('source_counter_id', $stockTransfer->source_counter_id ?? 'null') }},
                destination_branch_id: {{ old('destination_branch_id', $stockTransfer->destination_branch_id ?? 'null') }},
                destination_counter_id: {{ old('destination_counter_id', $stockTransfer->destination_counter_id ?? 'null') }},
                transfer_date: '{{ old("transfer_date", isset($stockTransfer->transfer_date) ? $stockTransfer->transfer_date->format("Y-m-d") : now()->format("Y-m-d")) }}',
                remarks: '{{ old("remarks", $stockTransfer->remarks ?? "") }}',
                items: []
            },
            searchQuery: '',
            searchResults: [],

            initForm() {
                @if(isset($stockTransfer) && $stockTransfer->details->count() > 0)
                    this.form.items = [
                        @foreach($stockTransfer->details as $d)
                        {
                            product_id: {{ $d->product_id }},
                            product_name: '{{ addslashes($d->product->name ?? "") }}',
                            tracking_type: {{ $d->tracking_type }},
                            stock_item_id: {{ $d->stock_item_id ?? 'null' }},
                            item_code: '{{ $d->item_code ?? "" }}',
                            transferred_qty: {{ $d->transferred_qty }},
                            available_qty: 9999,
                            unit_cost: {{ $d->unit_cost ?? 0 }}
                        },
                        @endforeach
                    ];
                @endif
            },

            onTransferTypeChange() {
                if (this.form.transfer_type == 1) {
                    this.form.source_counter_id = null;
                    this.form.destination_counter_id = null;
                }
            },

            onSourceBranchChange() {
                this.form.items = [];
                this.searchQuery = '';
                this.searchResults = [];
            },

            searchItems() {
                if (!this.searchQuery || this.searchQuery.trim().length < 2 || !this.form.source_branch_id) {
                    this.searchResults = [];
                    return;
                }

                let url = `/api/inventory/search?search=${encodeURIComponent(this.searchQuery)}&branch_id=${this.form.source_branch_id}`;
                if (this.form.source_counter_id) {
                    url += `&counter_id=${this.form.source_counter_id}`;
                }

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            this.searchResults = data;
                        } else if (data && Array.isArray(data.data)) {
                            this.searchResults = data.data;
                        } else {
                            this.searchResults = [];
                        }
                    })
                    .catch(err => console.error(err));
            },

            addItem(result) {
                if (result.tracking_type == 2) {
                    const exists = this.form.items.some(i => i.stock_item_id === result.stock_item_id);
                    if (exists) {
                        alert('This serialized item is already added to the transfer list.');
                        return;
                    }

                    this.form.items.push({
                        product_id: result.product_id,
                        product_name: result.product_name,
                        tracking_type: 2,
                        stock_item_id: result.stock_item_id,
                        item_code: result.item_code,
                        transferred_qty: 1.00,
                        available_qty: 1.00,
                        unit_cost: result.rate || 0
                    });
                } else {
                    const existing = this.form.items.find(i => i.product_id === result.product_id && i.tracking_type == 1);
                    if (existing) {
                        existing.transferred_qty = Math.min(existing.available_qty, existing.transferred_qty + 1);
                    } else {
                        this.form.items.push({
                            product_id: result.product_id,
                            product_name: result.product_name,
                            tracking_type: 1,
                            stock_item_id: null,
                            item_code: null,
                            transferred_qty: 1.00,
                            available_qty: result.available_qty,
                            unit_cost: result.rate || 0
                        });
                    }
                }

                this.searchQuery = '';
                this.searchResults = [];
            },

            removeItem(index) {
                this.form.items.splice(index, 1);
            }
        };
    }
</script>
