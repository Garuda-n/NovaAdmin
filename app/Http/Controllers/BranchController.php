<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\State;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with(['company', 'country', 'state', 'city'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $companies = Company::where('status', 1)
            ->orderBy('name')
            ->get();
        $countries = Country::orderBy('name')->get();
        $defaultCountry = Country::where('is_default', true)->first() ?? $countries->first();
        $states = $defaultCountry ? State::where('country_id', $defaultCountry->id)->orderBy('name')->get() : collect();
        $defaultState = $states->where('is_default', true)->first() ?? $states->first();
        $cities = $defaultState ? City::where('state_id', $defaultState->id)->orderBy('name')->get() : collect();

        return view('branches.create', compact('companies', 'countries', 'defaultCountry', 'states', 'defaultState', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id'     => 'required|exists:companies,id',
            'name'           => 'required|max:255',
            'branch_code'    => 'required|max:100|unique:branches,branch_code',
            'gst_number'     => 'nullable|max:20',
            'phone'          => 'nullable|max:20',
            'email'          => 'nullable|email',
            'country_id'     => 'nullable|exists:countries,id',
            'state_id'       => 'nullable|exists:states,id',
            'city_id'        => 'nullable|exists:cities,id',
            'pincode'        => 'nullable|string|max:10',
            'address'        => 'nullable',
            'is_head_office' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {

            if ($request->boolean('is_head_office')) {
                Branch::where('company_id', $request->company_id)
                    ->update(['is_head_office' => false]);
            }

            $branch = Branch::create([
                'company_id'     => $request->company_id,
                'name'           => $request->name,
                'branch_code'    => $request->branch_code,
                'gst_number'     => $request->gst_number,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'country_id'     => $request->country_id,
                'state_id'       => $request->state_id,
                'city_id'        => $request->city_id,
                'pincode'        => $request->pincode,
                'address'        => $request->address,
                'is_head_office' => $request->boolean('is_head_office'),
                'status'         => true,
            ]);

            ActivityLogService::log(
                'Branch',
                'CREATE',
                $branch->id,
                'Created Branch : ' . $branch->name
            );

            DB::commit();

            return redirect()
                ->route('branches.index')
                ->with('success', 'Branch created successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public function edit(Branch $branch)
    {
        $companies = Company::where('status', 1)
            ->orderBy('name')
            ->get();
        $countries = Country::orderBy('name')->get();
        $states = $branch->country_id ? State::where('country_id', $branch->country_id)->orderBy('name')->get() : collect();
        $cities = $branch->state_id ? City::where('state_id', $branch->state_id)->orderBy('name')->get() : collect();

        return view('branches.edit', compact('branch', 'companies', 'countries', 'states', 'cities'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'company_id'      => 'required|exists:companies,id',
            'name'            => 'required|max:255',
            'branch_code'     => 'required|max:100|unique:branches,branch_code,' . $branch->id,
            'gst_number'      => 'nullable|max:20',
            'phone'           => 'nullable|max:20',
            'email'           => 'nullable|email',
            'country_id'      => 'nullable|exists:countries,id',
            'state_id'        => 'nullable|exists:states,id',
            'city_id'         => 'nullable|exists:cities,id',
            'pincode'         => 'nullable|string|max:10',
            'address'         => 'nullable',
            'is_head_office'  => 'nullable|boolean',
            'status'          => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            if ($request->boolean('is_head_office')) {
                Branch::where('company_id', $request->company_id)
                    ->where('id', '!=', $branch->id)
                    ->update(['is_head_office' => false]);
            }

            $branch->update([
                'company_id'      => $request->company_id,
                'name'            => $request->name,
                'branch_code'     => $request->branch_code,
                'gst_number'      => $request->gst_number,
                'phone'           => $request->phone,
                'email'           => $request->email,
                'country_id'      => $request->country_id,
                'state_id'        => $request->state_id,
                'city_id'         => $request->city_id,
                'pincode'         => $request->pincode,
                'address'         => $request->address,
                'is_head_office'  => $request->boolean('is_head_office'),
                'status'          => $request->status,
            ]);

            ActivityLogService::log(
                'Branch',
                'UPDATE',
                $branch->id,
                'Updated Branch : ' . $branch->name
            );

            DB::commit();

            return redirect()
                ->route('branches.index')
                ->with('success', 'Branch updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }
}