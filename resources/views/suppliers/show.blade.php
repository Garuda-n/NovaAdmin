<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                                    {{ $supplier->supplier_name }}
                                </h1>
                                @if($supplier->status)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300 border border-green-300 dark:border-green-500/30">
                                        Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300 border border-red-300 dark:border-red-500/30">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                                Supplier ID: <span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold">#{{ $supplier->id }}</span>
                                | Type: <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $supplier->supplier_type }}</span>
                                @if($supplier->supplier_code)
                                    | Code: <span class="font-mono text-slate-700 dark:text-slate-300 font-semibold">{{ $supplier->supplier_code }}</span>
                                @endif
                                | Created via: <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $supplier->created_through }}</span>
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            @can('suppliers.edit')
                            <a href="{{ route('suppliers.edit', $supplier) }}"
                               class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition">
                                Edit Supplier
                            </a>
                            @endcan
                            <a href="{{ route('suppliers.index') }}"
                               class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 text-sm font-medium transition">
                                ← Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Supplier Detail Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Left Panel: Contact & Identity -->
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-6 border border-slate-200 dark:border-slate-700/60 space-y-4">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3">
                                Contact & Tax Details
                            </h2>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Supplier Type</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $supplier->supplier_type }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Contact Person</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $supplier->contact_person ?: '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Mobile Number</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $supplier->mobile }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Alternate Mobile</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->alternate_mobile ?: '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Email Address</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->email ?: '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">GST Number</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-mono font-semibold">{{ $supplier->gst_number ?: 'N/A' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">PAN Number</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-mono font-semibold">{{ $supplier->pan_number ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Address & Location -->
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-6 border border-slate-200 dark:border-slate-700/60 space-y-4">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3">
                                Location & Branch
                            </h2>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Country</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->country->name ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">State</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->state->name ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">City</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->city->name ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Pincode</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $supplier->pincode }}</span>
                                </div>

                                <div class="col-span-2">
                                    <span class="text-slate-500 dark:text-slate-400 block">Branch</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $supplier->branch ? $supplier->branch->name : 'Global (All Branches)' }}</span>
                                </div>

                                <div class="col-span-2">
                                    <span class="text-slate-500 dark:text-slate-400 block">Full Address</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->address ?: '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Panel: Financial Terms & Audit -->
                        <div class="md:col-span-2 bg-slate-50 dark:bg-slate-800/60 rounded-xl p-6 border border-slate-200 dark:border-slate-700/60 space-y-4">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3">
                                Financial Terms & Audit Details
                            </h2>

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Opening Balance</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold text-base">₹{{ number_format($supplier->opening_balance, 2) }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Credit Limit</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold text-base">₹{{ number_format($supplier->credit_limit, 2) }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Credit Days</span>
                                    <span class="text-slate-800 dark:text-slate-200 font-bold text-base">{{ $supplier->credit_days }} Days</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Created By</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->creator->name ?? 'System' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block">Last Updated By</span>
                                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->updater->name ?? 'System' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>
