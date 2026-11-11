<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isTeacher();
    }

    public function rules(): array
    {
        return [
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'section_id'       => ['nullable', 'exists:sections,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'attendance_date'  => ['required', 'date', 'before_or_equal:today'],
            'statuses'         => ['required', 'array', 'min:1'],
            'statuses.*'       => ['required', 'in:'.implode(',', Attendance::STATUSES)],
        ];
    }
}
