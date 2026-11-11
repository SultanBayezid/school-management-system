<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamMarksTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Exam $exam;
    private Subject $subject;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $class = SchoolClass::create(['name' => 'Class One', 'level' => 1]);
        $year = AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
        $this->subject = Subject::create(['name' => 'Math', 'code' => 'M1']);
        $class->subjects()->attach($this->subject->id);

        $this->exam = Exam::create([
            'name' => 'Unit Test', 'academic_year_id' => $year->id, 'school_class_id' => $class->id,
        ]);

        $this->student = Student::factory()->create();
        $class->enrollments()->create([
            'student_id' => $this->student->id,
            'academic_year_id' => $year->id,
            'roll_no' => 1,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_an_exam(): void
    {
        $response = $this->actingAs($this->admin)->post('/exams', [
            'name' => 'Final Examination',
            'academic_year_id' => 1,
            'school_class_id' => 1,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-15',
        ]);

        $response->assertRedirect(route('exams.index'));
        $this->assertDatabaseHas('exams', ['name' => 'Final Examination']);
    }

    public function test_teacher_can_enter_marks(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->post("/exams/{$this->exam->id}/marks", [
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'marks' => [$this->student->id => 87],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('marks', [
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
            'marks_obtained' => 87,
        ]);
    }

    public function test_marks_cannot_exceed_maximum(): void
    {
        $response = $this->actingAs($this->admin)->post("/exams/{$this->exam->id}/marks", [
            'subject_id' => $this->subject->id,
            'max_marks' => 50,
            'marks' => [$this->student->id => 60],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('marks', [
            'exam_id' => $this->exam->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_entering_marks_twice_updates_existing_record(): void
    {
        $payload = [
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'marks' => [$this->student->id => 50],
        ];

        $this->actingAs($this->admin)->post("/exams/{$this->exam->id}/marks", $payload);
        $payload['marks'][$this->student->id] = 75;
        $this->actingAs($this->admin)->post("/exams/{$this->exam->id}/marks", $payload);

        $this->assertSame(1, $this->exam->marks()->count());
        $this->assertDatabaseHas('marks', ['marks_obtained' => 75]);
    }

    public function test_report_card_page_shows_grade(): void
    {
        $this->seed(\Database\Seeders\GradeBandSeeder::class);

        $this->exam->marks()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'marks_obtained' => 95,
            'max_marks' => 100,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/exams/{$this->exam->id}/report-card/{$this->student->id}");

        $response->assertOk()
            ->assertSee('Report Card')
            ->assertSee('A+')
            ->assertSee('95.00');
    }
}
