<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate monthly invoices for every active student of a class.
     * Skips students that already have an invoice for the same
     * fee type + month. Returns the number of invoices created.
     */
    public function generateForClass(
        SchoolClass $class,
        FeeType $feeType,
        AcademicYear $year,
        ?string $month,
        ?string $dueDate,
    ): int {
        $created = 0;

        $students = $class->enrollments()
            ->where('academic_year_id', $year->id)
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter(fn ($s) => $s !== null && $s->is_active);

        foreach ($students as $student) {
            $exists = $student->invoices()
                ->where('fee_type_id', $feeType->id)
                ->when($month, fn ($q) => $q->where('month', $month))
                ->when(! $month, fn ($q) => $q->whereNull('month'))
                ->exists();

            if ($exists) {
                continue;
            }

            DB::transaction(function () use ($student, $class, $year, $feeType, $month, $dueDate) {
                $student->invoices()->create([
                    'invoice_no'       => $this->nextInvoiceNo(),
                    'school_class_id'  => $class->id,
                    'academic_year_id' => $year->id,
                    'fee_type_id'      => $feeType->id,
                    'month'            => $month,
                    'amount'           => $feeType->amount,
                    'due_date'         => $dueDate,
                    'issued_date'      => now()->toDateString(),
                    'status'           => 'unpaid',
                ]);
            });

            $created++;
        }

        return $created;
    }

    /**
     * Human-readable sequential invoice number, e.g. INV-2026-000042.
     */
    public function nextInvoiceNo(): string
    {
        $year = now()->format('Y');
        $count = \App\Models\Invoice::whereYear('created_at', $year)->lockForUpdate()->count() + 1;

        return sprintf('INV-%s-%06d', $year, $count);
    }
}
