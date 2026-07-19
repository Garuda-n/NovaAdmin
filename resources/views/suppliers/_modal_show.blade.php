<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700/60">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ $supplier->supplier_name }}
                </h2>
                @if($supplier->status)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200 dark:bg-green-500/20 dark:text-green-300 dark:border-green-500/30">
                        Active
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200 dark:bg-red-500/20 dark:text-red-300 dark:border-red-500/30">
                        Inactive
                    </span>
                @endif
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">
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
               class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition shadow">
                Edit Supplier
            </a>
            @endcan
            <button @click="showModal = false"
                    class="px-3 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 text-xs font-medium transition">
                Close &times;
            </button>
        </div>
    </div>

    <!-- Supplier Detail Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Left Panel: Contact & Identity -->
        <div class="bg-slate-50 border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700/60 pb-2.5">
                Contact & Tax Details
            </h3>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Supplier Type</span>
                    <span class="text-slate-900 dark:text-slate-200 font-semibold text-sm">{{ $supplier->supplier_type }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Contact Person</span>
                    <span class="text-slate-900 dark:text-slate-200 font-semibold text-sm">{{ $supplier->contact_person ?: '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Mobile Number</span>
                    <span class="text-slate-900 dark:text-slate-200 font-semibold text-sm">{{ $supplier->mobile }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Alternate Mobile</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->alternate_mobile ?: '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Email Address</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->email ?: '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">GST Number</span>
                    <span class="text-slate-800 dark:text-slate-200 font-mono font-semibold">{{ $supplier->gst_number ?: 'N/A' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">PAN Number</span>
                    <span class="text-slate-800 dark:text-slate-200 font-mono font-semibold">{{ $supplier->pan_number ?: 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Right Panel: Address & Location -->
        <div class="bg-slate-50 border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700/60 pb-2.5">
                Location & Branch
            </h3>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Country</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->country->name ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">State</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->state->name ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">City</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->city->name ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Pincode</span>
                    <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $supplier->pincode }}</span>
                </div>

                <div class="col-span-2">
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Branch</span>
                    <span class="text-slate-900 dark:text-slate-200 font-semibold">{{ $supplier->branch ? $supplier->branch->name : 'Global (All Branches)' }}</span>
                </div>

                <div class="col-span-2">
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Full Address</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->address ?: '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Bottom Panel: Financial Terms & Audit -->
        <div class="md:col-span-2 bg-slate-50 border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700/60 pb-2.5">
                Financial Terms & Audit Details
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Opening Balance</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold text-sm">₹{{ number_format($supplier->opening_balance, 2) }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Credit Limit</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold text-sm">₹{{ number_format($supplier->credit_limit, 2) }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Credit Days</span>
                    <span class="text-slate-900 dark:text-slate-200 font-bold text-sm">{{ $supplier->credit_days }} Days</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Created By</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->creator->name ?? 'System' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block mb-1">Last Updated By</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $supplier->updater->name ?? 'System' }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
