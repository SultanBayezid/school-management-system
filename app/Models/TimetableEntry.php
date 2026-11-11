<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    public const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    protected $fillable = [
        'school_class_id', 'section_id', 'subject_id', 'teacher_id',
        'day_of_week', 'start_time', 'end_time', 'room',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'start_time'  => 'datetime:H:i',
            'end_time'    => 'datetime:H:i',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function dayName(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }
}
