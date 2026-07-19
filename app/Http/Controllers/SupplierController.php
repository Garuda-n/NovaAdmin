<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Supplier;
use App\Services\ActivityLogService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::with(['country', 'state', 'city', 'branch']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%")
                  ->orWhere('pan_number', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('supplier_type')) {
            $query->where('supplier_type', $request->supplier_type);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $suppliers = $query->latest()->paginate(15)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('suppliers._table', compact('suppliers'))->render()
            ]);
        }

        $branches = Branch::where('status', true)->orderBy('name')->get();

        return view('suppliers.index', compact('suppliers', 'branches'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $defaultCountry = Country::where('is_default', true)->first() ?? $countries->first();
        $states = $defaultCountry ? State::where('country_id', $defaultCountry->id)->orderBy('name')->get() : collect();
        $defaultState = $states->where('is_default', true)->first() ?? $states->first();
        $cities = $defaultState ? City::where('state_id', $defaultState->id)->orderBy('name')->get() : collect();
        $branches = Branch::where('status', true)->orderBy('name')->get();
        $supplierScope = SettingService::get('supplier_scope', 'Global');

        return view('suppliers.create', compact(
            'countries',
            'states',
            'cities',
            'branches',
            'defaultCountry',
            'defaultState',
            'supplierScope'
        ));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(SupplierRequest $request)
    {
        $data = $request->validated();
        $supplierScope = SettingService::get('supplier_scope', 'Global');

        // If supplier scope is Global, force branch_id to null
        if (strcasecmp($supplierScope, 'Branch') !== 0) {
            $data['branch_id'] = null;
        }

        DB::transaction(function () use ($data) {
            $supplier = Supplier::create([
                'supplier_name'    => $data['supplier_name'],
                'supplier_code'    => $data['supplier_code'] ?? null,
                'contact_person'   => $data['contact_person'] ?? null,
                'mobile'           => $data['mobile'],
                'alternate_mobile' => $data['alternate_mobile'] ?? null,
                'email'            => $data['email'] ?? null,
                'supplier_type'    => $data['supplier_type'],
                'gst_number'       => !empty($data['gst_number']) ? strtoupper($data['gst_number']) : null,
                'pan_number'       => !empty($data['pan_number']) ? strtoupper($data['pan_number']) : null,
                'address'          => $data['address'] ?? null,
                'country_id'       => $data['country_id'],
                'state_id'         => $data['state_id'],
                'city_id'          => $data['city_id'],
                'pincode'          => $data['pincode'],
                'branch_id'        => $data['branch_id'] ?? null,
                'opening_balance'  => $data['opening_balance'] ?? 0.00,
                'credit_limit'     => $data['credit_limit'] ?? 0.00,
                'credit_days'      => $data['credit_days'] ?? 0,
                'created_through'  => 'Admin',
                'status'           => isset($data['status']) ? (bool) $data['status'] : true,
                'created_by'       => Auth::id(),
                'updated_by'       => Auth::id(),
            ]);

            ActivityLogService::log(
                'Supplier',
                'CREATE',
                $supplier->id,
                'Created Supplier : ' . $supplier->supplier_name
            );
        });

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Request $request, Supplier $supplier)
    {
        $supplier->load(['country', 'state', 'city', 'branch', 'creator', 'updater']);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('suppliers._modal_show', compact('supplier'))->render()
            ]);
        }

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', $supplier->country_id)->orderBy('name')->get();
        $cities = City::where('state_id', $supplier->state_id)->orderBy('name')->get();
        $branches = Branch::where('status', true)->orderBy('name')->get();
        $supplierScope = SettingService::get('supplier_scope', 'Global');

        return view('suppliers.edit', compact(
            'supplier',
            'countries',
            'states',
            'cities',
            'branches',
            'supplierScope'
        ));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $data = $request->validated();
        $supplierScope = SettingService::get('supplier_scope', 'Global');

        // If supplier scope is Global, force branch_id to null
        if (strcasecmp($supplierScope, 'Branch') !== 0) {
            $data['branch_id'] = null;
        }

        DB::transaction(function () use ($supplier, $data) {
            $oldName = $supplier->supplier_name;

            $supplier->update([
                'supplier_name'    => $data['supplier_name'],
                'supplier_code'    => $data['supplier_code'] ?? null,
                'contact_person'   => $data['contact_person'] ?? null,
                'mobile'           => $data['mobile'],
                'alternate_mobile' => $data['alternate_mobile'] ?? null,
                'email'            => $data['email'] ?? null,
                'supplier_type'    => $data['supplier_type'],
                'gst_number'       => !empty($data['gst_number']) ? strtoupper($data['gst_number']) : null,
                'pan_number'       => !empty($data['pan_number']) ? strtoupper($data['pan_number']) : null,
                'address'          => $data['address'] ?? null,
                'country_id'       => $data['country_id'],
                'state_id'         => $data['state_id'],
                'city_id'          => $data['city_id'],
                'pincode'          => $data['pincode'],
                'branch_id'        => $data['branch_id'] ?? null,
                'opening_balance'  => $data['opening_balance'] ?? 0.00,
                'credit_limit'     => $data['credit_limit'] ?? 0.00,
                'credit_days'      => $data['credit_days'] ?? 0,
                'status'           => isset($data['status']) ? (bool) $data['status'] : $supplier->status,
                'updated_by'       => Auth::id(),
            ]);

            ActivityLogService::log(
                'Supplier',
                'UPDATE',
                $supplier->id,
                "Updated Supplier from '{$oldName}' to '{$supplier->supplier_name}'"
            );
        });

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        DB::transaction(function () use ($supplier) {
            $supplierName = $supplier->supplier_name;
            $supplierId = $supplier->id;
            $supplier->delete();

            ActivityLogService::log(
                'Supplier',
                'DELETE',
                $supplierId,
                "Deleted Supplier: {$supplierName}"
            );
        });

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
