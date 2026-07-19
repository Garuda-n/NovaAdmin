<!-- Data Table -->
<div class="rounded-xl overflow-hidden border border-slate-200 dark:border-[#27334d] bg-white dark:bg-[#1c2538] shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-slate-50 dark:bg-[#25314a] border-b border-slate-200 dark:border-[#2b3752]">
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        SUPPLIER NAME
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        TYPE
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        MOBILE / EMAIL
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        GST / PAN
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        LOCATION / BRANCH
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        STATUS
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center">
                        ACTION
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 dark:divide-[#242f48]">

                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#212b40] transition duration-150">

                        <!-- ID -->
                        <td class="py-4 px-6 text-slate-800 dark:text-slate-200 text-sm font-semibold">
                            {{ $supplier->id }}
                        </td>

                        <!-- SUPPLIER NAME -->
                        <td class="py-4 px-6 text-slate-900 dark:text-white font-bold text-sm">
                            <a href="{{ route('suppliers.show', $supplier) }}"
                               @click.prevent="openSupplierModal({{ $supplier->id }})"
                               class="hover:text-indigo-600 dark:hover:text-[#5851ea] transition cursor-pointer">
                                {{ $supplier->supplier_name }}
                            </a>
                            @if($supplier->supplier_code)
                                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $supplier->supplier_code }}</div>
                            @endif
                        </td>

                        <!-- TYPE -->
                        <td class="py-4 px-6 text-slate-800 dark:text-slate-200 font-medium text-sm">
                            {{ $supplier->supplier_type }}
                        </td>

                        <!-- MOBILE / EMAIL -->
                        <td class="py-4 px-6 text-slate-700 dark:text-slate-300 text-sm">
                            <div class="font-medium text-slate-900 dark:text-slate-200">{{ $supplier->mobile }}</div>
                            @if($supplier->email)
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $supplier->email }}</div>
                            @endif
                        </td>

                        <!-- GST / PAN -->
                        <td class="py-4 px-6 text-slate-700 dark:text-slate-300 text-sm font-mono">
                            <div>{{ $supplier->gst_number ?: '-' }}</div>
                            @if($supplier->pan_number)
                                <div class="text-xs text-slate-500 dark:text-slate-400">PAN: {{ $supplier->pan_number }}</div>
                            @endif
                        </td>

                        <!-- LOCATION / BRANCH -->
                        <td class="py-4 px-6 text-slate-700 dark:text-slate-300 text-sm">
                            <div>{{ $supplier->city->name ?? '-' }}, {{ $supplier->state->name ?? '-' }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Branch: {{ $supplier->branch ? $supplier->branch->name : 'Global' }}
                            </div>
                        </td>

                        <!-- STATUS -->
                        <td class="py-4 px-6">
                            @if($supplier->status)
                                <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-[#103a2e] dark:text-[#34d399]">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-400 border border-red-200 dark:border-red-800/50">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <!-- ACTION -->
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('suppliers.show', $supplier) }}"
                                   @click.prevent="openSupplierModal({{ $supplier->id }})"
                                   title="View Supplier Details"
                                   class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-[#28344d] dark:hover:bg-[#32415f] dark:text-white transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                @can('suppliers.edit')
                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                   title="Edit Supplier"
                                   class="p-2 rounded-lg bg-[#f59e0b] hover:bg-[#d97706] text-white transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('suppliers.delete')
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Supplier"
                                            class="p-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 px-6 text-center text-slate-400 font-medium">
                            No suppliers found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>
</div>

<!-- Pagination -->
@if($suppliers->hasPages())
<div class="mt-6 pagination-wrapper">
    {{ $suppliers->links() }}
</div>
@endif
