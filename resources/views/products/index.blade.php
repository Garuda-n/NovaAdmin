<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Product Master
            </h2>

            @can('products.create')
            <a href="{{ route('products.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700">
                + Add Product
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4">
                <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name, code, HSN..."
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand Filter -->
                    <div>
                        <select name="brand_id" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'brand_id']))
                            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-100 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Brand</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">HSN</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">UOM</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Tax Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Status</th>
                                @if(Auth::user()->can('products.edit') || Auth::user()->can('products.delete'))
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                    <!-- Image -->
                                    <td class="px-6 py-4">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="Product" class="w-10 h-10 rounded-lg border object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
                                                <x-heroicon-o-cube class="w-6 h-6 text-gray-400" />
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Code -->
                                    <td class="px-6 py-4 font-mono text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                        {{ $product->code }}
                                    </td>

                                    <!-- Name -->
                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200 font-medium">
                                        {{ $product->name }}
                                    </td>

                                    <!-- Category -->
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-sm">
                                        {{ $product->category?->name ?? '—' }}
                                    </td>

                                    <!-- Brand -->
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-sm">
                                        {{ $product->brand?->name ?? '—' }}
                                    </td>

                                    <!-- HSN -->
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm font-mono">
                                        {{ $product->hsn_code ?? '—' }}
                                    </td>

                                    <!-- UOM -->
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-sm">
                                        {{ $product->uom?->shortcode ?? $product->uom?->name ?? '—' }}
                                    </td>

                                    <!-- Tax Type -->
                                    <td class="px-6 py-4 text-sm capitalize text-gray-600 dark:text-gray-300">
                                        {{ $product->tax_type }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @if($product->status)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    @if(Auth::user()->can('products.edit') || Auth::user()->can('products.delete'))
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">
                                            @can('products.edit')
                                            <a href="{{ route('products.edit', $product) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition"
                                                title="Edit">
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </a>
                                            @endcan

                                            @can('products.delete')
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                  onsubmit="return confirm('Delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-500 hover:bg-red-600 text-white transition"
                                                    title="Delete">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
