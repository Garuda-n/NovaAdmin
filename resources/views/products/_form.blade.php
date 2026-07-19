<div class="space-y-6">

    <!-- Section 1: Basic Information -->
    <div class="border-b border-gray-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Basic Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Product Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Product Code (SKU) <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="code"
                    value="{{ old('code', $product->code ?? '') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g. PRD-00001"
                    required>
                @error('code')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Product Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $product->name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g. Samsung 250L Refrigerator"
                    required>
                @error('name')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- HSN Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    HSN / SAC Code
                </label>
                <input
                    type="text"
                    name="hsn_code"
                    value="{{ old('hsn_code', $product->hsn_code ?? '') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g. 84181010">
                @error('hsn_code')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>
                <select
                    name="category_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">— Select Category —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Brand <span class="text-red-500">*</span>
                </label>
                <select
                    name="brand_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">— Select Brand —</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Product Image
                </label>
                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-white hover:file:bg-indigo-700">
                @if(isset($product) && $product->image_url)
                    <div class="mt-3">
                        <img src="{{ $product->image_url }}" alt="Product Image" class="w-20 h-20 rounded-lg border object-cover">
                    </div>
                @endif
                @error('image')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Section 2: Tax Configuration -->
    <div class="border-b border-gray-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Tax Settings
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Tax Slab -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tax Slab
                </label>
                <select
                    name="tax_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— None / Category Fallback —</option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}" {{ old('tax_id', $product->tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                            {{ $tax->name }} ({{ $tax->percentage }}%)
                        </option>
                    @endforeach
                </select>
                @error('tax_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tax Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tax Type <span class="text-red-500">*</span>
                </label>
                <select
                    name="tax_type"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="exclusive" {{ old('tax_type', $product->tax_type ?? 'exclusive') == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                    <option value="inclusive" {{ old('tax_type', $product->tax_type ?? 'exclusive') == 'inclusive' ? 'selected' : '' }}>Inclusive</option>
                </select>
                @error('tax_type')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sales Based On -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sales Based On <span class="text-red-500">*</span>
                </label>
                <select
                    name="sales_based_on"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="fixed" {{ old('sales_based_on', $product->sales_based_on ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="flexible" {{ old('sales_based_on', $product->sales_based_on ?? 'fixed') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                </select>
                @error('sales_based_on')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Purchase Based On -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Purchase Based On <span class="text-red-500">*</span>
                </label>
                <select
                    name="purchase_based_on"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="fixed" {{ old('purchase_based_on', $product->purchase_based_on ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="flexible" {{ old('purchase_based_on', $product->purchase_based_on ?? 'fixed') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                </select>
                @error('purchase_based_on')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    <!-- Section 3: Measurement & Attributes -->
    <div x-data="{ showSizes: {{ old('has_size', $product->has_size ?? false) ? 'true' : 'false' }}, showSubProducts: {{ old('has_sub_product', $product->has_sub_product ?? false) ? 'true' : 'false' }} }" class="border-b border-gray-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Measurement & Attributes
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- UOM -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Unit of Measurement (UOM) <span class="text-red-500">*</span>
                </label>
                <select
                    name="uom_id"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">— Select UOM —</option>
                    @foreach($uoms as $uom)
                        <option value="{{ $uom->id }}" {{ old('uom_id', $product->uom_id ?? '') == $uom->id ? 'selected' : '' }}>
                            {{ $uom->name }} ({{ $uom->shortcode }})
                        </option>
                    @endforeach
                </select>
                @error('uom_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Calculation Based On -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Calculation Based On <span class="text-red-500">*</span>
                </label>
                <select
                    name="calculation_based_on"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="quantity" {{ old('calculation_based_on', $product->calculation_based_on ?? 'quantity') == 'quantity' ? 'selected' : '' }}>Quantity</option>
                    <option value="weight" {{ old('calculation_based_on', $product->calculation_based_on ?? 'quantity') == 'weight' ? 'selected' : '' }}>Weight</option>
                    <option value="sqft" {{ old('calculation_based_on', $product->calculation_based_on ?? 'quantity') == 'sqft' ? 'selected' : '' }}>Sqft</option>
                    <option value="dimension" {{ old('calculation_based_on', $product->calculation_based_on ?? 'quantity') == 'dimension' ? 'selected' : '' }}>Dimension</option>
                </select>
                @error('calculation_based_on')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Has Size Checkbox -->
            <div class="flex items-center pt-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="has_size"
                        value="1"
                        x-model="showSizes"
                        class="sr-only peer"
                        {{ old('has_size', $product->has_size ?? false) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Has Size Options</span>
                </label>
            </div>

            <!-- Has Sub Product Checkbox -->
            <div class="flex items-center pt-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="has_sub_product"
                        value="1"
                        x-model="showSubProducts"
                        class="sr-only peer"
                        {{ old('has_sub_product', $product->has_sub_product ?? false) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Has Sub Products</span>
                </label>
            </div>

        </div>

        <!-- Dynamic Size Selection Checkboxes -->
        <div x-show="showSizes" x-transition class="mt-6 p-4 rounded-lg bg-gray-50 dark:bg-slate-900/60 border border-gray-200 dark:border-slate-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Select Applicable Sizes for this Product
            </label>
            @php
                $selectedSizeIds = old('size_ids', isset($product) ? $product->sizes->pluck('id')->toArray() : []);
            @endphp
            @if(isset($sizes) && count($sizes) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($sizes as $sz)
                        <label class="inline-flex items-center p-2.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer transition">
                            <input
                                type="checkbox"
                                name="size_ids[]"
                                value="{{ $sz->id }}"
                                class="rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                {{ in_array($sz->id, $selectedSizeIds) ? 'checked' : '' }}>
                            <span class="ml-2.5 text-sm text-gray-700 dark:text-gray-200 font-medium">{{ $sz->code }} ({{ $sz->name }})</span>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-amber-500">No sizes found in Size Master. <a href="{{ route('sizes.create') }}" class="underline font-semibold" target="_blank">Add sizes first</a>.</p>
            @endif
        </div>

        <!-- Dynamic Sub Product Selection Checkboxes -->
        <div x-show="showSubProducts" x-transition class="mt-6 p-4 rounded-lg bg-gray-50 dark:bg-slate-900/60 border border-gray-200 dark:border-slate-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Select Applicable Sub Products
            </label>
            @php
                $selectedSubProductIds = old('sub_product_ids', isset($product) ? $product->subProducts->pluck('id')->toArray() : []);
            @endphp
            @if(isset($subProducts) && count($subProducts) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($subProducts as $sp)
                        <label class="inline-flex items-center p-2.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 cursor-pointer transition">
                            <input
                                type="checkbox"
                                name="sub_product_ids[]"
                                value="{{ $sp->id }}"
                                class="rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                {{ in_array($sp->id, $selectedSubProductIds) ? 'checked' : '' }}>
                            <span class="ml-2.5 text-sm text-gray-700 dark:text-gray-200 font-medium">{{ $sp->code }} ({{ $sp->name }})</span>
                        </label>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-amber-500">No sub products found in Sub Product Master. <a href="{{ route('sub-products.create') }}" class="underline font-semibold" target="_blank">Add sub products first</a>.</p>
            @endif
        </div>
    </div>

    <!-- Section 4: Inventory & Reordering -->
    <div class="border-b border-gray-200 dark:border-slate-700 pb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Stock & Reorder
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Reorder Applicable Checkbox -->
            <div class="flex items-center pt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="reorder_applicable"
                        value="1"
                        class="sr-only peer"
                        {{ old('reorder_applicable', $product->reorder_applicable ?? false) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Reorder Applicable</span>
                </label>
            </div>

            <!-- Minimum Stock Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Minimum Stock Alert Level
                </label>
                <input
                    type="number"
                    step="0.01"
                    name="min_stock_level"
                    value="{{ old('min_stock_level', $product->min_stock_level ?? 0) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="0.00">
                @error('min_stock_level')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <!-- Section 5: Description & Status -->
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description / Specifications
                </label>
                <textarea
                    name="description"
                    rows="3"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter product description or specifications">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
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
                    <option value="1" {{ old('status', $product->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $product->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

</div>

<!-- Buttons -->
<div class="flex justify-end gap-3 mt-8">
    <a href="{{ route('products.index') }}" class="px-5 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
        Cancel
    </a>
    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        Save Product
    </button>
</div>
