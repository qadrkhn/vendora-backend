<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
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
            'file' => 'required_without:files|file|max:5120',
            'files.*' => 'required_without:file|file|max:5120',

            // Require both if one is set
            'fileable_type' => 'required_with:fileable_id|string|in:category',
            'fileable_id' => 'required_with:fileable_type|integer',
        ];
    }
}
