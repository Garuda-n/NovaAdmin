<?php

namespace App\Http\Controllers;

use App\Models\SubProduct;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subProducts = SubProduct::latest()->get();
        return view('sub_products.index', compact('subProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sub_products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:sub_products,code',
            'name' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['created_by'] = auth()->id();

            $subProduct = SubProduct::create($validated);

            ActivityLogService::log(
                'SubProduct',
                'CREATED',
                $subProduct->id,
                "Created Sub Product '{$subProduct->name}' ({$subProduct->code})"
            );

            DB::commit();

            return redirect()
                ->route('sub-products.index')
                ->with('success', 'Sub Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the sub product.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubProduct $subProduct)
    {
        return view('sub_products.edit', compact('subProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubProduct $subProduct)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sub_products', 'code')->ignore($subProduct->id),
            ],
            'name' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['updated_by'] = auth()->id();

            $subProduct->update($validated);

            ActivityLogService::log(
                'SubProduct',
                'UPDATED',
                $subProduct->id,
                "Updated Sub Product '{$subProduct->name}' ({$subProduct->code})"
            );

            DB::commit();

            return redirect()
                ->route('sub-products.index')
                ->with('success', 'Sub Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the sub product.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubProduct $subProduct)
    {
        DB::beginTransaction();

        try {
            $name = $subProduct->name;
            $code = $subProduct->code;

            $subProduct->delete();

            ActivityLogService::log(
                'SubProduct',
                'DELETED',
                $subProduct->id,
                "Deleted Sub Product '{$name}' ({$code})"
            );

            DB::commit();

            return redirect()
                ->route('sub-products.index')
                ->with('success', 'Sub Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Something went wrong while deleting the sub product.');
        }
    }
}
