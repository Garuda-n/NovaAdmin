<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Brand Code -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Brand Code <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="code"
            value="{{ old('code', $brand->code ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter Brand Code">

        @error('code')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Brand Name -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Brand Name <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $brand->name ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter Brand Name">

        @error('name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Brand Logo -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Brand Logo
        </label>

        <input
            type="file"
            name="logo"
            accept="image/*"
            class="block w-full text-sm text-gray-700 dark:text-gray-300
                file:mr-4
                file:rounded-lg
                file:border-0
                file:bg-indigo-600
                file:px-4
                file:py-2
                file:text-white
                hover:file:bg-indigo-700">

        @if(isset($brand) && $brand->logo_url)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                    Current Logo
                </p>

                <img
                    src="{{ $brand->logo_url }}"
                    alt="Brand Logo"
                    class="w-24 h-24 rounded-lg border object-cover">
            </div>
        @endif

        @error('logo')
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

            <option value="1" {{ old('status', $brand->status ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0" {{ old('status', $brand->status ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        @error('status')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="flex justify-end gap-3 mt-8">

    <a href="{{ route('brands.index') }}"
        class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
        Cancel
    </a>

    <button
        type="submit"
        class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        Save
    </button>

</div>