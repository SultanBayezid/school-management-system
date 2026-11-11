<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\GradeBand;
use App\Models\Student;
use Illuminate\Support\Collection;

class GradeService
{
    /**
     * Resolve a percentage into the matching grade band.
     */
    public function bandFor(float $percentage): ?GradeBand
    {
        return GradeBand::forPercentage($percentage)
            ->orderByDesc('min_percent')
            ->first();
    }

    /**
     * Build a full report card for a student in one exam:
     * per-subject marks, totals, average, GPA and overall grade.
     *
     * @return array{
     *     rows: \Illuminate\Support\Collection<int, array{subject: string, obtained: float, max: float, percentage: float, grade: ?string, point: float}>,
     *     total_obtained: float,
     *     total_max: float,
     *     percentage: float,
     *     average: float,
     *     grade: ?string,
     *     grade_point: float,
     *     failed_subjects: int
     * }
     */
    public function reportCard(Exam $exam, Student $student): array
    {
        $marks = $exam->marks()
            ->with('subject')
            ->where('student_id', $student->id)
            ->get();

        $rows = collect();
        $totalObtained = 0.0;
        $totalMax = 0.0;
        $failed = 0;

        foreach ($marks as $mark) {
            $percentage = $mark->percentage();
            $band = $this->bandFor($percentage);

            if ($band && $band->grade === 'F') {
                $failed++;
            }

            $totalObtained += (float) $mark->marks_obtained;
            $totalMax += (float) $mark->max_marks;

            $rows->push([
                'subject'    => $mark->subject->name,
                'obtained'   => (float) $mark->marks_obtained,
                'max'        => (float) $mark->max_marks,
                'percentage' => $percentage,
                'grade'      => $band?->grade,
                'point'      => (float) ($band?->grade_point ?? 0),
            ]);
        }

        $subjectCount = max($rows->count(), 1);
        $overallPercentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0.0;
        $overallBand = $this->bandFor($overallPercentage);
        $gpa = round($rows->sum('point') / $subjectCount, 2);

        return [
            'rows'            => $rows,
            'total_obtained'  => $totalObtained,
            'total_max'       => $totalMax,
            'percentage'      => $overallPercentage,
            'average'         => round($totalObtained / $subjectCount, 2),
            'grade'           => $overallBand?->grade,
            'grade_point'     => $gpa,
            'failed_subjects' => $failed,
        ];
    }

    /**
     * Position of a student among classmates for an exam (highest total first).
     */
    public function classPosition(Exam $exam, Student $student): int
    {
        $totals = $exam->marks()
            ->selectRaw('student_id, SUM(marks_obtained) AS total')
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->pluck('total', 'student_id');

        $position = 0;
        foreach ($totals as $studentId => $total) {
            $position++;
            if ((int) $studentId === $student->id) {
                return $position;
            }
        }

        return 0;
    }
}
