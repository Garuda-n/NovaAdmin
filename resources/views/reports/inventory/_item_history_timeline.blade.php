@props([
    'historyData' => [],
    'hasSearched' => false,
])

@php
    $found = $historyData['found'] ?? false;
    $summary = $historyData['summary'] ?? null;
    $timeline = $historyData['timeline'] ?? collect();
    $searchedCode = $historyData['item_code'] ?? '';
@endphp

<div class="space-y-6">

    @if(!$hasSearched)
        {{-- Initial Empty State Before Search --}}
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 p-12 text-center">
            <div class="flex flex-col items-center justify-center gap-3">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/50 rounded-full text-indigo-500">
                    <x-heroicon-o-magnifying-glass class="w-10 h-10" />
                </div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Search Allocated Item History</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">
                    Enter an Item Code in the search field above and click <span class="font-semibold text-indigo-600 dark:text-indigo-400">Search</span> to view its complete lifecycle timeline.
                </p>
            </div>
        </div>

    @elseif(!$found)
        {{-- Item Not Found Empty State --}}
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 p-12 text-center">
            <div class="flex flex-col items-center justify-center gap-3">
                <div class="p-3 bg-rose-50 dark:bg-rose-950/50 rounded-full text-rose-500">
                    <x-heroicon-o-archive-box-x-mark class="w-10 h-10" />
                </div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">No History Found</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    No history found for Item Code <span class="font-mono font-bold text-rose-600 dark:text-rose-400">"{{ $searchedCode }}"</span>.
                </p>
            </div>
        </div>

    @else
        {{-- Result Header Summary Card --}}
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-700 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/50 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <x-heroicon-o-qr-code class="w-7 h-7" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Code</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">
                                {{ $summary['item_code'] }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">
                            {{ $summary['product_name'] }}
                            @if(!empty($summary['product_code']))
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400">({{ $summary['product_code'] }})</span>
                            @endif
                        </h3>
                    </div>
                </div>
                <div>
                    @php
                        $statusClass = match((int)$summary['status_code']) {
                            1 => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300',
                            2 => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-300',
                            3 => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-300',
                            5 => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-300',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-300',
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $statusClass }}">
                        ● {{ $summary['status'] }}
                    </span>
                </div>
            </div>

            {{-- Metric Cards Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700/60">
                    <span class="block text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400">Current Branch</span>
                    <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mt-1">{{ $summary['current_branch'] }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700/60">
                    <span class="block text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400">Current Counter</span>
                    <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mt-1">{{ $summary['current_counter'] }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700/60">
                    <span class="block text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400">Current Owner (If Sold)</span>
                    <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mt-1">{{ $summary['current_owner'] ?? 'N/A' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700/60">
                    <span class="block text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400">Created Date</span>
                    <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100 mt-1">
                        {{ $summary['created_date'] ? \Carbon\Carbon::parse($summary['created_date'])->format('d M Y, h:i A') : 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Vertical Timeline Section --}}
        <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 p-6">
            <h4 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-6 flex items-center gap-2 border-b border-gray-200 dark:border-slate-700 pb-3">
                <x-heroicon-o-clock class="w-5 h-5 text-indigo-500" />
                Lifecycle History Timeline
            </h4>

            @if($timeline->isEmpty())
                <p class="text-xs text-gray-500 dark:text-gray-400 py-4 text-center">No lifecycle events recorded for this item.</p>
            @else
                <div class="relative pl-6 space-y-8 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700">
                    @foreach($timeline as $event)
                        @php
                            $badgeColorClass = match($event['color'] ?? 'indigo') {
                                'purple' => 'bg-purple-600 text-white ring-purple-100 dark:ring-purple-950',
                                'emerald' => 'bg-emerald-600 text-white ring-emerald-100 dark:ring-emerald-950',
                                'amber' => 'bg-amber-500 text-white ring-amber-100 dark:ring-amber-950',
                                'blue' => 'bg-blue-600 text-white ring-blue-100 dark:ring-blue-950',
                                'rose' => 'bg-rose-600 text-white ring-rose-100 dark:ring-rose-950',
                                default => 'bg-indigo-600 text-white ring-indigo-100 dark:ring-indigo-950',
                            };
                        @endphp

                        <div class="relative group">
                            {{-- Timeline Icon Node --}}
                            <div class="absolute -left-9 top-0.5 w-6 h-6 rounded-full flex items-center justify-center ring-4 {{ $badgeColorClass }} shadow-sm">
                                <x-dynamic-component :component="'heroicon-o-' . ($event['icon'] ?? 'cube')" class="w-3.5 h-3.5" />
                            </div>

                            {{-- Timeline Card Body --}}
                            <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-4 border border-slate-200 dark:border-slate-700/80 hover:border-indigo-400 dark:hover:border-indigo-500 transition shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-slate-200 dark:border-slate-800 pb-2.5 mb-3">
                                    <div class="flex items-center gap-2">
                                        <h5 class="text-sm font-bold text-gray-900 dark:text-white">{{ $event['title'] }}</h5>
                                        @if(!empty($event['reference_no']))
                                            <span class="px-2 py-0.5 text-[11px] font-mono font-semibold rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                {{ $event['reference_no'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-1 font-medium">
                                            <x-heroicon-o-calendar class="w-3.5 h-3.5 text-gray-400" />
                                            Business Date: <strong class="text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($event['business_date'])->format('d M Y') }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3">{{ $event['description'] }}</p>

                                {{-- Event Metadata Key-Values --}}
                                @if(!empty($event['metadata']) && count($event['metadata']) > 0)
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                                        @foreach($event['metadata'] as $key => $val)
                                            <div>
                                                <span class="block text-[10px] uppercase font-semibold text-gray-400 dark:text-gray-500">{{ $key }}</span>
                                                <span class="block text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $val ?? 'N/A' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
