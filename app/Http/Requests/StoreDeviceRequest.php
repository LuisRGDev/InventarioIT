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
            'device_category_id'  => ['required', 'exists:device_categories,id'],
            'serial_number'       => ['required', 'string', 'max:100', 'unique:devices,serial_number'],
            'computer_name'       => ['nullable', 'string', 'max:100'],
            'mac_address_ethernet' => ['nullable', 'string', 'max:17', 'unique:devices,mac_address_ethernet'],
            'mac_address_wifi'    => ['nullable', 'string', 'max:17', 'unique:devices,mac_address_wifi'],
            'brand'               => ['required', 'string', 'max:100'],
            'model'               => ['required', 'string', 'max:100'],
            'status'              => ['required', Rule::enum(DeviceStatus::class)],
            'purchase_date'       => ['nullable', 'date'],
            'warranty_expires_at' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'specs'                 => ['nullable', 'array'],
            'specs.cpu'             => ['nullable', 'string', 'max:100'],
            'specs.cores'           => ['nullable', 'integer', 'min:1'],
            'specs.ram'             => ['nullable', 'string', 'max:50'],
            'specs.storage'         => ['nullable', 'string', 'max:100'],
            'specs.os'              => ['nullable', 'string', 'max:100'],
            'specs.phone_number'    => ['nullable', 'string', 'max:30'],
            'specs.imei'            => ['nullable', 'string', 'max:50'],
            'specs.data_plan'       => ['nullable', 'string', 'max:100'],
            'notes'                 => ['nullable', 'string'],
        ];
    }
}
