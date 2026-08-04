<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Receive Stock Transfer #{{ $stockTransfer->transfer_no }}
            </h2>

            <a href="{{ route('stock-transfers.show', $stockTransfer->id) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-slate-700 border border-transparent rounded-lg font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-slate-600 transition text-sm">
                ← Back to Transfer Details
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Location Summary -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6 border-l-4 border-emerald-500">
                <div>
                    <div class="text-xs uppercase font-bold text-gray-400">SOURCE LOCATION</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $stockTransfer->sourceBranch->name ?? '-' }}</div>
                    @if($stockTransfer->sourceCounter)
                        <div class="text-xs text-gray-500">Counter: {{ $stockTransfer->sourceCounter->counter_name }}</div>
                    @endif
                </div>

                <div>
                    <div class="text-xs uppercase font-bold text-gray-400">DESTINATION RECEIVING LOCATION</div>
                    <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $stockTransfer->destinationBranch->name ?? '-' }}</div>
                    @if($stockTransfer->destinationCounter)
                        <div class="text-xs text-emerald-500">Counter: {{ $stockTransfer->destinationCounter->counter_name }}</div>
                    @endif
                </div>
            </div>

            <!-- Receive Confirmation Form -->
            <form action="{{ route('stock-transfers.receive', $stockTransfer->id) }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-3 border-gray-200 dark:border-slate-700">
                        Confirm Received Quantities
                    </h3>

                    <div class="overflow-x-auto border border-gray-200 dark:border-slate-700 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tracking</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Serial Code</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Transferred Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-40">Received Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-36">Damaged Qty</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                @foreach($stockTransfer->details as $index => $detail)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $detail->product->name ?? '' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $detail->tracking_type == 2 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                                {{ $detail->tracking_type == 2 ? 'Serial' : 'Bulk' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-mono text-indigo-600 dark:text-indigo-400 font-semibold">
                                            {{ $detail->item_code ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                                            {{ number_format($detail->transferred_qty, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($detail->tracking_type == 2)
                                                <input type="hidden" name="receive_data[{{ $detail->id }}][received_qty]" value="1.00">
                                                <span class="font-bold text-emerald-600">1.00</span>
                                            @else
                                                <input type="number" step="0.01" min="0" max="{{ $detail->transferred_qty }}"
                                                       name="receive_data[{{ $detail->id }}][received_qty]"
                                                       value="{{ old('receive_data.'.$detail->id.'.received_qty', $detail->transferred_qty) }}"
                                                       required
                                                       class="w-32 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm p-1.5 focus:ring-emerald-500 focus:border-emerald-500">
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <input type="number" step="0.01" min="0" max="{{ $detail->transferred_qty }}"
                                                   name="receive_data[{{ $detail->id }}][damaged_qty]"
                                                   value="{{ old('receive_data.'.$detail->id.'.damaged_qty', 0.00) }}"
                                                   class="w-28 rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm p-1.5 focus:ring-rose-500 focus:border-rose-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('stock-transfers.show', $stockTransfer->id) }}" class="px-5 py-2.5 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 shadow-sm">
                        Confirm & Add Stock to Destination
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
