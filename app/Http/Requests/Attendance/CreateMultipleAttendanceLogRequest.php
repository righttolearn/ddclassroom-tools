<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CreateMultipleAttendanceLogRequest extends FormRequest
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
            'month_id' => ['required', 'integer', 'exists:months,id'],
            'year' => ['required', 'exists:years,name'],
            'sub_grade_id' => ['nullable', 'integer', 'exists:sub_grades,id'],
            'term' => ['required', 'integer', 'in:1,2'],
            'location' => ['required', 'in:ddc,dlc,arsa'],
            'file' => ['required', 'mimes:xlsx, csv, xls'],
        ];
    }
}
