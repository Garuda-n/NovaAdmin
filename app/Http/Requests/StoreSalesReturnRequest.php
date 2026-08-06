<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('sales-returns.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sales_id' => ['required', 'integer', 'exists:sales,id'],
            'return_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_mode_id' => ['nullable', 'integer', 'exists:payment_modes,id'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sales_detail_id' => ['required', 'integer', 'exists:sales_details,id'],
            'items.*.returned_quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
