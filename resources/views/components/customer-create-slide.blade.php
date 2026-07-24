@can('customers.create')
<div x-data="customerSlideComponent()"
     x-cloak
     x-show="isOpen"
     style="display: none;"
     @open-customer-slide.window="openSlide($event.detail)"
     @keydown.escape.window="closeSlide()"
     class="relative z-[999]">

    <!-- Backdrop Overlay -->
    <div x-show="isOpen"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeSlide()"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Slide Panel Container -->
    <div x-show="isOpen" style="display: none;" class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div x-show="isOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-2xl bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col">

            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Add New Customer
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Create customer master record dynamically without leaving your page.
                    </p>
                </div>
                <button type="button" @click="closeSlide()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Form Content -->
            <form id="global-customer-slide-form" @submit.prevent="submitForm($el)" class="flex-1 flex flex-col min-h-0">
                @csrf
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    @include('customers._form', ['customer' => new \App\Models\Customer()])
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/80 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="closeSlide()" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition flex items-center gap-2 shadow-lg disabled:opacity-50">
                        <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="submitting ? 'Saving Customer...' : 'Save Customer'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
