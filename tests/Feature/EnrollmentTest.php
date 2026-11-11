<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_be_enrolled_twice_in_same_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $year = AcademicYear::create([
            'name' => '2026', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_current' => true,
        ]);
        $classOne = SchoolClass::create(['name' => 'Class One', 'level' => 1]);
        $classTwo = SchoolClass::create(['name' => 'Class Two', 'level' => 2]);
        $student = Student::factory()->create();

        $this->actingAs($admin)->post('/enrollments', [
            'student_id' => $student->id,
            'school_class_id' => $classOne->id,
            'academic_year_id' => $year->id,
            'status' => 'active',
        ])->assertRedirect();

        $response = $this->actingAs($admin)->post('/enrollments', [
            'student_id' => $student->id,
            'school_class_id' => $classTwo->id,
            'academic_year_id' => $year->id,
            'status' => 'active',
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(1, $student->enrollments()->count());
    }
}
