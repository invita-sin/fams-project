<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
{
    public function rules(): array
    {
        $employeeId = $this->route('id');

        return [
            'employee_code' => [
                'required', 'string', 'max:50',
                'unique:employees,employee_code' . ($employeeId ? ',' . $employeeId : ''),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'string', 'in:male,female'],
            'branch_id' => ['nullable', 'string', 'exists:branches,id'],
            'division_id' => ['nullable', 'string', 'exists:divisions,id'],
            'department_id' => ['nullable', 'string', 'exists:departments,id'],
            'position_id' => ['nullable', 'string', 'exists:positions,id'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
