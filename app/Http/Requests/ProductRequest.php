<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_start_at' => ['nullable', 'date'],
            'discount_end_at' => ['nullable', 'date', 'after_or_equal:discount_start_at'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'string', 'max:2048'],
            'variants'    => ['nullable', 'array'],
            'variants.*.key' => ['required_with:variants', 'string', 'max:120'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:120'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'variants.*.discount_start_at' => ['nullable', 'date'],
            'variants.*.discount_end_at' => ['nullable', 'date'],
        ];
    }
}
