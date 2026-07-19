<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\State;
use App\Services\ActivityLogService;
use App\Services\CustomerService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $query = Customer::with(['country', 'state', 'city', 'branch']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('customers._table', compact('customers'))->render()
            ]);
        }

        $branches = Branch::where('status', true)->orderBy('name')->get();

        return view('customers.index', compact('customers', 'branches'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $defaultCountry = Country::where('is_default', true)->first() ?? $countries->first();
        $states = $defaultCountry ? State::where('country_id', $defaultCountry->id)->orderBy('name')->get() : collect();
        $defaultState = $states->where('is_default', true)->first() ?? $states->first();
        $cities = $defaultState ? City::where('state_id', $defaultState->id)->orderBy('name')->get() : collect();
        $branches = Branch::where('status', true)->orderBy('name')->get();
        $customerScope = SettingService::get('customer_scope', 'Global');

        return view('customers.create', compact(
            'countries',
            'states',
            'cities',
            'branches',
            'defaultCountry',
            'defaultState',
            'customerScope'
        ));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerRequest $request)
    {
        $this->customerService->createCustomer($request->validated(), 'Admin');

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Request $request, Customer $customer)
    {
        $customer->load(['country', 'state', 'city', 'branch', 'creator', 'updater']);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('customers._modal_show', compact('customer'))->render()
            ]);
        }

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', $customer->country_id)->orderBy('name')->get();
        $cities = City::where('state_id', $customer->state_id)->orderBy('name')->get();
        $branches = Branch::where('status', true)->orderBy('name')->get();
        $customerScope = SettingService::get('customer_scope', 'Global');

        return view('customers.edit', compact(
            'customer',
            'countries',
            'states',
            'cities',
            'branches',
            'customerScope'
        ));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $this->customerService->updateCustomer($customer, $request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            $customerName = $customer->customer_name;
            $customerId = $customer->id;
            $customer->delete();

            ActivityLogService::log(
                'Customer',
                'DELETE',
                $customerId,
                "Deleted Customer: {$customerName}"
            );
        });

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
