<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerApiController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Get paginated customer list or search customers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with(['country', 'state', 'city', 'branch'])->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $customers = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => true,
            'data'   => $customers
        ]);
    }

    /**
     * Store a new customer via API (e.g., POS, Mobile App, Website).
     */
    public function store(Request $request): JsonResponse
    {
        $rules = CustomerService::getValidationRules(null, $request->all());
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $createdThrough = $request->header('X-Source-App', $request->input('source', 'API'));
        $customer = $this->customerService->createCustomer($validator->validated(), $createdThrough);
        $customer->load(['country', 'state', 'city', 'branch']);

        return response()->json([
            'status'  => true,
            'message' => 'Customer created successfully.',
            'data'    => $customer
        ], 201);
    }

    /**
     * Show a single customer.
     */
    public function show(Customer $customer): JsonResponse
    {
        $customer->load(['country', 'state', 'city', 'branch']);

        return response()->json([
            'status' => true,
            'data'   => $customer
        ]);
    }

    /**
     * Update customer via API.
     */
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $rules = CustomerService::getValidationRules($customer->id, $request->all());
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updatedCustomer = $this->customerService->updateCustomer($customer, $validator->validated());
        $updatedCustomer->load(['country', 'state', 'city', 'branch']);

        return response()->json([
            'status'  => true,
            'message' => 'Customer updated successfully.',
            'data'    => $updatedCustomer
        ]);
    }
}
