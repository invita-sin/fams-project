<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', 'string', 'exists:attendance_schedules,id'],
            'effective_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:effective_date'],
        ];
    }
}
