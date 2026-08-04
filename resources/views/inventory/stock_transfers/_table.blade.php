<div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Transfer No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Source</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Destination</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($transfers as $transfer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                            <a href="{{ route('stock-transfers.show', $transfer->id) }}" class="hover:underline">
                                {{ $transfer->transfer_no }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $transfer->transfer_date ? $transfer->transfer_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $transfer->transfer_type === \App\Enums\StockTransferType::COUNTER ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                {{ $transfer->transfer_type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $transfer->sourceBranch->name ?? '-' }}</div>
                            @if($transfer->sourceCounter)
                                <div class="text-xs text-gray-500 dark:text-gray-400">Counter: {{ $transfer->sourceCounter->counter_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $transfer->destinationBranch->name ?? '-' }}</div>
                            @if($transfer->destinationCounter)
                                <div class="text-xs text-gray-500 dark:text-gray-400">Counter: {{ $transfer->destinationCounter->counter_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-gray-200">
                                {{ $transfer->details_count }} {{ Str::plural('item', $transfer->details_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transfer->status->badgeClass() }}">
                                {{ $transfer->status->uiLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('stock-transfers.show', $transfer->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 font-semibold inline-flex items-center gap-1">
                                👁️ View
                            </a>

                            @if($transfer->isDraft())
                                @can('stock-transfer.dispatch')
                                    <form action="{{ route('stock-transfers.dispatch', $transfer->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to DISPATCH this stock transfer? Stock will be deducted from source immediately.')">
                                        @csrf
                                        <button type="submit" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 font-semibold inline-flex items-center gap-1">
                                            🚀 Dispatch
                                        </button>
                                    </form>
                                @endcan
                            @endif

                            @if($transfer->isDispatched())
                                @can('stock-transfer.receive')
                                    <a href="{{ route('stock-transfers.receive-form', $transfer->id) }}" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 font-semibold inline-flex items-center gap-1">
                                        📥 Receive
                                    </a>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <p class="text-base font-medium">No stock transfers found.</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting filter criteria or create a new transfer.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transfers->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700">
            {{ $transfers->links() }}
        </div>
    @endif
</div>
