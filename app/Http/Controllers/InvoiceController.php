<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'schoolClass', 'feeType']);

        // Non-admin roles only see their own invoices.
        $user = $request->user();
        if ($user->isStudent() && $user->student) {
            $query->where('student_id', $user->student->id);
        } elseif ($user->isParent()) {
            $query->whereIn('student_id', $user->children()->pluck('id'));
        }

        $query->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($qq) use ($term) {
                    $qq->where('invoice_no', 'like', "%{$term}%")
                        ->orWhereHas('student', function ($s) use ($term) {
                            $s->where('first_name', 'like', "%{$term}%")
                                ->orWhere('last_name', 'like', "%{$term}%")
                                ->orWhere('admission_no', 'like', "%{$term}%");
                        });
                });
            });

        $totals = [
            'billed' => (clone $query)->get()->sum(fn ($i) => $i->netAmount()),
            'collected' => (clone $query)->with('payments')->get()->sum(fn ($i) => $i->paidAmount()),
        ];

        $invoices = $query->latest()->paginate(20)->withQueryString();

        return view('fees.invoices', compact('invoices', 'totals'));
    }

    public function create()
    {
        $students = Student::active()->orderBy('first_name')->get();
        $classes = SchoolClass::orderBy('level')->get();
        $feeTypes = FeeType::active()->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('fees.invoice-form', compact('students', 'classes', 'feeTypes', 'years'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();

        $duplicate = Invoice::where('student_id', $data['student_id'])
            ->where('fee_type_id', $data['fee_type_id'])
            ->when($data['month'] ?? null, fn ($q) => $q->where('month', $data['month']), fn ($q) => $q->whereNull('month'))
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'An invoice already exists for this student, fee type and month.');
        }

        DB::transaction(function () use ($data) {
            Student::find($data['student_id'])->invoices()->create($data + [
                'invoice_no'  => app(InvoiceService::class)->nextInvoiceNo(),
                'issued_date' => now()->toDateString(),
                'status'      => 'unpaid',
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['student', 'schoolClass', 'feeType', 'payments.receiver']);

        return view('fees.invoice-show', compact('invoice'));
    }

    public function generate(Request $request, InvoiceService $service)
    {
        $data = $request->validate([
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'fee_type_id'      => ['required', 'exists:fee_types,id'],
            'month'            => ['nullable', 'date_format:Y-m'],
            'due_date'         => ['nullable', 'date'],
        ]);

        $count = $service->generateForClass(
            SchoolClass::find($data['school_class_id']),
            FeeType::find($data['fee_type_id']),
            AcademicYear::find($data['academic_year_id']),
            $data['month'] ?? null,
            $data['due_date'] ?? null,
        );

        return back()->with('success', "{$count} invoices generated.");
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        if ($invoice->payments()->exists()) {
            return back()->with('error', 'Cannot delete: payments recorded against this invoice.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
