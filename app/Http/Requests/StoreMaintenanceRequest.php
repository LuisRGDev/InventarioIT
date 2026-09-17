<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id'    => ['required', 'exists:devices,id'],
            'type'         => ['required', Rule::enum(MaintenanceType::class)],
            'status'       => ['required', Rule::enum(MaintenanceStatus::class)],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'next_due_at'  => ['nullable', 'date', 'after_or_equal:today'],
            'update_device_status_repair' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'device_id.required' => 'Debes seleccionar el equipo al que se le aplicará el servicio.',
            'title.required'     => 'Por favor escribe un título corto para identificar el servicio.',
        ];
    }
}
