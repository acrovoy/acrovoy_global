<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class StoreRFQRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);

                    if (!$category) {
                        return;
                    }

                    // Проверяем, что это RFQ категория
                    if ($category->type !== 'project') {
                        $fail("This category is for custom project requests (RFQ). Please choose an RFQ category.");
                    }
                }
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'quantity' => ['required', 'integer', 'min:1'],
            'lead_time' => ['nullable', 'integer', 'min:1'],
            'attachments.*' => ['file', 'max:10240'], // файлы до 10MB
        ];
    }

    public function attributes(): array
    {
        return [
            'category' => 'Category',
            'description' => 'Project Description',
            'quantity' => 'Quantity',
            'lead_time' => 'Lead Time',
            'attachments.*' => 'Attachment',
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Please select a valid RFQ category. Product categories cannot be chosen here.',
            'category.exists' => 'The selected category does not exist.',
            'description.required' => 'Please provide a description for the project.',
            'quantity.required' => 'Please enter the quantity.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.max' => 'Each attachment must be smaller than 10 MB.',
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        info('RFQ category id: ' . $this->category);
    });
}

}

