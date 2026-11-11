<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\GradeBand;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Services\GradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GradeServiceTest extends TestCase
{
    use RefreshDatabase;

    private GradeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(GradeService::class);

        // Seed the standard grade bands.
        $bands = [
            ['grade' => 'A+', 'min_percent' => 80,   'max_percent' => 100,   'grade_point' => 5.00],
            ['grade' => 'A',  'min_percent' => 70,   'max_percent' => 79.99, 'grade_point' => 4.00],
            ['grade' => 'A-', 'min_percent' => 60,   'max_percent' => 69.99, 'grade_point' => 3.50],
            ['grade' => 'B',  'min_percent' => 50,   'max_percent' => 59.99, 'grade_point' => 3.00],
            ['grade' => 'C',  'min_percent' => 40,   'max_percent' => 49.99, 'grade_point' => 2.00],
            ['grade' => 'D',  'min_percent' => 33,   'max_percent' => 39.99, 'grade_point' => 1.00],
            ['grade' => 'F',  'min_percent' => 0,    'max_percent' => 32.99, 'grade_point' => 0.00],
        ];
        foreach ($bands as $band) {
            GradeBand::create($band);
        }
    }

    public static function percentageProvider(): array
    {
        return [
            'full marks'        => [100.0, 'A+'],
            'eighty percent'    => [80.0, 'A+'],
            'seventy percent'   => [70.0, 'A'],
            'sixty percent'     => [60.0, 'A-'],
            'fifty percent'     => [50.0, 'B'],
            'forty percent'     => [40.0, 'C'],
            'pass mark'         => [33.0, 'D'],
            'just below pass'   => [32.99, 'F'],
            'zero'              => [0.0, 'F'],
        ];
    }

    #[DataProvider('percentageProvider')]
    public function test_band_for_percentage(float $percentage, string $expectedGrade): void
    {
        $band = $this->service->bandFor($percentage);

        $this->assertNotNull($band, "No band found for {$percentage}%");
        $this->assertSame($expectedGrade, $band->grade);
    }

    public function test_report_card_computes_totals_and_gpa(): void
    {
        $class = SchoolClass::create(['name' => 'Class One', 'level' => 1]);
        $year = AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
        $exam = Exam::create([
            'name' => 'Unit Test', 'academic_year_id' => $year->id, 'school_class_id' => $class->id,
        ]);
        $student = Student::create([
            'admission_no' => 'ADM-1', 'first_name' => 'Test', 'last_name' => 'Student',
        ]);

        $math = Subject::create(['name' => 'Math', 'code' => 'M1']);
        $english = Subject::create(['name' => 'English', 'code' => 'E1']);

        // 90/100 = A+ (5.00) and 60/100 = A- (3.50); total 150/200 = 75% -> grade A, GPA (5.00+3.50)/2 = 4.25
        $exam->marks()->createMany([
            ['student_id' => $student->id, 'subject_id' => $math->id,    'marks_obtained' => 90, 'max_marks' => 100],
            ['student_id' => $student->id, 'subject_id' => $english->id, 'marks_obtained' => 60, 'max_marks' => 100],
        ]);

        $report = $this->service->reportCard($exam, $student);

        $this->assertCount(2, $report['rows']);
        $this->assertEquals(150.0, $report['total_obtained']);
        $this->assertEquals(200.0, $report['total_max']);
        $this->assertEquals(75.0, $report['percentage']);
        $this->assertEquals(75.0, $report['average']);
        $this->assertSame('A', $report['grade']);
        $this->assertEquals(4.25, $report['grade_point']);
        $this->assertSame(0, $report['failed_subjects']);
    }

    public function test_report_card_counts_failed_subjects(): void
    {
        $class = SchoolClass::create(['name' => 'Class Two', 'level' => 2]);
        $year = AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
        $exam = Exam::create(['name' => 'Test', 'academic_year_id' => $year->id, 'school_class_id' => $class->id]);
        $student = Student::create([
            'admission_no' => 'ADM-2', 'first_name' => 'Fail', 'last_name' => 'Case',
        ]);
        $subject = Subject::create(['name' => 'Science', 'code' => 'S1']);

        $exam->marks()->create([
            'student_id' => $student->id, 'subject_id' => $subject->id,
            'marks_obtained' => 20, 'max_marks' => 100,
        ]);

        $report = $this->service->reportCard($exam, $student);

        $this->assertSame('F', $report['grade']);
        $this->assertSame(1, $report['failed_subjects']);
        $this->assertEquals(0.0, $report['grade_point']);
    }
}
