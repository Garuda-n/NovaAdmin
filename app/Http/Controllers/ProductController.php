<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\SubProduct;
use App\Models\Tax;
use App\Models\Uom;
use App\Services\ActivityLogService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'uom', 'tax', 'sizes', 'subProducts'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('hsn_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->get();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('products._table', compact('products'))->render()
            ]);
        }

        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $uoms = Uom::where('status', 1)->orderBy('name')->get();
        $taxes = Tax::where('status', 1)->orderBy('name')->get();
        $sizes = Size::where('status', 1)->orderBy('name')->get();
        $subProducts = SubProduct::where('status', 1)->orderBy('name')->get();

        return view('products.create', compact('categories', 'brands', 'uoms', 'taxes', 'sizes', 'subProducts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:150',
            'hsn_code' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'uom_id' => 'required|exists:uoms,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'tax_type' => ['required', Rule::in(['inclusive', 'exclusive'])],
            'sales_based_on' => ['required', Rule::in(['fixed', 'flexible'])],
            'purchase_based_on' => ['required', Rule::in(['fixed', 'flexible'])],
            'has_size' => 'nullable|boolean',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'has_sub_product' => 'nullable|boolean',
            'sub_product_ids' => 'nullable|array',
            'sub_product_ids.*' => 'exists:sub_products,id',
            'calculation_based_on' => ['required', Rule::in(['quantity', 'weight', 'sqft', 'dimension'])],
            'reorder_applicable' => 'nullable|boolean',
            'min_stock_level' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $validated['has_size'] = $request->has('has_size') ? (bool) $request->has_size : false;
        $validated['has_sub_product'] = $request->has('has_sub_product') ? (bool) $request->has_sub_product : false;
        $validated['reorder_applicable'] = $request->has('reorder_applicable') ? (bool) $request->reorder_applicable : false;
        $validated['min_stock_level'] = $validated['min_stock_level'] ?? 0;

        DB::beginTransaction();

        try {
            $validated['created_by'] = auth()->id();

            if ($request->hasFile('image')) {
                $validated['image'] = ImageUploadService::upload(
                    $request->file('image'),
                    'products'
                );
            }

            $product = Product::create($validated);

            if ($product->has_size && $request->filled('size_ids')) {
                $product->sizes()->sync($request->size_ids);
            } else {
                $product->sizes()->detach();
            }

            if ($product->has_sub_product && $request->filled('sub_product_ids')) {
                $product->subProducts()->sync($request->sub_product_ids);
            } else {
                $product->subProducts()->detach();
            }

            ActivityLogService::log(
                'Product',
                'CREATED',
                $product->id,
                "Created Product '{$product->name}' ({$product->code})"
            );

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the product: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $uoms = Uom::where('status', 1)->orderBy('name')->get();
        $taxes = Tax::where('status', 1)->orderBy('name')->get();
        $sizes = Size::where('status', 1)->orderBy('name')->get();
        $subProducts = SubProduct::where('status', 1)->orderBy('name')->get();
        $product->load(['sizes', 'subProducts']);

        return view('products.edit', compact('product', 'categories', 'brands', 'uoms', 'taxes', 'sizes', 'subProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name' => 'required|string|max:150',
            'hsn_code' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'uom_id' => 'required|exists:uoms,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'tax_type' => ['required', Rule::in(['inclusive', 'exclusive'])],
            'sales_based_on' => ['required', Rule::in(['fixed', 'flexible'])],
            'purchase_based_on' => ['required', Rule::in(['fixed', 'flexible'])],
            'has_size' => 'nullable|boolean',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'has_sub_product' => 'nullable|boolean',
            'sub_product_ids' => 'nullable|array',
            'sub_product_ids.*' => 'exists:sub_products,id',
            'calculation_based_on' => ['required', Rule::in(['quantity', 'weight', 'sqft', 'dimension'])],
            'reorder_applicable' => 'nullable|boolean',
            'min_stock_level' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $validated['has_size'] = $request->has('has_size') ? (bool) $request->has_size : false;
        $validated['has_sub_product'] = $request->has('has_sub_product') ? (bool) $request->has_sub_product : false;
        $validated['reorder_applicable'] = $request->has('reorder_applicable') ? (bool) $request->reorder_applicable : false;
        $validated['min_stock_level'] = $validated['min_stock_level'] ?? 0;

        DB::beginTransaction();

        try {
            $validated['updated_by'] = auth()->id();

            if ($request->hasFile('image')) {
                $validated['image'] = ImageUploadService::update(
                    $request->file('image'),
                    $product->image,
                    'products'
                );
            }

            $product->update($validated);

            if ($product->has_size && $request->filled('size_ids')) {
                $product->sizes()->sync($request->size_ids);
            } else {
                $product->sizes()->detach();
            }

            if ($product->has_sub_product && $request->filled('sub_product_ids')) {
                $product->subProducts()->sync($request->sub_product_ids);
            } else {
                $product->subProducts()->detach();
            }

            ActivityLogService::log(
                'Product',
                'UPDATED',
                $product->id,
                "Updated Product '{$product->name}' ({$product->code})"
            );

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            $productName = $product->name;
            $productCode = $product->code;

            $product->delete();

            ActivityLogService::log(
                'Product',
                'DELETED',
                $product->id,
                "Deleted Product '{$productName}' ({$productCode})"
            );

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Something went wrong while deleting the product.');
        }
    }
}
