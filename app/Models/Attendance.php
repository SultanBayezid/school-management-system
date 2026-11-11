<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT  = 'absent';
    public const STATUS_LATE    = 'late';
    public const STATUS_LEAVE   = 'leave';

    public const STATUSES = [
        self::STATUS_PRESENT,
        self::STATUS_ABSENT,
        self::STATUS_LATE,
        self::STATUS_LEAVE,
    ];

    protected $fillable = [
        'student_id', 'school_class_id', 'section_id', 'academic_year_id',
        'attendance_date', 'status', 'remarks', 'marked_by',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
