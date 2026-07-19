<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = Size::latest()->get();
        return view('sizes.index', compact('sizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sizes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:sizes,code',
            'name' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['created_by'] = auth()->id();

            $size = Size::create($validated);

            ActivityLogService::log(
                'Size',
                'CREATED',
                $size->id,
                "Created Size '{$size->name}' ({$size->code})"
            );

            DB::commit();

            return redirect()
                ->route('sizes.index')
                ->with('success', 'Size created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the size.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Size $size)
    {
        return view('sizes.edit', compact('size'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sizes', 'code')->ignore($size->id),
            ],
            'name' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['updated_by'] = auth()->id();

            $size->update($validated);

            ActivityLogService::log(
                'Size',
                'UPDATED',
                $size->id,
                "Updated Size '{$size->name}' ({$size->code})"
            );

            DB::commit();

            return redirect()
                ->route('sizes.index')
                ->with('success', 'Size updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the size.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        DB::beginTransaction();

        try {
            $sizeName = $size->name;
            $sizeCode = $size->code;

            $size->delete();

            ActivityLogService::log(
                'Size',
                'DELETED',
                $size->id,
                "Deleted Size '{$sizeName}' ({$sizeCode})"
            );

            DB::commit();

            return redirect()
                ->route('sizes.index')
                ->with('success', 'Size deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Something went wrong while deleting the size.');
        }
    }
}
