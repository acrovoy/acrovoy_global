<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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

            'category' => ['required', 'exists:categories,id'],
            'country_id' => ['required', 'exists:countries,id'],

            'moq' => ['nullable', 'integer', 'min:1'],
            'lead_time' => ['nullable', 'integer', 'min:1'],

            'price_tiers' => ['required', 'array', 'min:1'],
            'price_tiers.*.min_qty' => ['required', 'integer', 'min:1'],
            'price_tiers.*.price' => ['required', 'numeric', 'min:0'],

            'materials' => ['array'],
            'images.*' => 'image|mimes:jpeg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Enter at least one product name',
            'price_tiers.required' => 'Add at least one price tier',
            'price_tiers.*.price.required' => 'Price is required',
        ];
    }
}
