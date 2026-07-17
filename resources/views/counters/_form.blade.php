<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-2">
            Counter Name <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="counter_name"
            value="{{ old('counter_name', $counter->counter_name ?? '') }}"
            class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

        @error('counter_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-300 mb-2">
            Counter Code <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="counter_code"
            value="{{ old('counter_code', $counter->counter_code ?? '') }}"
            class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

        @error('counter_code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="mt-6">

    <label class="block text-sm font-medium text-gray-300 mb-2">
        Status
    </label>

    <select
        name="status"
        class="w-full md:w-60 rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-2">

        <option value="1" {{ old('status',1)==1 ? 'selected' : '' }}>
            Active
        </option>

        <option value="0" {{ old('status')==='0' ? 'selected' : '' }}>
            Inactive
        </option>

    </select>

</div>

</div>

<div class="mt-8 flex gap-3">

    <button
        type="submit"
        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">

        Save
    </button>

    <a
        href="{{ route('counters.index') }}"
        class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">

        Cancel
    </a>

</div>