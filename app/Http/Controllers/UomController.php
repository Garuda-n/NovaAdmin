<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $uoms = Uom::latest()->paginate(15)->withQueryString();
        return view('uoms.index', compact('uoms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('uoms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:uoms,name',
            'shortcode' => 'required|string|max:20|unique:uoms,shortcode',
            'has_decimals' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $uom = Uom::create($validated);

            ActivityLogService::log(
                'UOM',
                'Created',
                $uom->id,
                "Created UOM '{$uom->name}'"
            );

            DB::commit();

            return redirect()
                ->route('uoms.index')
                ->with('success', 'UOM created successfully.');

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
    public function show(Uom $uom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Uom $uom)
    {
        return view('uoms.edit', compact('uom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Uom $uom)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('uoms', 'name')->ignore($uom->id),
            ],
            'shortcode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('uoms', 'shortcode')->ignore($uom->id),
            ],
            'has_decimals' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $uom->update($validated);

            ActivityLogService::log(
                'UOM',
                'UPDATE',
                $uom->id,
                "Updated UOM '{$uom->name}'"
            );

            DB::commit();

            return redirect()
                ->route('uoms.index')
                ->with('success', 'UOM updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the UOM.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Uom $uom)
    {
        //
    }
}
