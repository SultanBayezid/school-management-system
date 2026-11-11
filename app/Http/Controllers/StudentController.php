<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $students = Student::with('currentEnrollment.schoolClass', 'currentEnrollment.section')
            ->active()
            ->search($request->input('search'))
            ->when($request->filled('class_id'), function ($q) use ($request) {
                $q->whereHas('currentEnrollment', fn ($e) => $e->where('school_class_id', $request->input('class_id')));
            })
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::orderBy('level')->get();

        return view('students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $this->authorize('create', Student::class);

        $classes = SchoolClass::with('sections')->orderBy('level')->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('students.form', ['student' => new Student(), 'classes' => $classes, 'years' => $years]);
    }

    public function store(StoreStudentRequest $request)
    {
        $this->authorize('create', Student::class);

        $data = $request->validated();

        $student = DB::transaction(function () use ($request, $data) {
            $student = Student::create($data);

            // Optional enrollment into a class right away.
            if ($request->filled('school_class_id')) {
                $enroll = $request->validate([
                    'school_class_id'  => ['required', 'exists:school_classes,id'],
                    'section_id'       => ['nullable', 'exists:sections,id'],
                    'academic_year_id' => ['required', 'exists:academic_years,id'],
                    'roll_no'          => ['nullable', 'integer', 'min:1', 'max:9999'],
                ]);

                $student->enrollments()->create($enroll + ['status' => 'active']);
            }

            return $student;
        });

        return redirect()->route('students.show', $student)->with('success', 'Student admitted.');
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);

        $student->load('currentEnrollment.schoolClass', 'currentEnrollment.section');

        $attendance = app(\App\Services\AttendanceService::class)
            ->monthlySummary($student->id, now()->format('Y-m'));

        $invoices = $student->invoices()->with('feeType', 'payments')->latest()->take(10)->get();
        $bookIssues = $student->bookIssues()->with('book')->latest()->take(10)->get();

        return view('students.show', compact('student', 'attendance', 'invoices', 'bookIssues'));
    }

    public function edit(Student $student)
    {
        $this->authorize('update', $student);

        $classes = SchoolClass::with('sections')->orderBy('level')->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('students.form', ['student' => $student, 'classes' => $classes, 'years' => $years]);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->authorize('update', $student);

        $student->update($request->validated());

        return redirect()->route('students.show', $student)->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        DB::transaction(function () use ($student) {
            $student->user?->delete();
            $student->parentUser?->delete();
            $student->delete();
        });

        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }
}
