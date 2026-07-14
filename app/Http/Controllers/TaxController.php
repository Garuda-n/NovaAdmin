<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::latest()->get();

        return view('taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:taxes,name',
            'percentage' => 'required|numeric|min:0|max:100',
            'status'     => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $lastTax = Tax::latest('id')->first();

            $nextNumber = $lastTax
                ? ((int) substr($lastTax->code, 3)) + 1
                : 1;

            $validated['code'] = 'TAX' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $validated['created_by'] = auth()->id();

            $tax = Tax::create($validated);

            ActivityLogService::log(
                'Tax',
                'CREATED',
                $tax->id,
                "Created Tax '{$tax->name}'"
            );

            DB::commit();

            return redirect()
                ->route('taxes.index')
                ->with('success', 'Tax created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('taxes', 'name')->ignore($tax->id),
            ],
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $validated['updated_by'] = auth()->id();

            $tax->update($validated);

            ActivityLogService::log(
                'Tax',
                'UPDATED',
                $tax->id,
                "Updated Tax '{$tax->name}'"
            );

            DB::commit();

            return redirect()
                ->route('taxes.index')
                ->with('success', 'Tax updated successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the Tax.');
        }
    }
}