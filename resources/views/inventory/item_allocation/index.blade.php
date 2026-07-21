<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                <x-heroicon-o-cube-transparent class="w-6 h-6 text-indigo-500" />
                Individual Item Allocation List
            </h2>
        </div>
    </x-slot>

    <script src="{{ asset('js/inventory/allocation_filter.js') }}"></script>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="itemAllocationFilter({ search: '{{ request('search') }}', branch_id: '{{ request('branch_id') }}', supplier_id: '{{ request('supplier_id') }}', category_id: '{{ request('category_id') }}', filterUrl: '{{ route('item-allocation.filter') }}', csrfToken: '{{ csrf_token() }}' })">

            <x-toast />

            <!-- Filter Panel -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-5 border border-gray-200 dark:border-slate-700">
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('item-allocation.filter') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <!-- Search Input -->
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Search</label>
                        <input type="text" id="search" name="search" x-model="search" placeholder="Invoice No, Product, Supplier..."
                               class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

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

                    <!-- Supplier Filter -->
                    <div>
                        <label for="supplier_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Supplier</label>
                        <select id="supplier_id" name="supplier_id" x-model="supplier_id" @change="applyFilter()"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->supplier_name }}</option>
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

                    <!-- Action Buttons -->
                    <div class="md:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-gray-200 dark:border-slate-700">
                        <button type="button" @click="resetFilter()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition">
                            Reset Filters
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" /> Filter Results
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dynamic Table Container -->
            <div id="allocation-table-container">
                @include('inventory.item_allocation._table')
            </div>

    <!-- Include Allocation Modal Partial -->
    @include('inventory.stock_inwards._allocation_modal')
</x-app-layout>
