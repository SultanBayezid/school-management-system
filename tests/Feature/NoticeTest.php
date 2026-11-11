<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_a_notice(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/notices', [
            'title' => 'School Closed Tomorrow',
            'body' => 'The school remains closed tomorrow due to weather conditions.',
            'audience' => 'all',
        ]);

        $response->assertRedirect(route('notices.index'));
        $this->assertDatabaseHas('notices', ['title' => 'School Closed Tomorrow', 'is_active' => true]);
    }

    public function test_audience_scoping_hides_notices_from_other_roles(): void
    {
        $teacherNotice = Notice::create([
            'title' => 'Staff Meeting', 'body' => 'Meeting at noon.', 'audience' => 'teachers', 'is_active' => true,
        ]);
        Notice::create([
            'title' => 'Holiday', 'body' => 'No school.', 'audience' => 'students', 'is_active' => true,
        ]);

        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($teacher)->get('/notices')
            ->assertOk()->assertSee('Staff Meeting')->assertDontSee('Holiday');

        $this->actingAs($student)->get('/notices')
            ->assertOk()->assertSee('Holiday')->assertDontSee('Staff Meeting');
    }

    public function test_inactive_notices_are_hidden(): void
    {
        Notice::create([
            'title' => 'Hidden Notice', 'body' => 'x', 'audience' => 'all', 'is_active' => false,
        ]);

        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->get('/notices')->assertOk()->assertDontSee('Hidden Notice');
    }
}
