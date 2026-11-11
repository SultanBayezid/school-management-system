<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_issues_a_token(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_wrong_credentials_are_rejected(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@school.test',
            'password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    public function test_protected_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/students')->assertUnauthorized();
        $this->getJson('/api/v1/stats')->assertUnauthorized();
    }

    public function test_admin_can_list_students(): void
    {
        $admin = Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        Student::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/students');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'admission_no', 'name']],
                'links', 'meta',
            ]);
    }

    public function test_students_can_only_view_their_own_record(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $own = Student::factory()->create(['user_id' => $user->id]);
        $other = Student::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/students/{$own->id}")->assertOk();
        $this->getJson("/api/v1/students/{$other->id}")->assertForbidden();
    }

    public function test_student_list_is_searchable(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        Student::factory()->create(['first_name' => 'Zephyr', 'last_name' => 'Unique']);
        Student::factory()->create(['first_name' => 'Common', 'last_name' => 'Name']);

        $response = $this->getJson('/api/v1/students?search=Zephyr');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_me_endpoint_returns_authenticated_user(): void
    {
        $user = Sanctum::actingAs(User::factory()->create(['role' => 'teacher']));

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJson(['role' => 'teacher', 'email' => $user->email]);
    }
}
