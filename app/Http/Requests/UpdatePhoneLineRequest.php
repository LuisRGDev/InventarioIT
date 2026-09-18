<?php

namespace App\Http\Requests;

use App\Enums\PhoneLineStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'number' => ['required', 'string', 'max:20', Rule::unique('phone_lines', 'number')->ignore($this->route('phone_line'))],
            'status' => ['required', Rule::enum(PhoneLineStatus::class)],
            'data_plan' => ['nullable', 'string', 'max:255'],
            'plan_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
