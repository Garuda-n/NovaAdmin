<x-app-layout>

    <div class="py-6 bg-[#0f1422] min-h-screen"
         x-data="ajaxCustomerFilter({
             filterUrl: '{{ route('customers.filter') }}',
             csrfToken: '{{ csrf_token() }}'
         })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-toast />

            <!-- Header Section -->
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">
                        Customer Master
                    </h1>
                    <p class="text-slate-400 text-sm mt-1">
                        Manage B2C and B2B customer accounts.
                    </p>
                </div>

                @can('customers.create')
                <a href="{{ route('customers.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700">
                    <span class="text-lg leading-none">+</span>
                    <span>Add Customer</span>
                </a>
                @endcan
            </div>

            <!-- Filter Card Container (Zero Reload AJAX POST) -->
            <div class="bg-[#1c2538] border border-[#27334d] rounded-xl p-4 mb-6">
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('customers.filter') }}" class="flex flex-row items-center gap-3 w-full flex-nowrap">
                    @csrf
                    
                    <!-- Search Input -->
                    <div class="flex-1 min-w-0">
                        <input
                            type="text"
                            name="search"
                            x-model="search"
                            @input.debounce.400ms="applyFilter()"
                            placeholder="Search name, code, mobile..."
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- Customer Type Select -->
                    <div class="w-48 shrink-0">
                        <select
                            name="customer_type"
                            x-model="customerType"
                            @change="applyFilter()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Types</option>
                            <option value="B2C">B2C</option>
                            <option value="B2B">B2B</option>
                        </select>
                    </div>

                    <!-- Branch Select -->
                    <div class="w-48 shrink-0">
                        <select
                            name="branch_id"
                            x-model="branchId"
                            @change="applyFilter()"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 whitespace-nowrap">
                        <svg x-show="loading" class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Filter</span>
                    </button>

                </form>
            </div>

            <!-- Dynamic Table Container -->
            <div id="customer-table-container"
                 x-ref="tableContainer"
                 :class="{ 'opacity-50 pointer-events-none': loading }"
                 class="transition-opacity duration-200">
                @include('customers._table', ['customers' => $customers])
            </div>

            <!-- Customer Detail Quick View Modal Popup -->
            <div x-show="showModal"
                 x-cloak
                 x-transition
                 @keydown.escape.window="showModal = false"
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
                <div @click.outside="showModal = false"
                     class="relative w-full max-w-4xl bg-[#111827] border border-[#1f293d] rounded-2xl shadow-2xl p-6 sm:p-8 overflow-hidden my-8">
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
    function ajaxCustomerFilter(config) {
        return {
            search: '{{ request('search') }}',
            customerType: '{{ request('customer_type') }}',
            branchId: '{{ request('branch_id') }}',
            filterUrl: config.filterUrl,
            csrfToken: config.csrfToken,
            loading: false,

            showModal: false,
            modalHtml: '',
            modalLoading: false,

            init() {
                // Intercept pagination clicks inside table container
                document.addEventListener('click', (e) => {
                    const paginationLink = e.target.closest('#customer-table-container .pagination-wrapper a, #customer-table-container nav a');
                    if (paginationLink && paginationLink.href) {
                        e.preventDefault();
                        this.fetchData(paginationLink.href);
                    }
                });
            },

            openCustomerModal(id) {
                this.modalLoading = true;
                this.showModal = true;
                this.modalHtml = '<div class="flex items-center justify-center p-12 text-slate-300 font-semibold gap-3"><svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading Customer Details...</div>';

                fetch(`/customers/${id}`, {
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
                    console.error('Error fetching customer details:', err);
                    this.modalHtml = '<div class="text-red-400 p-6 text-center">Failed to load customer details.</div>';
                })
                .finally(() => {
                    this.modalLoading = false;
                });
            },

            applyFilter() {
                this.fetchData(this.filterUrl);
            },

            fetchData(targetUrl) {
                this.loading = true;
                const formData = new FormData();
                formData.append('_token', this.csrfToken);
                if (this.search) formData.append('search', this.search);
                if (this.customerType) formData.append('customer_type', this.customerType);
                if (this.branchId) formData.append('branch_id', this.branchId);

                fetch(targetUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.html && this.$refs.tableContainer) {
                        this.$refs.tableContainer.innerHTML = data.html;
                    }
                })
                .catch(err => console.error('Error filtering customers:', err))
                .finally(() => {
                    this.loading = false;
                });
            }
        }
    }
    </script>

</x-app-layout>
