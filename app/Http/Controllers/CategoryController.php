<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tax;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\ImageUploadService;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('tax')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $taxes = Tax::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('taxes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'code' => 'required|string|max:20|unique:categories,code',
            'tax_id' => 'nullable|exists:taxes,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $validated['created_by'] = auth()->id();
            if ($request->hasFile('image')) {
                $validated['image'] = ImageUploadService::upload(
                    $request->file('image'),
                    'categories'
                );
            }

            $category = Category::create($validated);

            ActivityLogService::log(
                'Category',
                'CREATED',
                $category->id,
                "Created Category '{$category->name}'"
            );

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category created successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $taxes = Tax::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('categories', 'code')->ignore($category->id),
            ],
            'tax_id' => 'nullable|exists:taxes,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $validated['updated_by'] = auth()->id();
            $validated['image'] = ImageUploadService::update(
                $request->file('image'),
                $category->image,
                'categories'
            );

            $category->update($validated);

            ActivityLogService::log(
                'Category',
                'UPDATED',
                $category->id,
                "Updated Category '{$category->name}'"
            );

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}