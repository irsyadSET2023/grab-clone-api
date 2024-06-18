<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignCategoriesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Update this to check if the user is authorized to assign categories
        return true; // Adjust this as necessary
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'categories' => 'sometimes|array',
            'categories.*' => 'integer|exists:landlord.categories,id',
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'categories.required' => 'The categories field is required.',
            'categories.array' => 'The categories field must be an array.',
            'categories.*.integer' => 'Each category ID must be an integer.',
            'categories.*.exists' => 'Each category ID must exist in the categories table.',
        ];
    }
}
