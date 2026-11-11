<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'subject_id',
        'marks_obtained', 'max_marks', 'remarks', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'max_marks'      => 'decimal:2',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function percentage(): float
    {
        if ((float) $this->max_marks <= 0) {
            return 0.0;
        }

        return round(((float) $this->marks_obtained / (float) $this->max_marks) * 100, 2);
    }
}
