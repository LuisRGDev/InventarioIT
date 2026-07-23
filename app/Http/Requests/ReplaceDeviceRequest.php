<?php

namespace App\Http\Requests;

use App\Enums\DeviceCondition;
use App\Enums\DeviceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReplaceDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_device_id'        => ['required', 'exists:devices,id', 'different:new_device_id'],
            'new_device_id'        => ['required', 'exists:devices,id'],
            'employee_id'          => ['required', 'exists:employees,id'],
            'condition_on_return'  => ['required', Rule::enum(DeviceCondition::class)],
            'condition_on_delivery' => ['required', Rule::enum(DeviceCondition::class)],
            'old_device_new_status' => ['nullable', Rule::in([
                DeviceStatus::Disponible->value,
                DeviceStatus::EnReparacion->value,
                DeviceStatus::Obsoleto->value,
                DeviceStatus::Baja->value,
            ])],
            'return_notes'  => ['nullable', 'string'],
            'assign_notes'  => ['nullable', 'string'],
        ];
    }
}
