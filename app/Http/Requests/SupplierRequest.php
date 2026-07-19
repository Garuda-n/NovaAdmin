<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use App\Services\SettingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $supplierId = $this->route('supplier') ? $this->route('supplier')->id : null;
        $supplierScope = SettingService::get('supplier_scope', 'Global');
        $isBranchRequired = strcasecmp($supplierScope, 'Branch') === 0;

        $rules = [
            'supplier_name'    => ['required', 'string', 'max:255'],
            'supplier_code'    => ['nullable', 'string', 'max:50', Rule::unique('suppliers', 'supplier_code')->ignore($supplierId)],
            'contact_person'   => ['nullable', 'string', 'max:255'],
            'mobile'           => ['required', 'string', 'max:20', Rule::unique('suppliers', 'mobile')->ignore($supplierId)],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:255'],
            'supplier_type'    => ['required', Rule::in(Supplier::SUPPLIER_TYPES)],
            'gst_number'       => ['nullable', 'string', 'max:20'],
            'pan_number'       => ['nullable', 'string', 'max:20', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
            'address'          => ['nullable', 'string'],
            'country_id'       => ['required', 'exists:countries,id'],
            'state_id'         => ['required', 'exists:states,id'],
            'city_id'          => ['required', 'exists:cities,id'],
            'pincode'          => ['required', 'string', 'max:10'],
            'opening_balance'  => ['nullable', 'numeric', 'min:0'],
            'credit_limit'     => ['nullable', 'numeric', 'min:0'],
            'credit_days'      => ['nullable', 'integer', 'min:0'],
            'status'           => ['nullable', 'boolean'],
        ];

        // Branch requirement based on General Settings
        if ($isBranchRequired) {
            $rules['branch_id'] = ['required', 'exists:branches,id'];
        } else {
            $rules['branch_id'] = ['nullable'];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'supplier_name'    => 'supplier name',
            'supplier_code'    => 'supplier code',
            'contact_person'   => 'contact person',
            'supplier_type'    => 'supplier type',
            'gst_number'       => 'GST number',
            'pan_number'       => 'PAN number',
            'country_id'       => 'country',
            'state_id'         => 'state',
            'city_id'          => 'city',
            'branch_id'        => 'branch',
            'opening_balance'  => 'opening balance',
            'credit_limit'     => 'credit limit',
            'credit_days'      => 'credit days',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'pan_number.regex' => 'The PAN number format is invalid. Example format: ABCDE1234F',
        ];
    }
}
