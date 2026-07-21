<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <x-heroicon-o-cube class="w-6 h-6 text-indigo-500" />
                    Available Stock
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Displays all inventory items that are currently available for business operations.
                </p>
            </div>
        </div>
    </x-slot>

    <script src="{{ asset('js/inventory/available_stock_filter.js') }}"></script>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="availableStockFilter({
            branch_id: '{{ request('branch_id') }}',
            counter_id: '{{ request('counter_id') }}',
            category_id: '{{ request('category_id') }}',
            product_id: '{{ request('product_id') }}',
            sub_product_id: '{{ request('sub_product_id') }}',
            size_id: '{{ request('size_id') }}',
            item_code: '{{ request('item_code') }}',
            filterUrl: '{{ route('available-stock.filter') }}',
            csrfToken: '{{ csrf_token() }}'
        })">

            <x-toast />

            <!-- Filter Panel -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-5 border border-gray-200 dark:border-slate-700">
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('available-stock.filter') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @csrf

                    <!-- Branch Filter -->
                    <div>
                        <label for="branch_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Branch</label>
                        <select id="branch_id" name="branch_id" x-model="branch_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Branches</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->branch_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Counter Filter -->
                    <div>
                        <label for="counter_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Counter</label>
                        <select id="counter_id" name="counter_id" x-model="counter_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Counters</option>
                            @foreach($counters as $c)
                                <option value="{{ $c->id }}">{{ $c->counter_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Category</label>
                        <select id="category_id" name="category_id" x-model="category_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Filter -->
                    <div>
                        <label for="product_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Product</label>
                        <select id="product_id" name="product_id" x-model="product_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Products</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Product Filter -->
                    <div>
                        <label for="sub_product_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Sub Product</label>
                        <select id="sub_product_id" name="sub_product_id" x-model="sub_product_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Sub Products</option>
                            @foreach($subProducts as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Size Filter -->
                    <div>
                        <label for="size_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Size</label>
                        <select id="size_id" name="size_id" x-model="size_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Sizes</option>
                            @foreach($sizes as $sz)
                                <option value="{{ $sz->id }}">{{ $sz->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Item Code Input -->
                    <div>
                        <label for="item_code" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Item Code</label>
                        <input type="text" id="item_code" name="item_code" x-model="item_code" placeholder="Enter Item Code..."
                               class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Action Buttons -->
                    <div class="sm:col-span-2 md:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-gray-200 dark:border-slate-700">
                        <button type="button" @click="resetFilter()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition">
                            Reset
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dynamic Table Container -->
            <div id="available-stock-table-container">
                @include('inventory.available_stock._table')
            </div>
        </div>
    </div>
</x-app-layout>
