<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'admission_no'      => ['required', 'string', 'max:30', Rule::unique('students', 'admission_no')->ignore($this->route('student'))],
            'first_name'        => ['required', 'string', 'max:100'],
            'last_name'         => ['required', 'string', 'max:100'],
            'gender'            => ['nullable', 'in:male,female,other'],
            'date_of_birth'     => ['nullable', 'date', 'before:today'],
            'phone'             => ['nullable', 'string', 'max:40'],
            'address'           => ['nullable', 'string', 'max:500'],
            'blood_group'       => ['nullable', 'string', 'max:8'],
            'guardian_name'     => ['nullable', 'string', 'max:150'],
            'guardian_phone'    => ['nullable', 'string', 'max:40'],
            'guardian_relation' => ['nullable', 'string', 'max:40'],
            'admission_date'    => ['nullable', 'date'],
            'is_active'         => ['boolean'],
        ];
    }
}
