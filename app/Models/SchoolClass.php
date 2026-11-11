<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = ['name', 'level', 'description'];

    protected function casts(): array
    {
        return ['level' => 'integer'];
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject')
            ->withPivot(['id', 'teacher_id'])
            ->withTimestamps()
            ->using(ClassSubject::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }
}
