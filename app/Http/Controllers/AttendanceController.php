<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        $classes = SchoolClass::with('sections')->orderBy('level')->get();
        $class = $classId ? SchoolClass::findOrFail($classId) : $classes->first();
        $sections = $class?->sections ?? collect();
        $section = $sectionId ? $sections->firstWhere('id', $sectionId) : null;

        $students = collect();
        $existing = collect();

        if ($class) {
            $students = $class->enrollments()
                ->where('academic_year_id', AcademicYear::current()?->id)
                ->where('status', 'active')
                ->when($section, fn ($q) => $q->where('section_id', $section->id))
                ->with('student')
                ->orderBy('roll_no')
                ->get()
                ->pluck('student')
                ->filter();

            $existing = Attendance::whereIn('student_id', $students->pluck('id'))
                ->onDate($date)
                ->get()
                ->keyBy('student_id');
            // (onDate uses whereDate, matching the stored "Y-m-d H:i:s" values.)
        }

        return view('attendance.index', compact('classes', 'class', 'sections', 'section', 'students', 'existing', 'date'));
    }

    public function store(Request $request, AttendanceService $service)
    {
        $validated = $request->validate([
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'section_id'       => ['nullable', 'exists:sections,id'],
            'attendance_date'  => ['required', 'date', 'before_or_equal:today'],
            'statuses'         => ['required', 'array', 'min:1'],
            'statuses.*'       => ['required', 'in:'.implode(',', Attendance::STATUSES)],
        ]);

        $year = AcademicYear::current()
            ?? AcademicYear::orderByDesc('start_date')->firstOrFail();

        $count = $service->saveDaily(
            SchoolClass::find($validated['school_class_id']),
            $validated['section_id'] ? \App\Models\Section::find($validated['section_id']) : null,
            $year,
            $validated['attendance_date'],
            $validated['statuses'],
            $request->user(),
        );

        return redirect()
            ->route('attendance.index', ['date' => $validated['attendance_date'], 'class_id' => $validated['school_class_id'], 'section_id' => $validated['section_id']])
            ->with('success', "Attendance saved for {$count} students.");
    }

    public function report(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $service = app(AttendanceService::class);

        $query = Attendance::query()
            ->whereMonth('attendance_date', substr($month, 5, 2))
            ->whereYear('attendance_date', substr($month, 0, 4));

        $rows = (clone $query)
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->selectRaw('students.id, students.admission_no, students.first_name, students.last_name, attendances.status, COUNT(*) AS c')
            ->groupBy('students.id', 'students.admission_no', 'students.first_name', 'students.last_name', 'attendances.status')
            ->orderBy('students.admission_no')
            ->get()
            ->groupBy('id');

        $summary = (clone $query)
            ->selectRaw('status, COUNT(*) AS c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('attendance.report', compact('rows', 'summary', 'month'));
    }
}
