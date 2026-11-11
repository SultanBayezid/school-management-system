<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Student;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExamApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $exams = Exam::with(['schoolClass', 'academicYear'])
            ->withCount('marks')
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('is_published', true))
            ->when($request->query('class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->latest()
            ->paginate(min((int) $request->query('per_page', 20), 100));

        return ExamResource::collection($exams);
    }

    public function show(Exam $exam): ExamResource
    {
        $exam->load(['schoolClass', 'academicYear'])->loadCount('marks');

        return new ExamResource($exam);
    }

    public function reportCard(Request $request, Exam $exam, Student $student, GradeService $grades)
    {
        if (! $exam->is_published && ! $request->user()->isAdmin()) {
            abort(403, 'Results are not published yet.');
        }

        $report = $grades->reportCard($exam, $student);
        $position = $grades->classPosition($exam, $student);

        return response()->json([
            'exam'     => new ExamResource($exam->loadMissing('schoolClass', 'academicYear')),
            'student'  => ['id' => $student->id, 'name' => $student->full_name(), 'admission_no' => $student->admission_no],
            'subjects' => $report['rows'],
            'totals'   => [
                'obtained'       => $report['total_obtained'],
                'max'            => $report['total_max'],
                'percentage'     => $report['percentage'],
                'average'        => $report['average'],
                'grade'          => $report['grade'],
                'grade_point'    => $report['grade_point'],
                'failed_subjects' => $report['failed_subjects'],
            ],
            'class_position' => $position,
        ]);
    }
}
