<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <x-heroicon-o-archive-box class="w-6 h-6 text-indigo-500" />
                    Stock Register
                </h2>
                <nav class="flex text-xs text-gray-500 dark:text-gray-400 gap-1.5 items-center mt-1">
                    <span>Reports</span>
                    <span>/</span>
                    <span>Inventory</span>
                    <span>/</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Stock Register</span>
                </nav>
            </div>
        </div>
    </x-slot>

    <script src="{{ asset('js/report/date_range.js') }}"></script>
    <script src="{{ asset('js/report/filters.js') }}"></script>
    <script src="{{ asset('js/report/export.js') }}"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{
            ...reportExport({ exportBaseUrl: '{{ route('reports.inventory.index') }}' }),
            ...reportFilters({
                preset: '{{ $dateRange['preset'] }}',
                from_date: '{{ $dateRange['from_date']->format('Y-m-d') }}',
                to_date: '{{ $dateRange['to_date']->format('Y-m-d') }}',
                company_id: '{{ request('company_id') }}',
                branch_id: '{{ request('branch_id') }}',
                counter_id: '{{ request('counter_id') }}',
                category_id: '{{ request('category_id') }}',
                product_id: '{{ request('product_id') }}',
                show_zero_stock: '{{ request('show_zero_stock', '1') }}',
                filterUrl: '{{ route('reports.inventory.search') }}',
                csrfToken: '{{ csrf_token() }}',
                containerId: 'report-table-container'
            })
        }">

            {{-- Filter Panel --}}
            <x-report.filters>
                <form @submit.prevent="applyFilter()" class="space-y-4">
                    @csrf

                    {{-- Date Range Component --}}
                    <x-report.date-range
                        :preset="$dateRange['preset']"
                        :from-date="$dateRange['from_date']->format('Y-m-d')"
                        :to-date="$dateRange['to_date']->format('Y-m-d')"
                        :preset-options="$presetOptions"
                    />

                    {{-- Filter Dropdowns Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

                        {{-- Company Filter --}}
                        <div>
                            <label for="company_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Company</label>
                            <select id="company_id" name="company_id" x-model="company_id" @change="applyFilter()"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Branch Filter --}}
                        <div>
                            <label for="branch_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Branch</label>
                            <select id="branch_id" name="branch_id" x-model="branch_id" @change="applyFilter()"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Counter Filter --}}
                        <div>
                            <label for="counter_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Counter</label>
                            <select id="counter_id" name="counter_id" x-model="counter_id" @change="applyFilter()"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Counters</option>
                                @foreach($counters as $counter)
                                    <option value="{{ $counter->id }}">{{ $counter->counter_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label for="category_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Category</label>
                            <select id="category_id" name="category_id" x-model="category_id" @change="applyFilter()"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Product Filter --}}
                        <div>
                            <label for="product_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Product</label>
                            <select id="product_id" name="product_id" x-model="product_id" @change="applyFilter()"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Show Zero Stock Filter --}}
                        <div>
                            <label for="show_zero_stock" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Zero Stock</label>
                            <select id="show_zero_stock" name="show_zero_stock" x-model="show_zero_stock" @change="applyFilter()"
                                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Show Zero Stock</option>
                                <option value="0">Hide Zero Stock</option>
                            </select>
                        </div>

                    </div>

                    {{-- Action Buttons Component --}}
                    <x-report.filter-actions />
                </form>
            </x-report.filters>

            {{-- Report Table Container --}}
            <div id="report-table-container" class="relative">
                <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-800/70 z-10 flex items-center justify-center rounded-lg">
                    <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading report data...
                    </div>
                </div>

                @include('reports.inventory._table', ['reportData' => $reportData])
            </div>

        </div>
    </div>
</x-app-layout>
