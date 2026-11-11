<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'from'       => ['nullable', 'date'],
            'to'         => ['nullable', 'date', 'after_or_equal:from'],
            'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        // Students may only read their own attendance; parents their children's.
        $user = $request->user();
        if ($user->isStudent() && $user->student) {
            $data['student_id'] = $user->student->id;
        } elseif ($user->isParent()) {
            $childIds = $user->children()->pluck('id');
            if (empty($data['student_id']) || ! $childIds->contains($data['student_id'])) {
                abort(403, 'You may only view attendance of your own children.');
            }
        }

        $records = Attendance::with('student')
            ->when($data['student_id'] ?? null, fn ($q, $id) => $q->where('student_id', $id))
            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('attendance_date', '>=', $d))
            ->when($data['to'] ?? null, fn ($q, $d) => $q->whereDate('attendance_date', '<=', $d))
            ->orderByDesc('attendance_date')
            ->paginate($data['per_page'] ?? 50);

        return AttendanceResource::collection($records);
    }

    public function store(Request $request, AttendanceService $service)
    {
        $data = $request->validate([
            'school_class_id'  => ['required', 'exists:school_classes,id'],
            'section_id'       => ['nullable', 'exists:sections,id'],
            'attendance_date'  => ['required', 'date', 'before_or_equal:today'],
            'statuses'         => ['required', 'array', 'min:1'],
            'statuses.*'       => ['required', 'in:'.implode(',', Attendance::STATUSES)],
        ]);

        $year = \App\Models\AcademicYear::current()
            ?? \App\Models\AcademicYear::orderByDesc('start_date')->firstOrFail();

        $count = $service->saveDaily(
            \App\Models\SchoolClass::find($data['school_class_id']),
            isset($data['section_id']) ? \App\Models\Section::find($data['section_id']) : null,
            $year,
            $data['attendance_date'],
            $data['statuses'],
            $request->user(),
        );

        return response()->json(['saved' => $count], 201);
    }
}
