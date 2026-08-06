<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen"
         x-data="{
             search: '{{ request('search') }}',
             status: '{{ request('status') }}',
             customerType: '{{ request('customer_type') }}',
             preset: '{{ $dateRange['preset'] }}',
             dateFrom: '{{ $dateRange['from_date']->format('Y-m-d') }}',
             dateTo: '{{ $dateRange['to_date']->format('Y-m-d') }}',
             loading: false,
             applyFilter() {
                 this.loading = true;
                 const params = new URLSearchParams({
                     search: this.search,
                     status: this.status,
                     customer_type: this.customerType,
                     preset: this.preset,
                     date_from: this.dateFrom,
                     date_to: this.dateTo
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
             },
             onPresetChange() {
                 const today = new Date();
                 const formatDate = (d) => {
                     const yyyy = d.getFullYear();
                     const mm   = String(d.getMonth() + 1).padStart(2, '0');
                     const dd   = String(d.getDate()).padStart(2, '0');
                     return yyyy + '-' + mm + '-' + dd;
                 };
                 let from = '', to = '';
                 switch (this.preset) {
                     case 'today':
                         from = to = formatDate(today);
                         break;
                     case 'yesterday': {
                         const d = new Date(today);
                         d.setDate(d.getDate() - 1);
                         from = to = formatDate(d);
                         break;
                     }
                     case 'last_7_days': {
                         const d = new Date(today);
                         d.setDate(d.getDate() - 6);
                         from = formatDate(d);
                         to = formatDate(today);
                         break;
                     }
                     case 'last_30_days': {
                         const d = new Date(today);
                         d.setDate(d.getDate() - 29);
                         from = formatDate(d);
                         to = formatDate(today);
                         break;
                     }
                     case 'this_month':
                         from = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
                         to = formatDate(today);
                         break;
                     case 'last_month': {
                         const first = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                         const last  = new Date(today.getFullYear(), today.getMonth(), 0);
                         from = formatDate(first);
                         to = formatDate(last);
                         break;
                     }
                     case 'this_year':
                         from = formatDate(new Date(today.getFullYear(), 0, 1));
                         to = formatDate(today);
                         break;
                     case 'last_year':
                         from = formatDate(new Date(today.getFullYear() - 1, 0, 1));
                         to = formatDate(new Date(today.getFullYear() - 1, 11, 31));
                         break;
                     case 'custom':
                         return;
                 }
                 this.dateFrom = from;
                 this.dateTo = to;
                 this.applyFilter();
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
                <form @submit.prevent="applyFilter()" method="POST" action="{{ route('quotations.filter') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-center">
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

                    <!-- Date Range Preset Filter -->
                    <div>
                        <select
                            name="preset"
                            x-model="preset"
                            @change="onPresetChange()"
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($presetOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From Filter -->
                    <div>
                        <input
                            type="date"
                            name="date_from"
                            x-model="dateFrom"
                            @change="preset = 'custom'; applyFilter()"
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Date To Filter -->
                    <div>
                        <input
                            type="date"
                            name="date_to"
                            x-model="dateTo"
                            @change="preset = 'custom'; applyFilter()"
                            class="w-full rounded-lg border-gray-300 bg-white text-slate-900 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
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
