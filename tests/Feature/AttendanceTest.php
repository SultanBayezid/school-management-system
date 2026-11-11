<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private SchoolClass $class;
    private Section $section;
    private Student $studentOne;
    private Student $studentTwo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->class = SchoolClass::create(['name' => 'Class One', 'level' => 1]);
        $this->section = Section::create([
            'school_class_id' => $this->class->id, 'name' => 'Section A', 'capacity' => 40,
        ]);
        $this->studentOne = Student::factory()->create();
        $this->studentTwo = Student::factory()->create();

        AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
    }

    public function test_teacher_can_mark_daily_attendance(): void
    {
        $date = today()->toDateString();

        $response = $this->actingAs($this->teacher)->post('/attendance', [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'attendance_date' => $date,
            'statuses' => [
                $this->studentOne->id => 'present',
                $this->studentTwo->id => 'absent',
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->studentOne->id, 'status' => 'present',
        ]);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->studentTwo->id, 'status' => 'absent',
        ]);
    }

    public function test_marking_same_day_twice_updates_instead_of_duplicating(): void
    {
        $date = today()->toDateString();
        $payload = [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'attendance_date' => $date,
            'statuses' => [$this->studentOne->id => 'present'],
        ];

        $this->actingAs($this->teacher)->post('/attendance', $payload);
        $payload['statuses'][$this->studentOne->id] = 'late';
        $this->actingAs($this->teacher)->post('/attendance', $payload);

        $this->assertSame(1, Attendance::where('student_id', $this->studentOne->id)->count());
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->studentOne->id, 'status' => 'late',
        ]);
    }

    public function test_future_dates_are_rejected(): void
    {
        $response = $this->actingAs($this->teacher)->post('/attendance', [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'attendance_date' => now()->addDay()->toDateString(),
            'statuses' => [$this->studentOne->id => 'present'],
        ]);

        $response->assertSessionHasErrors('attendance_date');
    }

    public function test_invalid_status_is_rejected(): void
    {
        $response = $this->actingAs($this->teacher)->post('/attendance', [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'attendance_date' => today()->toDateString(),
            'statuses' => [$this->studentOne->id => 'excused'],
        ]);

        $response->assertSessionHasErrors('statuses.*');
    }

    public function test_students_cannot_mark_attendance(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->post('/attendance', [
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'attendance_date' => today()->toDateString(),
            'statuses' => [$this->studentOne->id => 'present'],
        ]);

        $response->assertForbidden();
    }

    public function test_monthly_report_renders(): void
    {
        Attendance::create([
            'student_id' => $this->studentOne->id,
            'school_class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'academic_year_id' => 1,
            'attendance_date' => today()->toDateString(),
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacher)
            ->get('/attendance/report?month='.now()->format('Y-m'));

        $response->assertOk()->assertSee($this->studentOne->full_name());
    }
}
