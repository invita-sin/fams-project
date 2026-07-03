<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'branch_id' => ['nullable', 'string', 'exists:branches,id'],
            'address' => ['nullable', 'string'],
            'radius_meter' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'active' => ['nullable', 'boolean'],
        ];
    }
}
