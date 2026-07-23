<?php

namespace App\Http\Requests;

use App\Enums\DeviceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_category_id' => ['required', 'exists:device_categories,id'],
            'serial_number'      => ['required', 'string', 'max:100', 'unique:devices,serial_number'],
            'mac_address'        => ['nullable', 'string', 'max:17', 'unique:devices,mac_address'],
            'brand'              => ['required', 'string', 'max:100'],
            'model'              => ['required', 'string', 'max:100'],
            'status'             => ['required', Rule::enum(DeviceStatus::class)],
            'purchase_date'      => ['nullable', 'date'],
            'warranty_expires_at' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'specs'              => ['nullable', 'array'],
            'notes'              => ['nullable', 'string'],
        ];
    }
}
