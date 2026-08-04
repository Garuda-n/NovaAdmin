<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Stock Transfer Management
            </h2>

            @can('stock-transfer.create')
            <a href="{{ route('stock-transfers.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 transition shadow-sm text-sm">
                ➕ Create Transfer
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6" x-data="stockTransferIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-100 border-l-4 border-rose-500 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4">
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('stock-transfers.filter') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf

                    <!-- Search Input -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Transfer No / Ref</label>
                        <input
                            type="text"
                            name="search"
                            x-model="filters.search"
                            @input.debounce.400ms="applyFilter()"
                            placeholder="e.g. ST000001..."
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- Transfer Type Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Transfer Type</label>
                        <select name="transfer_type" x-model="filters.transfer_type" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Transfer Types</option>
                            @foreach($transferTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" x-model="filters.status" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}">{{ $st->uiLabel() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source Branch Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Source Branch</label>
                        <select name="source_branch_id" x-model="filters.source_branch_id" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Source Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Destination Branch Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Destination Branch</label>
                        <select name="destination_branch_id" x-model="filters.destination_branch_id" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Destination Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- From Date -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">From Date</label>
                        <input type="date" name="from_date" x-model="filters.from_date" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- To Date -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">To Date</label>
                        <input type="date" name="to_date" x-model="filters.to_date" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>🔍 Filter</span>
                        </button>
                        <button
                            type="button"
                            @click="resetFilter()"
                            class="px-4 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                            🔄 Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dynamic Table Container -->
            <div id="stock-transfer-table-container"
                 x-ref="tableContainer"
                 :class="{ 'opacity-50 pointer-events-none': loading }"
                 class="transition-opacity duration-200">
                @include('inventory.stock_transfers._table', ['transfers' => $transfers])
            </div>
        </div>
    </div>

    <script>
        function stockTransferIndex() {
            return {
                loading: false,
                filters: {
                    search: '',
                    transfer_type: '',
                    status: '',
                    source_branch_id: '',
                    destination_branch_id: '',
                    from_date: '',
                    to_date: '',
                },
                applyFilter() {
                    this.loading = true;
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key]) {
                            formData.append(key, this.filters[key]);
                        }
                    });

                    fetch('{{ route("stock-transfers.filter") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.html) {
                            document.getElementById('stock-transfer-table-container').innerHTML = data.html;
                        }
                    })
                    .catch(err => console.error(err))
                    .finally(() => {
                        this.loading = false;
                    });
                },
                resetFilter() {
                    this.filters = {
                        search: '',
                        transfer_type: '',
                        status: '',
                        source_branch_id: '',
                        destination_branch_id: '',
                        from_date: '',
                        to_date: '',
                    };
                    this.applyFilter();
                }
            };
        }
    </script>
</x-app-layout>
