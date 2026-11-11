<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::find($request->input('year_id')) ?? AcademicYear::current();
        $class = SchoolClass::with('sections')->find($request->input('class_id'));

        $enrollments = Enrollment::with(['student', 'schoolClass', 'section', 'academicYear'])
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->when($class, fn ($q) => $q->where('school_class_id', $class->id))
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderBy('roll_no')
            ->paginate(30)
            ->withQueryString();

        $classes = SchoolClass::orderBy('level')->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('enrollments.index', compact('enrollments', 'classes', 'years', 'year', 'class'));
    }

    public function create()
    {
        $students = Student::active()->orderBy('first_name')->get();
        $classes = SchoolClass::with('sections')->orderBy('level')->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('enrollments.form', [
            'enrollment' => new Enrollment(),
            'students'   => $students,
            'classes'    => $classes,
            'years'      => $years,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'       => ['required', 'exists:students,id'],
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'section_id'       => ['nullable', 'exists:sections,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'roll_no'          => ['nullable', 'integer', 'min:1', 'max:9999'],
            'status'           => ['required', 'in:active,completed,dropped'],
        ]);

        $exists = Enrollment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Student already has an enrollment in this academic year.');
        }

        Enrollment::create($data);

        return redirect()->route('enrollments.index')->with('success', 'Student enrolled.');
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'section_id' => ['nullable', 'exists:sections,id'],
            'roll_no'    => ['nullable', 'integer', 'min:1', 'max:9999'],
            'status'     => ['required', 'in:active,completed,dropped'],
        ]);

        $enrollment->update($data);

        return back()->with('success', 'Enrollment updated.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return back()->with('success', 'Enrollment removed.');
    }
}
