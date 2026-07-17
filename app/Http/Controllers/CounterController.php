<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\BranchCounter;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $counters = Counter::with('branches')->latest()->get();
        $branches = Branch::where('status', 1)->get();
        return view('counters.index', compact('counters', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('counters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'counter_name' => 'required|string|max:100|unique:counters,counter_name',
            'counter_code' => 'required|string|max:20|unique:counters,counter_code',
            'status' => 'required|boolean',
        ]);

        Counter::create([
            'counter_name' => $request->counter_name,
            'counter_code' => strtoupper($request->counter_code),
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('counters.index')
            ->with('success', 'Counter created successfully.');
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
    public function edit(string $id)
    {
        $counter = Counter::findOrFail($id);
        return view('counters.edit', compact('counter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'counter_name' => 'required|string|max:100|unique:counters,counter_name,' . $id,
            'counter_code' => 'required|string|max:20|unique:counters,counter_code,' . $id,
            'status' => 'required|boolean',
        ]);

        $counter = Counter::findOrFail($id);

        $counter->update([
            'counter_name' => $request->counter_name,
            'counter_code' => strtoupper($request->counter_code),
            'status' => $request->status,
        ]);

        return redirect()
            ->route('counters.index')
            ->with('success', 'Counter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
   public function saveBranchMapping(Request $request, Counter $counter)
    {
        $request->validate([
            'branch_ids' => 'required|array',
        ]);
        foreach ($request->branch_ids as $branchId) {
            BranchCounter::updateOrCreate(
                [
                    'branch_id' => $branchId,
                    'counter_id' => $counter->id,
                ],
                [
                    'status' => true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );
        }
        return redirect()
            ->route('counters.index')
            ->with('success', 'Branch mapping saved successfully.');
    }
}
