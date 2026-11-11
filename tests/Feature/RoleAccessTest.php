<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function adminOnlyUrls(): array
    {
        return [
            ['/users'], ['/settings'], ['/students/create'], ['/teachers/create'],
            ['/exams/create'], ['/classes'], ['/subjects'], ['/academic-years'],
            ['/enrollments'], ['/fee-types'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminOnlyUrls')]
    public function test_students_cannot_access_admin_pages(string $url): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)->get($url)->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminOnlyUrls')]
    public function test_teachers_cannot_access_admin_pages(string $url): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)->get($url)->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminOnlyUrls')]
    public function test_admin_can_access_admin_pages(string $url): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get($url)->assertOk();
    }

    public function test_every_authenticated_role_can_view_notices(): void
    {
        foreach (['admin', 'teacher', 'student', 'parent'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/notices')->assertOk();
        }
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
