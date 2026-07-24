<?php

namespace App\Http\Requests;

use App\Enums\EmployeeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('employee');

        return [
            'employee_code'  => ['nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($employeeId)],
            'domain_account' => ['nullable', 'string', 'max:100', Rule::unique('employees', 'domain_account')->ignore($employeeId)],
            'name'           => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employeeId)],
            'phone'         => ['nullable', 'string', 'max:30'],
            'department'    => ['required', 'string', 'max:100'],
            'position'      => ['required', 'string', 'max:100'],
            'status'        => ['required', Rule::enum(EmployeeStatus::class)],
            'notes'         => ['nullable', 'string'],
        ];
    }
}
