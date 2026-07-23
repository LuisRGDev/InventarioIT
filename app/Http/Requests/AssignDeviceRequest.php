<?php

namespace App\Http\Requests;

use App\Enums\DeviceCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id'             => ['required', 'exists:devices,id'],
            'employee_id'           => ['required', 'exists:employees,id'],
            'condition_on_delivery' => ['required', Rule::enum(DeviceCondition::class)],
            'notes'                 => ['nullable', 'string'],
        ];
    }
}
