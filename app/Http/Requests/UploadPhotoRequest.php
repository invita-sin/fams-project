<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'media_file_id' => ['required', 'string', 'exists:media_files,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
