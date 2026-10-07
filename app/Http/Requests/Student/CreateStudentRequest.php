<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStudentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'fa_name' => 'required',
            'country_id' => 'required',
            'grade_id' => 'required',
            'username' => 'required',
            'email' => 'required',
            'support_type' => ['nullable', Rule::in(['credit_card', 'cash'])],
        ];
    }
}
