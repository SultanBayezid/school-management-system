<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'type', 'description'];

    public function schoolClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject')
            ->withPivot(['id', 'teacher_id'])
            ->withTimestamps()
            ->using(ClassSubject::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }
}
