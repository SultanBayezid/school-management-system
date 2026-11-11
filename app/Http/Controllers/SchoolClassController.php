<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount([
            'sections',
            'activeEnrollments' => fn ($q) => $q->where('academic_year_id', AcademicYear::current()?->id),
        ])->orderBy('level')->paginate(20);

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.form', ['class' => new SchoolClass()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:school_classes,name'],
            'level'       => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        SchoolClass::create($data);

        return redirect()->route('classes.index')->with('success', 'Class created.');
    }

    public function show(SchoolClass $class)
    {
        $class->load([
            'sections.classTeacher',
            'subjects',
            'classSubjects.teacher',
        ]);

        $enrollments = $class->enrollments()
            ->where('academic_year_id', AcademicYear::current()?->id)
            ->where('status', 'active')
            ->with(['student', 'section'])
            ->orderBy('roll_no')
            ->paginate(30);

        $teachers = Teacher::where('is_active', true)->orderBy('first_name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('classes.show', compact('class', 'enrollments', 'teachers', 'subjects'));
    }

    public function edit(SchoolClass $class)
    {
        return view('classes.form', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:school_classes,name,'.$class->id],
            'level'       => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $class->update($data);

        return redirect()->route('classes.index')->with('success', 'Class updated.');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->enrollments()->exists() || $class->exams()->exists()) {
            return back()->with('error', 'Cannot delete: this class has students or exams.');
        }

        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted.');
    }
}
