<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:device_categories,name'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:device_categories,slug'],
            'description' => ['nullable', 'string'],
        ];
    }
}
