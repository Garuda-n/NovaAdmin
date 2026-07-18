<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Menu Name -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Menu Name <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $menu->name ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter Menu Name">

        @error('name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Route -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Route Name
        </label>

        <input
            type="text"
            name="route"
            value="{{ old('route', $menu->route ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="e.g. users.index (leave blank for dropdown groups)">

        @error('route')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Icon -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Icon Name
        </label>

        <input
            type="text"
            name="icon"
            value="{{ old('icon', $menu->icon ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="e.g. home, users, cog-6-tooth">

        @error('icon')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Parent Menu -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Parent Menu
        </label>

        <select
            name="parent_id"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">

            <option value="">— None (Root Level) —</option>

            @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                    {{ old('parent_id', $menu->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
            @endforeach

        </select>

        @error('parent_id')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Permission Slug -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Permission Slug
        </label>

        <input
            type="text"
            name="permission_slug"
            value="{{ old('permission_slug', $menu->permission_slug ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="e.g. users.view">

        @error('permission_slug')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Order -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Order <span class="text-red-500">*</span>
        </label>

        <input
            type="number"
            name="order"
            value="{{ old('order', $menu->order ?? 0) }}"
            min="0"
            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="0">

        @error('order')
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

            <option value="1" {{ old('status', $menu->status ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0" {{ old('status', $menu->status ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        @error('status')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="flex justify-end gap-3 mt-8">

    <a href="{{ route('menus.index') }}"
        class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
        Cancel
    </a>

    <button
        type="submit"
        class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        Save
    </button>

</div>
