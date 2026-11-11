<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'student_id'       => ['required', 'exists:students,id'],
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'fee_type_id'      => ['required', 'exists:fee_types,id'],
            'month'            => ['nullable', 'date_format:Y-m'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'discount'         => ['nullable', 'numeric', 'min:0'],
            'fine'             => ['nullable', 'numeric', 'min:0'],
            'due_date'         => ['nullable', 'date'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}
