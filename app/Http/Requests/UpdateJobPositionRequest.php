<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'direction' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'direction.required' => 'La dirección es obligatoria.',
            'area.required' => 'El área es obligatoria.',
            'name.required' => 'El puesto es obligatorio.',
        ];
    }
}
