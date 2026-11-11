<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public const STATUS_UNPAID  = 'unpaid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID    = 'paid';

    protected $fillable = [
        'invoice_no', 'student_id', 'school_class_id', 'academic_year_id',
        'fee_type_id', 'month', 'amount', 'discount', 'fine',
        'due_date', 'issued_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'discount'    => 'decimal:2',
            'fine'        => 'decimal:2',
            'due_date'    => 'date',
            'issued_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function netAmount(): float
    {
        return (float) $this->amount - (float) $this->discount + (float) $this->fine;
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function dueAmount(): float
    {
        return max(0.0, $this->netAmount() - $this->paidAmount());
    }

    public function refreshStatus(): void
    {
        $due = $this->dueAmount();
        $net = $this->netAmount();

        $this->status = match (true) {
            $due <= 0 && $net > 0 => self::STATUS_PAID,
            $this->paidAmount() > 0 => self::STATUS_PARTIAL,
            default => self::STATUS_UNPAID,
        };
        $this->save();
    }
}
