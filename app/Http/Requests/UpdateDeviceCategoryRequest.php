<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deviceCategoryId = $this->route('device_category')?->id ?? $this->route('device_category');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('device_categories', 'name')->ignore($deviceCategoryId)],
            'description' => ['nullable', 'string'],
        ];
    }
}
