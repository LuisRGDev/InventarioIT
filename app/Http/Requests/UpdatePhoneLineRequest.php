<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePhoneLineRequest extends FormRequest
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
            'number' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('phone_lines', 'number')->ignore($this->route('phone_line'))],
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\PhoneLineStatus::class)],
            'data_plan' => ['nullable', 'string', 'max:255'],
            'plan_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
