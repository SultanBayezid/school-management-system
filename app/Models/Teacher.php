<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'employee_id', 'first_name', 'last_name', 'gender',
        'date_of_birth', 'phone', 'email', 'address', 'qualification',
        'join_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'join_date'     => 'date',
            'is_active'     => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'teacher_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function full_name(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
