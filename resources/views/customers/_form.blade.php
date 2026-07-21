<div x-data="customerForm({
    type: '{{ old('customer_type', $customer->customer_type ?? 'B2C') }}',
    countryId: '{{ old('country_id', $customer->country_id ?? ($defaultCountry->id ?? '')) }}',
    stateId: '{{ old('state_id', $customer->state_id ?? ($defaultState->id ?? '')) }}',
    cityId: '{{ old('city_id', $customer->city_id ?? '') }}',
    isBranchWise: {{ \App\Services\CustomerService::isBranchWiseCustomer() ? 'true' : 'false' }}
})" class="space-y-6">

    <!-- Section 1: Basic & Contact Information -->
    <div class="border-b border-slate-700 pb-6">
        <h3 class="text-lg font-medium text-white mb-4">
            Basic & Contact Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Customer Type -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Customer Type <span class="text-red-500">*</span>
                </label>
                <select
                    name="customer_type"
                    x-model="type"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="B2C">B2C (Individual / Retail)</option>
                    <option value="B2B">B2B (Business / Corporate)</option>
                </select>
                @error('customer_type')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Name -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Customer / Company Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="customer_name"
                    value="{{ old('customer_name', $customer->customer_name ?? '') }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. John Doe / Acme Corp"
                    required>
                @error('customer_name')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- GST Number -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    GST Number <span x-show="type === 'B2B'" class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="gst_number"
                    value="{{ old('gst_number', $customer->gst_number ?? '') }}"
                    :required="type === 'B2B'"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase"
                    placeholder="e.g. 24AAAAA0000A1Z5">
                <p x-show="type === 'B2C'" class="text-xs text-slate-400 mt-1">Optional for B2C customers</p>
                <p x-show="type === 'B2B'" class="text-xs text-amber-400 mt-1">Mandatory 15-digit GSTIN for B2B</p>
                @error('gst_number')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mobile -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="mobile"
                    value="{{ old('mobile', $customer->mobile ?? '') }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 9876543210"
                    required>
                @error('mobile')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alternate Mobile -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Alternate Mobile
                </label>
                <input
                    type="text"
                    name="alternate_mobile"
                    value="{{ old('alternate_mobile', $customer->alternate_mobile ?? '') }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 9876543211">
                @error('alternate_mobile')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $customer->email ?? '') }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. customer@example.com">
                @error('email')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Section 2: Address & Location Details (Standard Select Elements) -->
    <div class="border-b border-slate-700 pb-6">
        <h3 class="text-lg font-medium text-white mb-4">
            Address & Location Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

            <!-- Country Select -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Country <span class="text-red-500">*</span>
                </label>
                <select
                    name="country_id"
                    x-model="countryId"
                    @change="fetchStates()"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">— Select Country —</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- State Select -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    State <span class="text-red-500">*</span>
                </label>
                <select
                    name="state_id"
                    x-model="stateId"
                    @change="fetchCities()"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">— Select State —</option>
                    <template x-for="st in statesList" :key="st.id">
                        <option :value="st.id" :selected="st.id == stateId" x-text="st.name"></option>
                    </template>
                    @if(isset($states))
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id', $customer->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('state_id')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- City Select -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    City <span class="text-red-500">*</span>
                </label>
                <select
                    name="city_id"
                    x-model="cityId"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="">— Select City —</option>
                    <template x-for="ct in citiesList" :key="ct.id">
                        <option :value="ct.id" :selected="ct.id == cityId" x-text="ct.name"></option>
                    </template>
                    @if(isset($cities))
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $customer->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('city_id')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pincode -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Pincode <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="pincode"
                    value="{{ old('pincode', $customer->pincode ?? '') }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="e.g. 380001"
                    required>
                @error('pincode')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Full Address -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Full Address
            </label>
            <textarea
                name="address"
                rows="2"
                class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                placeholder="Street address, building, suite, etc.">{{ old('address', $customer->address ?? '') }}</textarea>
            @error('address')
                <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Section 3: Branch, Credit Terms & Status -->
    <div>
        <h3 class="text-lg font-medium text-white mb-4">
            Financial & Account Terms
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- Branch (Visible only if branch_wise_customer is enabled) -->
            <div x-show="isBranchWise">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Branch <span class="text-red-500">*</span>
                </label>
                <select
                    name="branch_id"
                    :required="isBranchWise"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">— Select Branch —</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $customer->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->branch_code }})
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Credit Limit -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Credit Limit (₹)
                </label>
                <input
                    type="number"
                    step="0.01"
                    name="credit_limit"
                    value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="0.00">
                @error('credit_limit')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Credit Days -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Credit Days
                </label>
                <input
                    type="number"
                    name="credit_days"
                    value="{{ old('credit_days', $customer->credit_days ?? 0) }}"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="0">
                @error('credit_days')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    name="status"
                    class="w-full rounded-lg border-slate-600 bg-slate-800 text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    required>
                    <option value="1" {{ old('status', $customer->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $customer->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

</div>

<script src="{{ asset('js/masters/customer_form.js') }}" defer></script>
