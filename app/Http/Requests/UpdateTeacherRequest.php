<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'employee_id'   => ['required', 'string', 'max:30', \Illuminate\Validation\Rule::unique('teachers', 'employee_id')->ignore($this->route('teacher'))],
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'gender'        => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'phone'         => ['nullable', 'string', 'max:40'],
            'email'         => ['nullable', 'email', 'max:150'],
            'address'       => ['nullable', 'string', 'max:500'],
            'qualification' => ['nullable', 'string', 'max:150'],
            'join_date'     => ['nullable', 'date'],
            'is_active'     => ['boolean'],
        ];
    }
}
