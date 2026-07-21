<x-app-layout>
    <script src="{{ asset('js/admin/setting_index.js') }}"></script>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen"
         x-data="ajaxSettingFilter({
             filterUrl: '{{ route('settings.filter') }}',
             csrfToken: '{{ csrf_token() }}'
         })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-slate-200 dark:bg-[#111827] dark:border-[#1f293d] shadow-xl p-6 sm:p-8">

                <x-toast />

                <!-- Header Section -->
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Settings Master
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                            Manage general system settings, configuration values, and descriptions.
                        </p>
                    </div>

                    @can('settings.edit')
                    <a href="{{ route('settings.create') }}"
                       class="inline-flex items-center justify-center gap-2 py-2.5 px-5 rounded-lg bg-[#5851ea] hover:bg-[#4b43e0] text-white font-medium text-sm transition shrink-0 shadow-md">
                        <span class="text-lg leading-none">+</span>
                        <span>Add Setting</span>
                    </a>
                    @endcan
                </div>

                <!-- Single Line Filter Bar -->
                <div class="bg-slate-50 border border-slate-200 dark:bg-[#1c2538] dark:border-[#27334d] rounded-xl p-4 mb-6">
                    <form @submit.prevent="applyFilter()" method="POST" action="{{ route('settings.filter') }}" class="flex flex-row items-center gap-3 w-full flex-nowrap">
                        @csrf
                        
                        <!-- Search Input -->
                        <div class="flex-1 min-w-0">
                            <input
                                type="text"
                                name="search"
                                x-model="search"
                                @input.debounce.400ms="applyFilter()"
                                placeholder="Search by setting key, value, or description..."
                                class="w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder-slate-400 dark:border-[#2b3752] dark:bg-[#161d2d] dark:text-white dark:placeholder-slate-400/70 py-2 px-4 focus:ring-2 focus:ring-[#5851ea] focus:border-[#5851ea] text-sm transition">
                        </div>

                        <!-- Filter Button -->
                        <button type="submit"
                                :disabled="loading"
                                class="px-6 py-2 rounded-lg bg-[#5851ea] hover:bg-[#4b43e0] text-white font-medium text-sm transition shadow shrink-0 whitespace-nowrap flex items-center gap-2">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>

                    </form>
                </div>

                <!-- Dynamic Table Container (Loads default data on opening, updates via AJAX) -->
                <div id="setting-table-container"
                     x-ref="tableContainer"
                     :class="{ 'opacity-50 pointer-events-none': loading }"
                     class="transition-opacity duration-200">
                    @include('settings._table', ['settings' => $settings])
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
