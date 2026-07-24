<?php

namespace App\Http\Requests;

use App\Enums\EmployeeStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code'  => ['nullable', 'string', 'max:50', 'unique:employees,employee_code'],
            'domain_account' => ['nullable', 'string', 'max:100', 'unique:employees,domain_account'],
            'name'           => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'department'    => ['required', 'string', 'max:100'],
            'position'      => ['required', 'string', 'max:100'],
            'status'        => ['required', Rule::enum(EmployeeStatus::class)],
            'notes'         => ['nullable', 'string'],
        ];
    }
}
