<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
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
        return [
            'featured' => 'boolean',
            'name' => 'required|string|max:255|unique:categories',
            'url' => 'required|unique:categories',
            'file' => 'required|array|size:1',
            'file.0.id' => 'required|integer|exists:files,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a valid string.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'name.unique' => 'This category name is already taken.',

            'url.required' => 'The category URL is required.',
            'url.unique' => 'This category URL is already taken.',

            'featured.boolean' => 'The featured field must be true or false.',

            'file.required' => 'A category image must be uploaded.',
            'file.array' => 'The file must be an array.',
            'file.size' => 'You must upload exactly one image.',

            'file.0.id.required' => 'The image ID is required.',
            'file.0.id.integer' => 'The image ID must be a valid number.',
            'file.0.id.exists' => 'The selected image does not exist in our records.',
        ];
    }
}
