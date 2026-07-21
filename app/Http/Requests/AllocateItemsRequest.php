<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllocateItemsRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stock_inward_item_id' => 'required|integer|exists:stock_inward_items,id',
            'counter_id' => 'required|integer|exists:counters,id',
            'quantity' => 'required|integer|min:1',
            'size_id' => 'nullable|integer|exists:sizes,id',
            'sub_product_id' => 'nullable|integer|exists:sub_products,id',
        ];
    }

    /**
     * Custom message definitions.
     */
    public function messages(): array
    {
        return [
            'counter_id.required' => 'Counter selection is mandatory for allocation.',
            'quantity.required' => 'Please specify the quantity to allocate.',
            'quantity.min' => 'Allocation quantity must be at least 1.',
        ];
    }
}
