<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bulk Stock Inward
            </h2>

            @can('stock-inwards.create')
            <a href="{{ route('stock-inwards.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 transition">
                + Add Bulk Inward
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6" x-data="stockInwardIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Filter Bar (Zero Reload AJAX POST) -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4">
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('stock-inwards.filter') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf

                    <!-- Search -->
                    <div>
                        <input
                            type="text"
                            name="search"
                            x-model="search"
                            @input.debounce.400ms="applyFilter()"
                            placeholder="Search Ref No, Invoice No or Supplier..."
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- Company Filter -->
                    <div>
                        <select name="company_id" x-model="companyId" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Branch Filter -->
                    <div>
                        <select name="branch_id" x-model="branchId" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Supplier Filter -->
                    <div>
                        <select name="supplier_id" x-model="supplierId" @change="applyFilter()" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="md:col-span-4 flex justify-end gap-2">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <button
                            type="button"
                            @click="resetFilter()"
                            x-show="search || companyId || branchId || supplierId"
                            class="px-4 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dynamic Table Container -->
            <div id="stock-inward-table-container"
                 x-ref="tableContainer"
                 :class="{ 'opacity-50 pointer-events-none': loading }"
                 class="transition-opacity duration-200">
                @include('inventory.stock_inwards._table', ['stockInwards' => $stockInwards])
            </div>

            <!-- Quick View Modal Popup -->
            <div x-show="showModal"
                 x-cloak
                 x-transition
                 @keydown.escape.window="showModal = false"
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div @click.outside="showModal = false"
                     class="relative w-full max-w-5xl bg-white border border-slate-200 dark:bg-[#111827] dark:border-[#1f293d] rounded-2xl shadow-2xl p-6 sm:p-8 overflow-hidden my-8 max-h-[90vh] overflow-y-auto">
                    <button @click="showModal = false"
                            class="absolute top-4 right-4 text-slate-400 hover:text-white p-2 text-xl font-bold transition z-10">
                        &times;
                    </button>
                    <div x-html="modalHtml"></div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function stockInwardIndex() {
            return {
                search: '{{ request('search') }}',
                companyId: '{{ request('company_id') }}',
                branchId: '{{ request('branch_id') }}',
                supplierId: '{{ request('supplier_id') }}',
                filterUrl: '{{ route('stock-inwards.filter') }}',
                csrfToken: '{{ csrf_token() }}',
                loading: false,

                showModal: false,
                modalHtml: '',
                modalLoading: false,

                init() {
                    document.addEventListener('click', (e) => {
                        const paginationLink = e.target.closest('#stock-inward-table-container .pagination-wrapper a, #stock-inward-table-container nav a');
                        if (paginationLink && paginationLink.href) {
                            e.preventDefault();
                            this.fetchData(paginationLink.href);
                        }
                    });
                },

                applyFilter() {
                    this.fetchData(this.filterUrl);
                },

                resetFilter() {
                    this.search = '';
                    this.companyId = '';
                    this.branchId = '';
                    this.supplierId = '';
                    this.applyFilter();
                },

                fetchData(url) {
                    this.loading = true;
                    const formData = new FormData();
                    formData.append('_token', this.csrfToken);
                    if (this.search) formData.append('search', this.search);
                    if (this.companyId) formData.append('company_id', this.companyId);
                    if (this.branchId) formData.append('branch_id', this.branchId);
                    if (this.supplierId) formData.append('supplier_id', this.supplierId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.html) {
                            this.$refs.tableContainer.innerHTML = data.html;
                        }
                    })
                    .catch(err => console.error('Filter error:', err))
                    .finally(() => {
                        this.loading = false;
                    });
                },

                openInwardModal(id) {
                    this.modalLoading = true;
                    this.showModal = true;
                    this.modalHtml = '<div class="flex items-center justify-center p-12 text-slate-300 font-semibold gap-3"><svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading Stock Inward Details...</div>';

                    fetch(`/inventory/stock-inwards/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.html) {
                            this.modalHtml = data.html;
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching stock inward details:', err);
                        this.modalHtml = '<div class="text-red-400 p-6 text-center">Failed to load invoice details.</div>';
                    })
                    .finally(() => {
                        this.modalLoading = false;
                    });
                }
            };
        }
    </script>
</x-app-layout>
