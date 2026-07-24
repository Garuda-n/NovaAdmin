<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(15)->withQueryString();
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:companies,name',
            'code'  => 'required|string|max:20|unique:companies,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        DB::transaction(function () use ($request) {
            $company = Company::create([
                'name'       => $request->name,
                'code'       => strtoupper($request->code),
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'status'     => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            ActivityLogService::log(
                'Company',
                'CREATE',
                $company->id,
                'Created company: ' . $company->name
            );
        });
        return redirect()
            ->route('companies.index')
            ->with('success', 'Company created successfully.');
    }
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:companies,name,' . $company->id,
            'code'    => 'required|string|max:20|unique:companies,code,' . $company->id,
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $company) {

            $oldName = $company->name;

            $company->update([
                'name'       => $request->name,
                'code'       => strtoupper($request->code),
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'updated_by' => Auth::id(),
            ]);

            ActivityLogService::log(
                'Company',
                'UPDATE',
                $company->id,
                "Updated company from '{$oldName}' to '{$company->name}'"
            );
        });

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }
}
