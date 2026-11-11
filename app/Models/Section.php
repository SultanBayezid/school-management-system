<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['school_class_id', 'name', 'capacity', 'teacher_id'];

    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function label(): string
    {
        return $this->schoolClass?->name.' - '.$this->name;
    }
}
