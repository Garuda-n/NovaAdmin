<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit UOM
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg">

                <form action="{{ route('uoms.update', $uom) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- UOM Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                UOM Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $uom->name) }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">

                            @error('name')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Short Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Short Code <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="shortcode"
                                value="{{ old('shortcode', $uom->shortcode) }}"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">

                            @error('shortcode')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Has Decimals -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Has Decimals <span class="text-red-500">*</span>
                            </label>

                            <select
                                name="has_decimals"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">

                                <option value="1" {{ old('has_decimals', $uom->has_decimals) == 1 ? 'selected' : '' }}>
                                    Yes
                                </option>

                                <option value="0" {{ old('has_decimals', $uom->has_decimals) == 0 ? 'selected' : '' }}>
                                    No
                                </option>

                            </select>

                            @error('has_decimals')
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

                                <option value="1" {{ old('status', $uom->status) == 1 ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="0" {{ old('status', $uom->status) == 0 ? 'selected' : '' }}>
                                    Inactive
                                </option>

                            </select>

                            @error('status')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-8">

                        <a href="{{ route('uoms.index') }}"
                            class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Update
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>