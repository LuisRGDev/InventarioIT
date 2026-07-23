<?php

namespace App\Http\Requests;

use App\Enums\DeviceCondition;
use App\Enums\DeviceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReturnDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condition_on_return' => ['required', Rule::enum(DeviceCondition::class)],
            'new_status'          => ['required', Rule::in([
                DeviceStatus::Disponible->value,
                DeviceStatus::EnReparacion->value,
                DeviceStatus::Obsoleto->value,
                DeviceStatus::Baja->value,
            ])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
