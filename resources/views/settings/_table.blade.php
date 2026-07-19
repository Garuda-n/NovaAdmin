<!-- Table Card Container -->
<div class="bg-white border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 dark:bg-[#25314a] dark:border-[#2b3752]">
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        #
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        SETTING KEY
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        VALUE
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        DESCRIPTION
                    </th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center">
                        ACTION
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 dark:divide-[#242f48]">

                @forelse($settings as $st)
                    <tr class="hover:bg-slate-50 dark:hover:bg-[#212b40] transition duration-150">

                        <!-- ID -->
                        <td class="py-4 px-6 text-slate-800 dark:text-slate-200 text-sm font-semibold">
                            {{ $loop->iteration }}
                        </td>

                        <!-- KEY -->
                        <td class="py-4 px-6 text-slate-900 dark:text-white font-bold text-sm font-mono">
                            <a href="{{ route('settings.edit', $st) }}" class="hover:text-indigo-600 dark:hover:text-[#5851ea] transition">
                                {{ $st->key }}
                            </a>
                        </td>

                        <!-- VALUE -->
                        <td class="py-4 px-6 text-slate-800 dark:text-slate-200 text-sm font-mono max-w-xs">
                            <span class="bg-slate-100 border border-slate-300 text-indigo-700 dark:bg-[#121826] dark:border-slate-700/60 dark:text-indigo-300 px-3 py-1.5 rounded font-semibold inline-block break-all">
                                {{ $st->value !== null && $st->value !== '' ? $st->value : 'null' }}
                            </span>
                        </td>

                        <!-- DESCRIPTION -->
                        <td class="py-4 px-6 text-slate-700 dark:text-slate-300 text-sm">
                            {{ $st->description ?: '-' }}
                        </td>

                        <!-- ACTION -->
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @can('settings.edit')
                                <a href="{{ route('settings.edit', $st) }}"
                                   title="Edit Setting"
                                   class="p-2 rounded-lg bg-[#f59e0b] hover:bg-[#d97706] text-white transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                @endcan

                                @can('settings.delete')
                                <form action="{{ route('settings.destroy', $st) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this setting?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Setting"
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
                        <td colspan="5" class="py-12 px-6 text-center text-slate-400 font-medium">
                            No settings found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>
</div>

<!-- Pagination -->
@if($settings->hasPages())
<div class="mt-6 pagination-wrapper">
    {{ $settings->links() }}
</div>
@endif
