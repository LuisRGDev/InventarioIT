<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resolution_notes'  => ['required', 'string'],
            'new_device_status' => ['required', 'in:disponible,asignado,obsoleto,baja,mantener'],
            'next_due_at'       => ['nullable', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required' => 'Por favor detalla qué solución o intervención se aplicó para cerrar el ticket.',
        ];
    }
}
