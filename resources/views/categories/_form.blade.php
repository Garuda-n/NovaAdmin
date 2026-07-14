<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Category Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Category Name <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
            placeholder="Enter Category Name">

        @error('name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Short Code --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Short Code <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="code"
            value="{{ old('code', $category->code ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
            placeholder="Ex : ELEC">

        @error('code')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tax --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Tax
        </label>

        <select
            name="tax_id"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">

            <option value="">-- Select Tax --</option>

            @foreach($taxes as $tax)
                <option value="{{ $tax->id }}"
                    {{ old('tax_id', $category->tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                    {{ $tax->name }} ({{ number_format($tax->percentage,2) }}%)
                </option>
            @endforeach

        </select>

        @error('tax_id')
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
                {{ old('status', $category->status ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0"
                {{ old('status', $category->status ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        @error('status')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="flex justify-end gap-3 mt-8">

    <a href="{{ route('categories.index') }}"
        class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
        Cancel
    </a>

    <button
        type="submit"
        class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

        {{ isset($category) ? 'Update' : 'Save' }}

    </button>

</div>