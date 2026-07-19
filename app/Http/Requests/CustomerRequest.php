<?php

namespace App\Http\Requests;

use App\Services\CustomerService;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;
        return CustomerService::getValidationRules($customerId, $this->all());
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_name' => 'customer name',
            'customer_type' => 'customer type',
            'gst_number'    => 'GST number',
            'country_id'    => 'country',
            'state_id'      => 'state',
            'city_id'       => 'city',
            'branch_id'     => 'branch',
            'credit_limit'  => 'credit limit',
            'credit_days'   => 'credit days',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'gst_number.regex' => 'The GST number format is invalid. Example format: 22AAAAA0000A1Z5',
        ];
    }
}
