<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'  => ['required_without:store_id', 'nullable', 'exists:products,id'],
            'quantity'    => ['required_with:product_id', 'integer', 'min:1'],
            'store_id'    => ['sometimes', 'nullable', 'exists:stores,id'],
            'total_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
