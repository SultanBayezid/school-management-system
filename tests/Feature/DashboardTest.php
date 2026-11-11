<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_shows_real_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Teacher::factory()->count(3)->create();
        Student::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Active Students')
            ->assertSee('Teachers')
            ->assertSeeInOrder(['Active Students', '5'], false)
            ->assertSee('3');
    }

    public function test_student_dashboard_renders_own_profile(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()->assertSee($student->full_name());
    }
}
