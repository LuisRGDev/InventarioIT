<?php

namespace App\Http\Requests;

use App\Enums\DeviceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deviceId = $this->route('device')?->id ?? $this->route('device');

        return [
            'device_category_id'  => ['required', 'exists:device_categories,id'],
            'serial_number'       => ['required', 'string', 'max:100', Rule::unique('devices', 'serial_number')->ignore($deviceId)],
            'mac_address'         => ['nullable', 'string', 'max:17', Rule::unique('devices', 'mac_address')->ignore($deviceId)],
            'brand'               => ['required', 'string', 'max:100'],
            'model'               => ['required', 'string', 'max:100'],
            'status'              => ['required', Rule::enum(DeviceStatus::class)],
            'purchase_date'       => ['nullable', 'date'],
            'warranty_expires_at' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'specs'               => ['nullable', 'array'],
            'notes'               => ['nullable', 'string'],
        ];
    }
}
