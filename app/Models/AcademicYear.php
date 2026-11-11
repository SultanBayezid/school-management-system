<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'end_date'    => 'date',
            'is_current'  => 'boolean',
        ];
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first();
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('is_current', true);
    }
}
