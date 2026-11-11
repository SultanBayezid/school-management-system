<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Upsert a full day of attendance for one class + section.
     *
     * @param  array<int, string>  $statuses  student_id => present|absent|late|leave
     */
    public function saveDaily(
        SchoolClass $class,
        ?Section $section,
        AcademicYear $year,
        string $date,
        array $statuses,
        User $markedBy,
    ): int {
        $touched = 0;

        // Eloquent persists date-cast columns as "Y-m-d H:i:s", so the
        // lookup key must use the same normalized format.
        $normalizedDate = \Illuminate\Support\Carbon::parse($date)->format('Y-m-d H:i:s');

        DB::transaction(function () use ($class, $section, $year, $normalizedDate, $statuses, $markedBy, &$touched) {
            foreach ($statuses as $studentId => $status) {
                if (! in_array($status, Attendance::STATUSES, true)) {
                    continue;
                }

                Attendance::updateOrCreate(
                    [
                        'student_id'      => $studentId,
                        'attendance_date' => $normalizedDate,
                    ],
                    [
                        'school_class_id'  => $class->id,
                        'section_id'       => $section?->id,
                        'academic_year_id' => $year->id,
                        'status'           => $status,
                        'marked_by'        => $markedBy->id,
                    ],
                );

                $touched++;
            }
        });

        return $touched;
    }

    /**
     * Monthly attendance summary for a student:
     * counts per status + attendance rate over recorded days.
     *
     * @return array{counts: array<string, int>, recorded: int, rate: float}
     */
    public function monthlySummary(int $studentId, string $month): array
    {
        $counts = Attendance::where('student_id', $studentId)
            ->whereMonth('attendance_date', substr($month, 5, 2))
            ->whereYear('attendance_date', substr($month, 0, 4))
            ->selectRaw('status, COUNT(*) AS c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $counts = collect(Attendance::STATUSES)
            ->mapWithKeys(fn ($s) => [$s => (int) ($counts[$s] ?? 0)])
            ->toArray();

        $recorded = array_sum($counts);
        $present = $counts[Attendance::STATUS_PRESENT] + $counts[Attendance::STATUS_LATE];
        $rate = $recorded > 0 ? round(($present / $recorded) * 100, 1) : 0.0;

        return ['counts' => $counts, 'recorded' => $recorded, 'rate' => $rate];
    }
}
