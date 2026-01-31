<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        'name' => ['required', 'array'],
        'name.*' => ['nullable', 'string', 'max:255'],
        'name' => function ($attribute, $value, $fail) {
            // Проверяем, что хотя бы одно название не пустое
            $filled = false;
            foreach ($value as $locale => $name) {
                if (!empty($name)) {
                    $filled = true;
                    break;
                }
            }
            if (!$filled) {
                $fail('Please enter at least one product name.');
            }
        },

        'category' => ['required', 'exists:categories,id'],
            'moq' => ['nullable', 'integer', 'min:1'],
            'lead_time' => ['nullable', 'integer', 'min:1'],

            'images.*' => ['image', 'max:5120'],

            'price_tiers.*.min_qty' => ['required', 'integer', 'min:1'],
            'price_tiers.*.max_qty' => ['nullable', 'integer', 'gt:price_tiers.*.min_qty'],
            'price_tiers.*.price' => ['required', 'numeric', 'min:0'],

            'shipping_templates.*' => ['exists:shipping_templates,id'],
            
    ];
}

public function attributes(): array
    {
        return [
            'name' => 'Product Name',
            'name.*' => 'Product Name',
            'category' => 'Category',
            'moq' => 'Minimum Order Quantity',
            'lead_time' => 'Lead Time',
            'images.*' => 'Product Image',
            'price_tiers.*.min_qty' => 'Minimum Quantity',
            'price_tiers.*.max_qty' => 'Maximum Quantity',
            'price_tiers.*.price' => 'Unit Price',
            'shipping_templates.*' => 'Shipping Template',
        ];
    }

public function messages(): array
    {
        return [
            'category.required' => 'Please select a category for the product.',
            'category.exists' => 'The selected category does not exist.',
            'moq.integer' => 'MOQ must be a number.',
            'moq.min' => 'MOQ must be at least 1.',
            'lead_time.integer' => 'Lead time must be a number.',
            'lead_time.min' => 'Lead time must be at least 1 day.',
            'images.*.image' => 'Each uploaded file must be a valid image.',
            'images.*.max' => 'Each image must be smaller than 5 MB.',
            'price_tiers.*.min_qty.required' => 'Please enter a minimum quantity for this price tier.',
            'price_tiers.*.max_qty.gt' => 'Maximum quantity must be greater than minimum quantity.',
            'price_tiers.*.price.required' => 'Please enter a price for this quantity tier.',
            'price_tiers.*.price.numeric' => 'Price must be a number.',
            'shipping_templates.*.exists' => 'Selected shipping template does not exist.',
            'country_of_origin.required' => 'Please select a country of origin for the product.',
        ];
    }
}
