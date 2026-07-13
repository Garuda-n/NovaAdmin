<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Financial Year
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg">

                <form action="{{ route('financial-years.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Start Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Date <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                value="{{ old('start_date') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">

                            @error('start_date')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Date <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                value="{{ old('end_date') }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">

                            @error('end_date')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="status"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">

                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                            </select>

                            @error('status')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-8">

                        <a href="{{ route('financial-years.index') }}"
                            class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Save
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>