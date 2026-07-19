<div class="space-y-6">

    <!-- Hidden Default Group & Type -->
    <input type="hidden" name="group" value="{{ old('group', $setting->group ?? 'general') }}">
    <input type="hidden" name="type" value="{{ old('type', $setting->type ?? 'string') }}">

    <!-- Setting Key -->
    <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">
            Setting Key <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="key"
            value="{{ old('key', $setting->key ?? '') }}"
            class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono"
            placeholder="e.g. customer_scope"
            required>
        <p class="text-xs text-slate-400 mt-1">Unique setting key (e.g. customer_scope, company_name)</p>
        @error('key')
            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Setting Value -->
    <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">
            Setting Value
        </label>
        <textarea
            name="value"
            rows="3"
            class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono"
            placeholder="e.g. Global / Branch / 1 / 0">{{ old('value', $setting->value ?? '') }}</textarea>
        @error('value')
            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">
            Description
        </label>
        <textarea
            name="description"
            rows="3"
            class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
            placeholder="Explain what this setting controls in the system...">{{ old('description', $setting->description ?? '') }}</textarea>
        @error('description')
            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>
