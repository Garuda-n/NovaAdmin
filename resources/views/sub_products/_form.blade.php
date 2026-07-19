<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Sub Product Code -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Sub Product Code <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="code"
            value="{{ old('code', $subProduct->code ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="e.g. SUB-001, SP-RED"
            required>
        @error('code')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Sub Product Name -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Sub Product Name / Display Label <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $subProduct->name ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="e.g. Red Trim, Extra Strap, Inner Liner"
            required>
        @error('name')
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
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            required>
            <option value="1" {{ old('status', $subProduct->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $subProduct->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<!-- Action Buttons -->
<div class="flex justify-end gap-3 mt-8">
    <a href="{{ route('sub-products.index') }}" class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
        Cancel
    </a>
    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        Save Sub Product
    </button>
</div>
