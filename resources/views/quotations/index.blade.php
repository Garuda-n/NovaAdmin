<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen"
         x-data="{
             search: '{{ request('search') }}',
             status: '{{ request('status') }}',
             customerType: '{{ request('customer_type') }}',
             loading: false,
             applyFilter() {
                 this.loading = true;
                 const params = new URLSearchParams({
                     search: this.search,
                     status: this.status,
                     customer_type: this.customerType
                 });
                 fetch('{{ route('quotations.filter') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/x-www-form-urlencoded',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'X-Requested-With': 'XMLHttpRequest'
                     },
                     body: params.toString()
                 })
                 .then(res => res.json())
                 .then(data => {
                     document.getElementById('quotation-table-container').innerHTML = data.html;
                 })
                 .catch(err => console.error(err))
                 .finally(() => { this.loading = false; });
             }
         }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Top Header Section -->
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Quotation
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                        Manage sales quotations, customer proposals, and pricing breakdowns.
                    </p>
                </div>

                @can('quotation.create')
                <a href="{{ route('quotations.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 transition">
                    + New Quotation
                </a>
                @endcan
            </div>

            <!-- Filter Card Container -->
            <div class="bg-white border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-4 shadow-sm">
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('quotations.filter') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    @csrf
                    
                    <!-- Search Input -->
                    <div>
                        <input
                            type="text"
                            name="search"
                            x-model="search"
                            @input.debounce.400ms="applyFilter()"
                            placeholder="Search Quotation No, Customer..."
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select
                            name="status"
                            x-model="status"
                            @change="applyFilter()"
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="1">Created</option>
                            <option value="2">Converted</option>
                        </select>
                    </div>

                    <!-- Customer Type Filter -->
                    <div>
                        <select
                            name="customer_type"
                            x-model="customerType"
                            @change="applyFilter()"
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Customer Types</option>
                            <option value="B2C">B2C</option>
                            <option value="B2B">B2B</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Quotation Table Container Card -->
            <div id="quotation-table-container" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl overflow-hidden">
                @include('quotations._table', ['quotations' => $quotations])
            </div>

        </div>
    </div>
</x-app-layout>
