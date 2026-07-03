<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'attendance_id' => ['required', 'string', 'exists:attendances,id'],
            'reason' => ['required', 'string', 'min:10'],
        ];
    }
}
