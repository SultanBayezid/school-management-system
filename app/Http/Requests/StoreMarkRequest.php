<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isTeacher();
    }

    public function rules(): array
    {
        return [
            'exam_id'            => ['required', 'exists:exams,id'],
            'subject_id'         => ['required', 'exists:subjects,id'],
            'max_marks'          => ['required', 'numeric', 'min:1', 'max:1000'],
            'marks'              => ['required', 'array', 'min:1'],
            'marks.*'            => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $max = (float) $this->input('max_marks', 0);
            foreach ((array) $this->input('marks', []) as $value) {
                if ($value !== null && (float) $value > $max) {
                    $validator->errors()->add('marks', 'A mark exceeds the maximum of '.$max.'.');
                    break;
                }
            }
        });
    }
}
