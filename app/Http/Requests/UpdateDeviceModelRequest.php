<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_category_id' => ['required', 'exists:device_categories,id'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:150'],
            'variant' => ['nullable', 'string', 'max:150'],
            'cpu' => ['nullable', 'string', 'max:255'],
            'cores' => ['nullable', 'string', 'max:100'],
            'ram' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:150'],
            'os' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'device_category_id.required' => 'Debes seleccionar una categoría para este estándar.',
            'brand.required' => 'La marca del modelo es obligatoria.',
            'model.required' => 'El nombre o número del modelo es obligatorio.',
        ];
    }
}
