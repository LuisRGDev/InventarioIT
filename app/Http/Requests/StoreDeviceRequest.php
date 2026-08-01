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
            'device_model_id'     => ['nullable', 'exists:device_models,id'],
            'serial_number'       => ['required', 'string', 'max:100', 'unique:devices,serial_number'],
            'service_tag'         => ['nullable', 'string', 'max:100'],
            'computer_name'       => ['nullable', 'string', 'max:100'],
            'bitlocker_identifier' => ['nullable', 'string', 'max:255'],
            'bitlocker_key'       => ['nullable', 'string'],
            'mac_address_ethernet' => ['nullable', 'string', 'max:17', 'unique:devices,mac_address_ethernet'],
            'mac_address_wifi'    => ['nullable', 'string', 'max:17', 'unique:devices,mac_address_wifi'],
            'brand'               => ['required', 'string', 'max:100'],
            'model'               => ['required', 'string', 'max:100'],
            'status'              => [
                'required', 
                Rule::enum(DeviceStatus::class),
                function ($attribute, $value, $fail) {
                    if ($value === DeviceStatus::Asignado->value && empty($this->input('assign_to_employee_id'))) {
                        $fail('No puedes registrar un equipo con estatus "Asignado" si no seleccionas a un empleado en la sección de "Asignación Inmediata".');
                    }
                }
            ],
            'purchase_date'       => ['nullable', 'date'],
            'warranty_expires_at' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'specs'                 => ['nullable', 'array'],
            'specs.cpu'             => ['nullable', 'string', 'max:100'],
            'specs.cores'           => ['nullable', 'integer', 'min:1'],
            'specs.ram'             => ['nullable', 'string', 'max:50'],
            'specs.storage'         => ['nullable', 'string', 'max:100'],
            'specs.os'              => ['nullable', 'string', 'max:100'],
            'imei'                  => ['nullable', 'string', 'max:50'],
            'notes'                 => ['nullable', 'string'],
        ];
    }
}
