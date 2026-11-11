<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    public const STATUS_ISSUED  = 'issued';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_LOST    = 'lost';

    protected $fillable = [
        'book_id', 'student_id', 'issue_date', 'due_date',
        'return_date', 'status', 'fine', 'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date'   => 'date',
            'return_date' => 'date',
            'fine'       => 'decimal:2',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ISSUED
            && $this->due_date->isPast();
    }
}
