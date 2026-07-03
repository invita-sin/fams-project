<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'checkin_start' => ['nullable', 'string', 'date_format:H:i:s'],
            'checkin_end' => ['nullable', 'string', 'date_format:H:i:s'],
            'checkout_time' => ['nullable', 'string', 'date_format:H:i:s'],
        ];
    }
}
