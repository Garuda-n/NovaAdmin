<script src="{{ asset('js/masters/supplier_form.js') }}"></script>
<div x-data="supplierForm({
    countryId: '{{ old('country_id', $supplier->country_id ?? ($defaultCountry->id ?? '')) }}',
    stateId: '{{ old('state_id', $supplier->state_id ?? ($defaultState->id ?? '')) }}',
    cityId: '{{ old('city_id', $supplier->city_id ?? '') }}',
    isBranchWise: {{ (isset($supplierScope) && strcasecmp($supplierScope, 'Branch') === 0) ? 'true' : 'false' }}
})" class="space-y-6">

    <!-- Section 1: Supplier Information -->
    <div class="border-b border-slate-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            Supplier Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Supplier Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Supplier Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="supplier_name"
                    value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. ABC Enterprises"
                    required>
                @error('supplier_name')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supplier Code -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Supplier Code
                </label>
                <input
                    type="text"
                    name="supplier_code"
                    value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. SUP-001">
                @error('supplier_code')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Person -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Contact Person
                </label>
                <input
                    type="text"
                    name="contact_person"
                    value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. Rajesh Kumar">
                @error('contact_person')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mobile -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="mobile"
                    value="{{ old('mobile', $supplier->mobile ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 9876543210"
                    required>
                @error('mobile')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alternate Mobile -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Alternate Mobile
                </label>
                <input
                    type="text"
                    name="alternate_mobile"
                    value="{{ old('alternate_mobile', $supplier->alternate_mobile ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 9876543211">
                @error('alternate_mobile')
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
                    value="{{ old('email', $supplier->email ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. supplier@example.com">
                @error('email')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supplier Type -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Supplier Type <span class="text-red-500">*</span>
                </label>
                <select
                    name="supplier_type"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">— Select Supplier Type —</option>
                    @foreach(\App\Models\Supplier::SUPPLIER_TYPES as $type)
                        <option value="{{ $type }}" {{ old('supplier_type', $supplier->supplier_type ?? '') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_type')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Section 2: Tax Information -->
    <div class="border-b border-slate-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            Tax Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- GST Number -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    GST Number
                </label>
                <input
                    type="text"
                    name="gst_number"
                    value="{{ old('gst_number', $supplier->gst_number ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase"
                    placeholder="e.g. 24AAAAA0000A1Z5">
                @error('gst_number')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- PAN Number -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    PAN Number
                </label>
                <input
                    type="text"
                    name="pan_number"
                    value="{{ old('pan_number', $supplier->pan_number ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase"
                    placeholder="e.g. ABCDE1234F">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Format: 5 letters, 4 digits, 1 letter</p>
                @error('pan_number')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Section 3: Address & Location Details -->
    <div class="border-b border-slate-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            Address & Location Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

            <!-- Country Select -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Country <span class="text-red-500">*</span>
                </label>
                <select
                    name="country_id"
                    x-model="countryId"
                    @change="fetchStates()"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
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
                    State <span class="text-red-500">*</span>
                </label>
                <select
                    name="state_id"
                    x-model="stateId"
                    @change="fetchCities()"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">— Select State —</option>
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
                    City <span class="text-red-500">*</span>
                </label>
                <select
                    name="city_id"
                    x-model="cityId"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">— Select City —</option>
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
                    Pincode <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="pincode"
                    value="{{ old('pincode', $supplier->pincode ?? '') }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 380001"
                    required>
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
                rows="2"
                class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                placeholder="Street address, building, suite, etc.">{{ old('address', $supplier->address ?? '') }}</textarea>
            @error('address')
                <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Section 4: Financial & Account Terms -->
    <div>
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            Financial & Account Terms
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- Branch (Visible only if supplier_scope is Branch) -->
            <div x-show="isBranchWise">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Branch <span class="text-red-500">*</span>
                </label>
                <select
                    name="branch_id"
                    :required="isBranchWise"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">— Select Branch —</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $supplier->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->branch_code }})
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Opening Balance -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Opening Balance (₹)
                </label>
                <input
                    type="number"
                    step="0.01"
                    name="opening_balance"
                    value="{{ old('opening_balance', $supplier->opening_balance ?? 0) }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="0.00">
                @error('opening_balance')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Credit Limit -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Credit Limit (₹)
                </label>
                <input
                    type="number"
                    step="0.01"
                    name="credit_limit"
                    value="{{ old('credit_limit', $supplier->credit_limit ?? 0) }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="0.00">
                @error('credit_limit')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Credit Days -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Credit Days
                </label>
                <input
                    type="number"
                    name="credit_days"
                    value="{{ old('credit_days', $supplier->credit_days ?? 0) }}"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="0">
                @error('credit_days')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    name="status"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="1" {{ old('status', $supplier->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $supplier->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

</div>
