<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Stock Transfer #{{ $stockTransfer->transfer_no }}
                </h2>
                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $stockTransfer->status->badgeClass() }}">
                    {{ $stockTransfer->status->uiLabel() }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('stock-transfers.print', $stockTransfer->id) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-white hover:bg-gray-700 transition shadow-sm text-sm">
                    🖨️ Print Manifest
                </a>

                @if($stockTransfer->isDraft())
                    @can('stock-transfer.dispatch')
                        <form action="{{ route('stock-transfers.dispatch', $stockTransfer->id) }}" method="POST" class="inline" onsubmit="return confirm('Confirm DISPATCH of this stock transfer? Source stock will be deducted immediately.')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-white hover:bg-emerald-700 transition shadow-sm text-sm">
                                🚀 Dispatch Stock
                            </button>
                        </form>
                    @endcan
                @endif

                @if($stockTransfer->isDispatched())
                    @can('stock-transfer.receive')
                        <a href="{{ route('stock-transfers.receive-form', $stockTransfer->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-white hover:bg-emerald-700 transition shadow-sm text-sm">
                            📥 Confirm Receive
                        </a>
                    @endcan
                @endif

                @if($stockTransfer->isDraft())
                    @can('stock-transfer.cancel')
                        <button type="button" onclick="openCancelModal()"
                                class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-white hover:bg-rose-700 transition shadow-sm text-sm">
                            🚫 Cancel Transfer
                        </button>
                    @endcan
                @endif

                <a href="{{ route('stock-transfers.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-slate-700 border border-transparent rounded-lg font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-slate-600 transition text-sm">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <!-- Location Details & Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Source Location -->
                <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border-t-4 border-indigo-500">
                    <h3 class="text-xs uppercase font-bold text-gray-400 dark:text-gray-400 tracking-wider mb-2">SOURCE LOCATION</h3>
                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $stockTransfer->sourceBranch->name ?? '-' }}</div>
                    @if($stockTransfer->sourceCounter)
                        <div class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold mt-1">Counter: {{ $stockTransfer->sourceCounter->counter_name }}</div>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">Company: {{ $stockTransfer->company->name ?? '-' }}</div>
                </div>

                <!-- Destination Location -->
                <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border-t-4 border-emerald-500">
                    <h3 class="text-xs uppercase font-bold text-gray-400 dark:text-gray-400 tracking-wider mb-2">DESTINATION LOCATION</h3>
                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $stockTransfer->destinationBranch->name ?? '-' }}</div>
                    @if($stockTransfer->destinationCounter)
                        <div class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold mt-1">Counter: {{ $stockTransfer->destinationCounter->counter_name }}</div>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">Type: {{ $stockTransfer->transfer_type->label() }}</div>
                </div>
            </div>

            <!-- Transfer Items Table -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 space-y-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-3 border-gray-200 dark:border-slate-700">
                    Transferred Line Items
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tracking Mode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Serial / Item Code</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Transferred Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Received Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Damaged Qty</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach($stockTransfer->details as $index => $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $detail->product->name ?? 'Product #' . $detail->product_id }}
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $detail->product->code ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $detail->tracking_type == 2 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                            {{ $detail->tracking_type == 2 ? 'Individual (Serial)' : 'Quantity (Bulk)' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $detail->item_code ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($detail->transferred_qty, 2) }} {{ $detail->product->uom->name ?? '' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $detail->received_qty !== null ? number_format($detail->received_qty, 2) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-rose-600 dark:text-rose-400">
                                        {{ number_format($detail->damaged_qty, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($stockTransfer->remarks)
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                        <strong class="text-gray-900 dark:text-gray-100">Remarks:</strong> {{ $stockTransfer->remarks }}
                    </div>
                @endif
            </div>

            <!-- Complete Audit Log Section -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 space-y-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-3 border-gray-200 dark:border-slate-700">
                    Audit Trail & Lifecycle History
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <!-- Created -->
                    <div class="p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">CREATED BY</div>
                        <div class="font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stockTransfer->creator->name ?? 'User #' . $stockTransfer->created_by }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stockTransfer->created_at ? $stockTransfer->created_at->format('d M Y, h:i A') : '-' }}</div>
                    </div>

                    <!-- Dispatched -->
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-xs font-semibold text-blue-600 dark:text-blue-400">DISPATCHED BY</div>
                        <div class="font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stockTransfer->dispatcher->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stockTransfer->dispatched_at ? $stockTransfer->dispatched_at->format('d M Y, h:i A') : 'Pending' }}</div>
                    </div>

                    <!-- Received -->
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                        <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">RECEIVED BY</div>
                        <div class="font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stockTransfer->receiver->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stockTransfer->received_at ? $stockTransfer->received_at->format('d M Y, h:i A') : 'Pending' }}</div>
                    </div>

                    <!-- Cancelled -->
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
                        <div class="text-xs font-semibold text-rose-600 dark:text-rose-400">CANCELLED BY</div>
                        <div class="font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stockTransfer->canceller->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stockTransfer->cancelled_at ? $stockTransfer->cancelled_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    </div>
                </div>

                @if($stockTransfer->cancellation_reason)
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/30 text-rose-800 dark:text-rose-300 rounded-lg text-sm">
                        <strong>Cancellation Reason:</strong> {{ $stockTransfer->cancellation_reason }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Cancellation Reason Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Cancel Stock Transfer #{{ $stockTransfer->transfer_no }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Please enter the reason for cancelling this stock transfer.</p>

            <form action="{{ route('stock-transfers.cancel', $stockTransfer->id) }}" method="POST">
                @csrf
                <textarea name="cancellation_reason" required rows="3" placeholder="Reason for cancellation..." class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm mb-4"></textarea>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold">
                        Dismiss
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }
        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
