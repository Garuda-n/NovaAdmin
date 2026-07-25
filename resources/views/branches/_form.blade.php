<script src="{{ asset('js/masters/branch_form.js') }}"></script>
<div x-data="branchForm({
    countryId: '{{ old('country_id', $branch->country_id ?? ($defaultCountry->id ?? '')) }}',
    stateId: '{{ old('state_id', $branch->state_id ?? ($defaultState->id ?? '')) }}',
    cityId: '{{ old('city_id', $branch->city_id ?? '') }}'
})" class="space-y-6">

    <!-- Section 1: Basic & Contact Information -->
    <div class="border-b border-slate-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            Basic & Contact Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Company Select -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Company <span class="text-red-500">*</span>
                </label>
                <select
                    name="company_id"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}"
                            {{ old('company_id', $branch->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Branch Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Branch Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $branch->name ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. Coimbatore Branch"
                    required>
                @error('name')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Branch Code -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Branch Code <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="branch_code"
                    value="{{ old('branch_code', $branch->branch_code ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase"
                    placeholder="e.g. CBE001"
                    required>
                @error('branch_code')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- GST Number -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    GST Number
                </label>
                <input
                    type="text"
                    name="gst_number"
                    value="{{ old('gst_number', $branch->gst_number ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase"
                    placeholder="e.g. 33AAAAA0000A1Z5">
                @error('gst_number')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Phone
                </label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $branch->phone ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 9876543210">
                @error('phone')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $branch->email ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. branch@example.com">
                @error('email')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Section 2: Address & Location Details -->
    <div class="border-b border-slate-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            Address & Location Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

            <!-- Country Select -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Country
                </label>
                <select
                    name="country_id"
                    x-model="countryId"
                    @change="fetchStates()"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">— Select Country —</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- State Select -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    State
                </label>
                <select
                    name="state_id"
                    x-model="stateId"
                    @change="fetchCities()"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">— Select State —</option>
                    @if(isset($states) && count($states) > 0)
                        @foreach($states as $st)
                            <option value="{{ $st->id }}" {{ old('state_id', $branch->state_id ?? '') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }}
                            </option>
                        @endforeach
                    @endif
                    <template x-for="st in statesList" :key="st.id">
                        <option :value="st.id" :selected="st.id == stateId" x-text="st.name"></option>
                    </template>
                </select>
                @error('state_id')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- City Select -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    City
                </label>
                <select
                    name="city_id"
                    x-model="cityId"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">— Select City —</option>
                    @if(isset($cities) && count($cities) > 0)
                        @foreach($cities as $ct)
                            <option value="{{ $ct->id }}" {{ old('city_id', $branch->city_id ?? '') == $ct->id ? 'selected' : '' }}>
                                {{ $ct->name }}
                            </option>
                        @endforeach
                    @endif
                    <template x-for="ct in citiesList" :key="ct.id">
                        <option :value="ct.id" :selected="ct.id == cityId" x-text="ct.name"></option>
                    </template>
                </select>
                @error('city_id')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pincode -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Pincode
                </label>
                <input
                    type="text"
                    name="pincode"
                    value="{{ old('pincode', $branch->pincode ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 641001">
                @error('pincode')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Full Address -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Full Address
            </label>
            <textarea
                name="address"
                rows="3"
                class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                placeholder="Street address, building, suite, etc.">{{ old('address', $branch->address ?? '') }}</textarea>
            @error('address')
                <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Section 3: Branch Settings & Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Head Office Checkbox -->
        <div class="flex items-center pt-2">
            <input
                id="is_head_office"
                type="checkbox"
                name="is_head_office"
                value="1"
                {{ old('is_head_office', $branch->is_head_office ?? false) ? 'checked' : '' }}
                class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800">
            <label for="is_head_office" class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                Mark as Head Office
            </label>
        </div>

        <!-- Status -->
        @if(isset($branch) && $branch->exists)
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Status <span class="text-red-500">*</span>
            </label>
            <select
                name="status"
                class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                required>
                <option value="1" {{ old('status', $branch->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $branch->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endif

    </div>

</div>
