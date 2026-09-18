<?php

namespace App\Http\Requests;

use App\Enums\ExtensionStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfficeExtensionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'extension_number' => ['required', 'string', 'max:50', 'unique:office_extensions,extension_number'],
            'direct_number' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::enum(ExtensionStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
