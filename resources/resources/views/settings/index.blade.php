<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-[#111827] rounded-2xl border border-[#1f293d] shadow-2xl p-6 sm:p-8">

                <x-toast />

                <!-- Header Section -->
                <div class="mb-8 pb-4 border-b border-slate-700/60">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">
                        General Settings
                    </h1>
                    <p class="text-slate-400 text-sm mt-1">
                        Configure system-wide settings, customer rules, and ERP scope policies.
                    </p>
                </div>

                <!-- Settings Form -->
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf

                    <div class="space-y-8">

                        <!-- Section 1: Customer Master Settings -->
                        <div class="bg-[#1c2538] border border-[#27334d] rounded-xl p-6 shadow-md">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Customer Scope Setting -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-slate-200">
                                    Customer Scope <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    <!-- Option 1: Global -->
                                    <label class="relative flex p-4 rounded-xl border cursor-pointer transition"
                                           :class="customerScope === 'Global' ? 'bg-indigo-950/40 border-indigo-500 text-white' : 'bg-[#161d2d] border-[#2b3752] text-slate-300 hover:bg-[#1f293e]'"
                                           x-data="{ customerScope: '{{ old('customer_scope', $customerScope) }}' }">
                                        <input
                                            type="radio"
                                            name="customer_scope"
                                            value="Global"
                                            class="sr-only"
                                            {{ old('customer_scope', $customerScope) === 'Global' ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-0.5">
                                                <div class="w-5 h-5 rounded-full border flex items-center justify-center {{ old('customer_scope', $customerScope) === 'Global' ? 'border-indigo-500 bg-indigo-600' : 'border-slate-500' }}">
                                                    @if(old('customer_scope', $customerScope) === 'Global')
                                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <span class="block font-bold text-sm text-white">Global Scope</span>
                                                <span class="block text-xs text-slate-400 mt-1">
                                                    Customers are created centrally. They are accessible across all company branches, and the Branch field is optional/null.
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Option 2: Branch Scope -->
                                    <label class="relative flex p-4 rounded-xl border cursor-pointer transition"
                                           :class="customerScope === 'Branch' ? 'bg-indigo-950/40 border-indigo-500 text-white' : 'bg-[#161d2d] border-[#2b3752] text-slate-300 hover:bg-[#1f293e]'">
                                        <input
                                            type="radio"
                                            name="customer_scope"
                                            value="Branch"
                                            class="sr-only"
                                            {{ old('customer_scope', $customerScope) === 'Branch' ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-0.5">
                                                <div class="w-5 h-5 rounded-full border flex items-center justify-center {{ old('customer_scope', $customerScope) === 'Branch' ? 'border-indigo-500 bg-indigo-600' : 'border-slate-500' }}">
                                                    @if(old('customer_scope', $customerScope) === 'Branch')
                                                        <div class="w-2 h-2 rounded-full bg-white"></div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <span class="block font-bold text-sm text-white">Branch Scope</span>
                                                <span class="block text-xs text-slate-400 mt-1">
                                                    Customers are bound to a specific branch. Selecting a Branch becomes mandatory during customer creation.
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                                </div>

                                @error('customer_scope')
                                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Save Settings Button -->
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                    class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition shadow-lg shadow-indigo-600/20">
                                Save Settings
                            </button>
                        </div>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
