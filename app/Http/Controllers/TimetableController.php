<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::with('sections')->orderBy('level')->get();
        $class = SchoolClass::with('sections')->find($request->input('class_id')) ?? $classes->first();
        $sections = $class?->sections ?? collect();
        $section = $sections->firstWhere('id', $request->input('section_id')) ?? $sections->first();

        $entries = collect();
        if ($class) {
            $entries = TimetableEntry::with(['subject', 'teacher'])
                ->where('school_class_id', $class->id)
                ->when($section, fn ($q) => $q->where('section_id', $section?->id), fn ($q) => $q->whereNull('section_id'))
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');
        }

        return view('timetable.index', compact('classes', 'class', 'sections', 'section', 'entries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id'      => ['nullable', 'exists:sections,id'],
            'subject_id'      => ['required', 'exists:subjects,id'],
            'teacher_id'      => ['nullable', 'exists:teachers,id'],
            'day_of_week'     => ['required', 'integer', 'min:0', 'max:6'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i', 'after:start_time'],
            'room'            => ['nullable', 'string', 'max:40'],
        ]);

        // Class-slot conflict check.
        $conflict = TimetableEntry::where('school_class_id', $data['school_class_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->when($data['section_id'], fn ($q) => $q->where('section_id', $data['section_id']), fn ($q) => $q->whereNull('section_id'))
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'This class already has a period in the selected time slot.');
        }

        // Teacher conflict check.
        if (! empty($data['teacher_id'])) {
            $teacherBusy = TimetableEntry::where('teacher_id', $data['teacher_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->exists();

            if ($teacherBusy) {
                return back()->withInput()->with('error', 'This teacher already has a class in the selected time slot.');
            }
        }

        TimetableEntry::create($data);

        return back()->with('success', 'Timetable period added.');
    }

    public function destroy(TimetableEntry $entry)
    {
        $entry->delete();

        return back()->with('success', 'Timetable period removed.');
    }
}
