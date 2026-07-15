<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\ActivityLogService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:brands,name',
            'code' => 'required|string|max:20|unique:brands,code',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $validated['created_by'] = auth()->id();

            if ($request->hasFile('logo')) {
                $validated['logo'] = ImageUploadService::upload(
                    $request->file('logo'),
                    'brands'
                );
            }

            $brand = Brand::create($validated);

            ActivityLogService::log(
                'Brand',
                'CREATED',
                $brand->id,
                "Created Brand '{$brand->name}'"
            );

            DB::commit();

            return redirect()
                ->route('brands.index')
                ->with('success', 'Brand created successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the brand.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('brands', 'name')->ignore($brand->id),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('brands', 'code')->ignore($brand->id),
            ],
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $validated['updated_by'] = auth()->id();

            $validated['logo'] = ImageUploadService::update(
                $request->file('logo'),
                $brand->logo,
                'brands'
            );

            $brand->update($validated);

            ActivityLogService::log(
                'Brand',
                'UPDATED',
                $brand->id,
                "Updated Brand '{$brand->name}'"
            );

            DB::commit();

            return redirect()
                ->route('brands.index')
                ->with('success', 'Brand updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the brand.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
