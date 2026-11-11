<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Student $student;
    private FeeType $tuition;
    private SchoolClass $class;
    private AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->year = AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
        $this->class = SchoolClass::create(['name' => 'Class One', 'level' => 1]);
        $this->student = Student::factory()->create();
        $this->class->enrollments()->create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->year->id,
            'roll_no' => 1,
            'status' => 'active',
        ]);
        $this->tuition = FeeType::create([
            'name' => 'Tuition', 'amount' => 2000, 'frequency' => 'monthly',
        ]);
    }

    public function test_admin_can_generate_invoices_for_a_class(): void
    {
        $response = $this->actingAs($this->admin)->post('/invoices/generate', [
            'school_class_id' => $this->class->id,
            'academic_year_id' => $this->year->id,
            'fee_type_id' => $this->tuition->id,
            'month' => '2026-11',
        ]);

        $response->assertRedirect()->assertSessionHas('success', '1 invoices generated.');
        $this->assertSame(1, Invoice::count());
        $this->assertDatabaseHas('invoices', [
            'student_id' => $this->student->id,
            'amount' => 2000,
            'status' => 'unpaid',
        ]);
    }

    public function test_generation_skips_existing_invoices(): void
    {
        $payload = [
            'school_class_id' => $this->class->id,
            'academic_year_id' => $this->year->id,
            'fee_type_id' => $this->tuition->id,
            'month' => '2026-11',
        ];

        $this->actingAs($this->admin)->post('/invoices/generate', $payload);
        $this->actingAs($this->admin)->post('/invoices/generate', $payload);

        $this->assertSame(1, Invoice::count());
    }

    public function test_payment_updates_invoice_status_to_partial_and_paid(): void
    {
        $invoice = $this->student->invoices()->create([
            'invoice_no' => 'INV-2026-000001',
            'school_class_id' => $this->class->id,
            'academic_year_id' => $this->year->id,
            'fee_type_id' => $this->tuition->id,
            'amount' => 1000,
            'issued_date' => today()->toDateString(),
            'status' => 'unpaid',
        ]);

        // Partial payment.
        $this->actingAs($this->admin)->post('/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 400,
            'paid_date' => today()->toDateString(),
            'method' => 'cash',
        ]);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'partial']);

        // Remaining payment.
        $this->actingAs($this->admin)->post('/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 600,
            'paid_date' => today()->toDateString(),
            'method' => 'cash',
        ]);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
        $this->assertSame(2, $invoice->payments()->count());
    }

    public function test_overpayment_is_rejected(): void
    {
        $invoice = $this->student->invoices()->create([
            'invoice_no' => 'INV-2026-000002',
            'school_class_id' => $this->class->id,
            'academic_year_id' => $this->year->id,
            'fee_type_id' => $this->tuition->id,
            'amount' => 500,
            'issued_date' => today()->toDateString(),
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->admin)->post('/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 600,
            'paid_date' => today()->toDateString(),
            'method' => 'cash',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertSame(0, $invoice->payments()->count());
    }

    public function test_students_only_see_their_own_invoices(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $own = $this->student;
        $own->update(['user_id' => $user->id]);
        $other = Student::factory()->create();

        $ownInvoice = $own->invoices()->create([
            'invoice_no' => 'INV-2026-000003',
            'school_class_id' => $this->class->id, 'academic_year_id' => $this->year->id,
            'fee_type_id' => $this->tuition->id, 'amount' => 100,
            'issued_date' => today()->toDateString(),
        ]);
        $otherInvoice = $other->invoices()->create([
            'invoice_no' => 'INV-2026-000004',
            'school_class_id' => $this->class->id, 'academic_year_id' => $this->year->id,
            'fee_type_id' => $this->tuition->id, 'amount' => 100,
            'issued_date' => today()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/invoices');

        $response->assertOk()->assertSee($ownInvoice->invoice_no)->assertDontSee($otherInvoice->invoice_no);
    }
}
