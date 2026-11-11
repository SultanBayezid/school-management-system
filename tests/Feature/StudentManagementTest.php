<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_a_student(): void
    {
        $response = $this->actingAs($this->admin)->post('/students', [
            'admission_no' => 'ADM-2026-0001',
            'first_name' => 'Rahim',
            'last_name' => 'Uddin',
            'gender' => 'male',
            'guardian_name' => 'Karim Uddin',
            'guardian_phone' => '+8801711000000',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('students.show', Student::first()));
        $this->assertDatabaseHas('students', [
            'admission_no' => 'ADM-2026-0001',
            'first_name' => 'Rahim',
        ]);
    }

    public function test_admission_number_must_be_unique(): void
    {
        Student::factory()->create(['admission_no' => 'ADM-2026-0002']);

        $response = $this->actingAs($this->admin)->post('/students', [
            'admission_no' => 'ADM-2026-0002',
            'first_name' => 'Duplicate',
            'last_name' => 'Student',
        ]);

        $response->assertSessionHasErrors('admission_no');
        $this->assertSame(1, Student::count());
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this->actingAs($this->admin)->post('/students', []);

        $response->assertSessionHasErrors(['admission_no', 'first_name', 'last_name']);
    }

    public function test_admin_can_update_a_student(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($this->admin)->put("/students/{$student->id}", [
            'admission_no' => $student->admission_no,
            'first_name' => 'Updated',
            'last_name' => $student->last_name,
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'first_name' => 'Updated']);
    }

    public function test_admin_can_delete_a_student(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($this->admin)->delete("/students/{$student->id}");

        $this->assertModelMissing($student);
    }

    public function test_teacher_can_list_students(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        Student::factory()->count(3)->create();

        $response = $this->actingAs($teacher)->get('/students');

        $response->assertOk();
    }

    public function test_student_search_finds_by_admission_no(): void
    {
        Student::factory()->create(['admission_no' => 'ADM-SEARCH-1', 'first_name' => 'Needle']);
        Student::factory()->create(['admission_no' => 'ADM-SEARCH-2', 'first_name' => 'Other']);

        $response = $this->actingAs($this->admin)->get('/students?search=Needle');

        $response->assertOk()->assertSee('Needle')->assertDontSee('Other');
    }
}
