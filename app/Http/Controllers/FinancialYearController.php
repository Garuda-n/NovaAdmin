<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    public function index()
    {
        $financialYears = FinancialYear::latest()->get();
        return view('financial-years.index', compact('financialYears'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|boolean',
        ]);

        // Prevent overlapping Financial Years
        $overlap = FinancialYear::where(function ($query) use ($validated) {
            $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                ->orWhere(function ($q) use ($validated) {
                    $q->where('start_date', '<=', $validated['start_date'])
                        ->where('end_date', '>=', $validated['end_date']);
                });
        })->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'The selected financial year overlaps with an existing financial year.'
                ]);
        }

        DB::beginTransaction();

        try {

            $startYear = date('Y', strtotime($validated['start_date']));
            $endYear   = date('y', strtotime($validated['end_date']));

            $name = "FY {$startYear}-{$endYear}";
            $shortCode = "FY" . substr($startYear, 2) . $endYear;

            $isFirstFinancialYear = FinancialYear::count() === 0;

            $financialYear = FinancialYear::create([
                'name'        => $name,
                'short_code'  => $shortCode,
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'],
                'status'      => $validated['status'],
                'is_current'  => $isFirstFinancialYear,
                'created_by'  => auth()->id(),
            ]);

            ActivityLogService::log(
                'Financial Year',
                'Create',
                $financialYear->id,
                "Created Financial Year '{$financialYear->name}'"
            );

            DB::commit();

            return redirect()
                ->route('financial-years.index')
                ->with('success', 'Financial Year created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the Financial Year.');
        }
    }
    public function create()
    {
        return view('financial-years.create');
    }
    public function edit(FinancialYear $financialYear)
    {
        return view('financial-years.edit', compact('financialYear'));
    }
    public function update(Request $request, FinancialYear $financialYear)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|boolean',
        ]);

        // Prevent overlapping Financial Years except current record
        $overlap = FinancialYear::where('id', '!=', $financialYear->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                        ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'The selected financial year overlaps with an existing financial year.'
                ]);
        }

        DB::beginTransaction();

        try {

            $startYear = date('Y', strtotime($validated['start_date']));
            $endYear   = date('y', strtotime($validated['end_date']));

            $financialYear->update([
                'name'       => "FY {$startYear}-{$endYear}",
                'short_code' => "FY" . substr($startYear, 2) . $endYear,
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'],
                'status'     => $validated['status'],
                'updated_by' => auth()->id(),
            ]);

            ActivityLogService::log(
                'Financial Year',
                'Update',
                $financialYear->id,
                "Updated Financial Year '{$financialYear->name}'"
            );

            DB::commit();

            return redirect()
                ->route('financial-years.index')
                ->with('success', 'Financial Year updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the Financial Year.');
        }
    }
}
