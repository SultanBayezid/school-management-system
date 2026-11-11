<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceCalculationTest extends TestCase
{
    use RefreshDatabase;

    private function persistInvoice(array $attributes = []): Invoice
    {
        $student = Student::factory()->create();
        $class = SchoolClass::create(['name' => 'Class One', 'level' => 1]);
        $year = AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
        $feeType = FeeType::create(['name' => 'Tuition', 'amount' => 1000, 'frequency' => 'monthly']);

        return $student->invoices()->create(array_merge([
            'invoice_no'       => 'INV-TEST-'.uniqid(),
            'school_class_id'  => $class->id,
            'academic_year_id' => $year->id,
            'fee_type_id'      => $feeType->id,
            'amount'           => 1000,
            'discount'         => 100,
            'fine'             => 50,
            'issued_date'      => today()->toDateString(),
            'status'           => 'unpaid',
        ], $attributes));
    }

    public function test_net_amount_combines_amount_discount_and_fine(): void
    {
        $invoice = $this->persistInvoice();

        // 1000 - 100 + 50 = 950
        $this->assertEquals(950.0, $invoice->netAmount());
    }

    public function test_paid_and_due_amounts_read_payments_relation(): void
    {
        $invoice = $this->persistInvoice();

        $invoice->payments()->create([
            'amount' => 400, 'paid_date' => now()->toDateString(),
            'method' => 'cash', 'receipt_no' => 'RCP-1', 'received_by' => User::factory()->create()->id,
        ]);

        $this->assertEquals(400.0, $invoice->paidAmount());
        $this->assertEquals(550.0, $invoice->dueAmount());
    }

    public function test_due_amount_never_negative(): void
    {
        $invoice = $this->persistInvoice(['amount' => 100, 'discount' => 0, 'fine' => 0]);

        $invoice->payments()->create([
            'amount' => 200, 'paid_date' => now()->toDateString(),
            'method' => 'cash', 'receipt_no' => 'RCP-2', 'received_by' => User::factory()->create()->id,
        ]);

        $this->assertEquals(0.0, $invoice->dueAmount());
    }
}
