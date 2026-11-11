<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\GradeService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::with(['schoolClass', 'academicYear'])
            ->withCount('marks')
            ->when($request->input('class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::orderBy('level')->get();

        return view('exams.index', compact('exams', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('level')->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('exams.form', ['exam' => new Exam(), 'classes' => $classes, 'years' => $years]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:150'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'start_date'       => ['nullable', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'description'      => ['nullable', 'string', 'max:1000'],
        ]);

        Exam::create($data);

        return redirect()->route('exams.index')->with('success', 'Exam created.');
    }

    public function show(Exam $exam)
    {
        $exam->load('schoolClass', 'academicYear');

        $subjects = $exam->schoolClass->subjects;

        $stats = $exam->marks()
            ->selectRaw('subject_id, COUNT(*) AS entries, AVG(marks_obtained) AS avg_marks, MAX(marks_obtained) AS best, MIN(marks_obtained) AS worst')
            ->with('subject:id,name')
            ->groupBy('subject_id')
            ->get();

        return view('exams.show', compact('exam', 'subjects', 'stats'));
    }

    public function edit(Exam $exam)
    {
        $classes = SchoolClass::orderBy('level')->get();
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('exams.form', compact('exam', 'classes', 'years'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:150'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'start_date'       => ['nullable', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'is_published'     => ['boolean'],
        ]);

        $exam->update($data + ['is_published' => (bool) ($data['is_published'] ?? false)]);

        return redirect()->route('exams.show', $exam)->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        if ($exam->marks()->exists()) {
            return back()->with('error', 'Cannot delete: marks recorded for this exam.');
        }

        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted.');
    }

    public function marksEntry(Request $request, Exam $exam)
    {
        $subjectId = $request->input('subject_id');
        $subjects = $exam->schoolClass->subjects;
        $subject = $subjects->firstWhere('id', $subjectId) ?? $subjects->first();

        $students = $exam->schoolClass->enrollments()
            ->where('academic_year_id', $exam->academic_year_id)
            ->where('status', 'active')
            ->with('student')
            ->orderBy('roll_no')
            ->get()
            ->pluck('student')
            ->filter();

        $existing = $subject
            ? $exam->marks()->where('subject_id', $subject->id)->get()->keyBy('student_id')
            : collect();

        return view('exams.marks-entry', compact('exam', 'subjects', 'subject', 'students', 'existing'));
    }

    public function marksStore(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'max_marks'  => ['required', 'numeric', 'min:1', 'max:1000'],
            'marks'      => ['required', 'array', 'min:1'],
            'marks.*'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $failures = collect($validated['marks'])
            ->filter(fn ($v) => $v !== null && (float) $v > (float) $validated['max_marks']);

        if ($failures->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'marks' => 'Marks cannot exceed the maximum of '.$validated['max_marks'].'.',
            ]);
        }

        foreach ($validated['marks'] as $studentId => $value) {
            $exam->marks()->updateOrCreate(
                ['student_id' => $studentId, 'subject_id' => $validated['subject_id']],
                [
                    'marks_obtained' => $value ?? 0,
                    'max_marks'      => $validated['max_marks'],
                    'recorded_by'    => $request->user()->id,
                ],
            );
        }

        return redirect()
            ->route('exams.marks-entry', ['exam' => $exam, 'subject_id' => $validated['subject_id']])
            ->with('success', 'Marks saved.');
    }

    public function reportCard(Request $request, Exam $exam, Student $student, GradeService $grades)
    {
        $report = $grades->reportCard($exam, $student);
        $position = $grades->classPosition($exam, $student);

        $student->load('currentEnrollment.schoolClass', 'currentEnrollment.section');

        return view('exams.report-card', compact('exam', 'student', 'report', 'position'));
    }
}
