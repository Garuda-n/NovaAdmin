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

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4">
                <form method="GET" action="{{ route('stock-inwards.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search Invoice No or Supplier..."
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- Company Filter -->
                    <div>
                        <select name="company_id" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Companies</option>
                            @foreach($companies as $cat)
                                <option value="{{ $cat->id }}" {{ request('company_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Branch Filter -->
                    <div>
                        <select name="branch_id" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter & Clear Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'company_id', 'branch_id']))
                            <a href="{{ route('stock-inwards.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Data Table Card -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-100 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Invoice No</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Company & Branch</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Supplier</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Items</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @forelse($stockInwards as $inward)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                    
                                    <!-- Invoice No -->
                                    <td class="px-6 py-4 font-mono font-semibold text-indigo-600 dark:text-indigo-400 text-sm">
                                        <a href="{{ route('stock-inwards.show', $inward) }}"
                                           @click.prevent="openInwardModal({{ $inward->id }})"
                                           class="hover:underline cursor-pointer">
                                            {{ $inward->invoice_no }}
                                        </a>
                                    </td>

                                    <!-- Date -->
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                        {{ $inward->invoice_date ? $inward->invoice_date->format('d M Y') : '—' }}
                                    </td>

                                    <!-- Company & Branch -->
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                        <div class="font-medium">{{ $inward->company->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inward->branch->name ?? '—' }}{{ $inward->counter ? ' (' . $inward->counter->counter_name . ')' : '' }}</div>
                                    </td>

                                    <!-- Supplier -->
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                        {{ $inward->supplier->supplier_name ?? '—' }}
                                    </td>

                                    <!-- Items Count -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                                            {{ $inward->items_count }} {{ Str::plural('item', $inward->items_count) }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- View Popup Button -->
                                            <button type="button"
                                                @click.prevent="openInwardModal({{ $inward->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition cursor-pointer"
                                                title="Quick View Invoice">
                                                <x-heroicon-o-eye class="w-4 h-4" />
                                            </button>

                                            @can('stock-inwards.edit')
                                            <a href="{{ route('stock-inwards.edit', $inward) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                                title="Edit Invoice">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            @endcan

                                            @can('stock-inwards.delete')
                                            <form action="{{ route('stock-inwards.destroy', $inward) }}" method="POST"
                                                  onsubmit="return confirm('Delete this bulk stock inward invoice?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition"
                                                    title="Delete Invoice">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No bulk stock inward records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($stockInwards->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-slate-700">
                        {{ $stockInwards->links() }}
                    </div>
                @endif
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
                showModal: false,
                modalHtml: '',
                modalLoading: false,

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
