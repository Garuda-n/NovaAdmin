<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <x-heroicon-o-clock class="w-6 h-6 text-indigo-500" />
                    Allocated Item History
                </h2>
                <nav class="flex text-xs text-gray-500 dark:text-gray-400 gap-1.5 items-center mt-1">
                    <span>Reports</span>
                    <span>/</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Allocated Item History</span>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{
            item_code: '{{ $itemCode ?? '' }}',
            loading: false,
            async searchHistory() {
                if (!this.item_code.trim()) return;
                this.loading = true;
                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('item_code', this.item_code);

                    const response = await fetch('{{ route('reports.allocated-item-history.search') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    });

                    if (!response.ok) throw new Error('Search request failed');

                    const data = await response.json();
                    const container = document.getElementById('history-timeline-container');
                    if (container && data.html) {
                        container.innerHTML = data.html;
                    }
                } catch (error) {
                    console.error('History search error:', error);
                } finally {
                    this.loading = false;
                }
            }
        }">

            {{-- Single Search Field Card --}}
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border border-gray-200 dark:border-slate-700">
                <form @submit.prevent="searchHistory()" class="flex flex-col sm:flex-row items-end gap-4 max-w-xl">
                    <div class="w-full">
                        <label for="item_code" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            Item Code <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="item_code"
                            name="item_code"
                            x-model="item_code"
                            placeholder="Enter Item Code (e.g. ELE00001)"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono uppercase"
                            required>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition flex items-center justify-center gap-2 shadow-sm shrink-0">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        <span>Search</span>
                    </button>
                </form>
            </div>

            {{-- Timeline Output Container --}}
            <div id="history-timeline-container" class="relative">
                <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-800/70 z-10 flex items-center justify-center rounded-lg">
                    <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading item history...
                    </div>
                </div>

                @include('reports.inventory._item_history_timeline', ['historyData' => $historyData, 'hasSearched' => $hasSearched])
            </div>

        </div>
    </div>
</x-app-layout>
