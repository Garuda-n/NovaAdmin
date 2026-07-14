<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Tax Code --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Tax Code
        </label>

        <input type="text"
               value="{{ $tax->code ?? 'Auto Generated' }}"
               class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white bg-gray-100"
               readonly>
    </div>

    {{-- Tax Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Tax Name <span class="text-red-500">*</span>
        </label>

        <input type="text"
               name="name"
               value="{{ old('name', $tax->name ?? '') }}"
               class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
               placeholder="Enter Tax Name">

        @error('name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Percentage --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Percentage <span class="text-red-500">*</span>
        </label>

        <input type="number"
               name="percentage"
               step="0.01"
               min="0"
               max="100"
               value="{{ old('percentage', $tax->percentage ?? '') }}"
               class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
               placeholder="18.00">

        @error('percentage')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Status <span class="text-red-500">*</span>
        </label>

        <select
            name="status"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">

            <option value="1"
                {{ old('status', $tax->status ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0"
                {{ old('status', $tax->status ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        @error('status')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="flex justify-end gap-3 mt-8">

    <a href="{{ route('taxes.index') }}"
       class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
        Cancel
    </a>

    <button
        type="submit"
        class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

        {{ isset($tax) ? 'Update' : 'Save' }}

    </button>

</div>