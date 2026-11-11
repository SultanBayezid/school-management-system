<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'parent_user_id', 'admission_no', 'first_name', 'last_name',
        'gender', 'date_of_birth', 'phone', 'address', 'blood_group',
        'guardian_name', 'guardian_phone', 'guardian_relation',
        'admission_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'    => 'date',
            'admission_date'   => 'date',
            'is_active'        => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollment()
    {
        $year = AcademicYear::current();

        return $this->hasOne(Enrollment::class)
            ->where('academic_year_id', $year?->id)
            ->where('status', 'active');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }

    public function full_name(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('admission_no', 'like', "%{$term}%")
                ->orWhere('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('guardian_name', 'like', "%{$term}%")
                ->orWhere('guardian_phone', 'like', "%{$term}%");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
