<?php

namespace App\Services;

use App\Models\Customer;
use App\Services\ActivityLogService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerService
{
    /**
     * Check if system setting enforces branch-wise customers.
     */
    public static function isBranchWiseCustomer(): bool
    {
        $branchWiseSetting = SettingService::get('branch_wise_customer', null);
        if ($branchWiseSetting !== null) {
            return in_array(strtolower((string) $branchWiseSetting), ['1', 'true', 'yes', 'branch']);
        }

        $customerScope = SettingService::get('customer_scope', 'Global');
        return strcasecmp($customerScope, 'Branch') === 0;
    }

    /**
     * Get validation rules for customer creation or update.
     */
    public static function getValidationRules(?int $customerId = null, array $input = []): array
    {
        $isBranchRequired = static::isBranchWiseCustomer();
        $customerType = $input['customer_type'] ?? 'B2C';
        $isB2B = strtoupper($customerType) === 'B2B';

        $rules = [
            'customer_name'    => ['required', 'string', 'max:255'],
            'mobile'           => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->ignore($customerId)],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:255'],
            'customer_type'    => ['required', Rule::in(['B2C', 'B2B'])],
            'address'          => ['nullable', 'string'],
            'country_id'       => ['required', 'exists:countries,id'],
            'state_id'         => ['required', 'exists:states,id'],
            'city_id'          => ['required', 'exists:cities,id'],
            'pincode'          => ['required', 'string', 'max:10'],
            'credit_limit'     => ['nullable', 'numeric', 'min:0'],
            'credit_days'      => ['nullable', 'integer', 'min:0'],
            'status'           => ['nullable', 'boolean'],
        ];

        // Branch requirement based on settings
        if ($isBranchRequired) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        } else {
            $rules['branch_id'] = ['nullable'];
        }

        // GST Number requirement based on Customer Type (B2B vs B2C)
        if ($isB2B) {
            $rules['gst_number'] = [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i'
            ];
        } else {
            $rules['gst_number'] = ['nullable', 'string', 'max:20'];
        }

        return $rules;
    }

    /**
     * Create a new customer record.
     */
    public function createCustomer(array $data, string $createdThrough = 'Admin', ?int $userId = null): Customer
    {
        // If branch_wise_customer is 0 (Global mode), force branch_id to null
        if (!static::isBranchWiseCustomer()) {
            $data['branch_id'] = null;
        }

        $userId = $userId ?? Auth::id();

        return DB::transaction(function () use ($data, $createdThrough, $userId) {
            $customer = Customer::create([
                'customer_name'    => $data['customer_name'],
                'mobile'           => $data['mobile'],
                'alternate_mobile' => $data['alternate_mobile'] ?? null,
                'email'            => $data['email'] ?? null,
                'customer_type'    => $data['customer_type'] ?? 'B2C',
                'gst_number'       => !empty($data['gst_number']) ? strtoupper($data['gst_number']) : null,
                'address'          => $data['address'] ?? null,
                'country_id'       => $data['country_id'],
                'state_id'         => $data['state_id'],
                'city_id'          => $data['city_id'],
                'pincode'          => $data['pincode'],
                'branch_id'        => $data['branch_id'] ?? null,
                'credit_limit'     => $data['credit_limit'] ?? 0.00,
                'credit_days'      => $data['credit_days'] ?? 0,
                'created_through'  => $createdThrough,
                'status'           => isset($data['status']) ? (bool) $data['status'] : true,
                'created_by'       => $userId,
                'updated_by'       => $userId,
            ]);

            ActivityLogService::log(
                'Customer',
                'CREATE',
                $customer->id,
                'Created Customer : ' . $customer->customer_name
            );

            return $customer;
        });
    }

    /**
     * Update an existing customer record.
     */
    public function updateCustomer(Customer $customer, array $data, ?int $userId = null): Customer
    {
        // If branch_wise_customer is 0 (Global mode), force branch_id to null
        if (!static::isBranchWiseCustomer()) {
            $data['branch_id'] = null;
        }

        $userId = $userId ?? Auth::id();

        return DB::transaction(function () use ($customer, $data, $userId) {
            $oldName = $customer->customer_name;

            $customer->update([
                'customer_name'    => $data['customer_name'],
                'mobile'           => $data['mobile'],
                'alternate_mobile' => $data['alternate_mobile'] ?? null,
                'email'            => $data['email'] ?? null,
                'customer_type'    => $data['customer_type'] ?? 'B2C',
                'gst_number'       => !empty($data['gst_number']) ? strtoupper($data['gst_number']) : null,
                'address'          => $data['address'] ?? null,
                'country_id'       => $data['country_id'],
                'state_id'         => $data['state_id'],
                'city_id'          => $data['city_id'],
                'pincode'          => $data['pincode'],
                'branch_id'        => $data['branch_id'] ?? null,
                'credit_limit'     => $data['credit_limit'] ?? 0.00,
                'credit_days'      => $data['credit_days'] ?? 0,
                'status'           => isset($data['status']) ? (bool) $data['status'] : $customer->status,
                'updated_by'       => $userId,
            ]);

            ActivityLogService::log(
                'Customer',
                'UPDATE',
                $customer->id,
                "Updated Customer from '{$oldName}' to '{$customer->customer_name}'"
            );

            return $customer;
        });
    }
}
