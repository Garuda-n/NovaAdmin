<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                                Create Company
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                                Add a new company to the system.
                            </p>
                        </div>

                        <a href="{{ route('companies.index') }}"
                           class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 text-sm font-medium transition">
                            ← Back to List
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-500/10 border border-red-500 text-red-600 dark:text-red-300 p-4">
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Company Name --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="e.g. Acme Corporation"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            </div>

                            {{-- Company Code --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Company Code <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="code"
                                    value="{{ old('code') }}"
                                    required
                                    placeholder="e.g. ACME"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase">
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Phone Number
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="e.g. +1 234 567 890"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="e.g. contact@acme.com"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Address
                                </label>

                                <textarea
                                    name="address"
                                    rows="3"
                                    placeholder="Street address, building, suite, etc."
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('address') }}</textarea>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>

                                <select
                                    name="status"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('companies.index') }}"
                               class="px-5 py-2.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 font-medium transition">
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-6 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition shadow-lg">
                                Save Company
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>