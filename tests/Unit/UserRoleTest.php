<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_role_helpers(): void
    {
        $admin = new User();
        $admin->role = 'admin';
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isTeacher());

        $teacher = new User();
        $teacher->role = 'teacher';
        $this->assertTrue($teacher->isTeacher());
        $this->assertFalse($teacher->isAdmin());

        $student = new User();
        $student->role = 'student';
        $this->assertTrue($student->isStudent());

        $parent = new User();
        $parent->role = 'parent';
        $this->assertTrue($parent->isParent());
    }

    public function test_role_labels(): void
    {
        $user = new User();
        $user->role = 'admin';
        $this->assertSame('Administrator', $user->roleLabel());

        $user->role = 'parent';
        $this->assertSame('Parent / Guardian', $user->roleLabel());

        $user->role = 'unknown';
        $this->assertSame('Unknown', $user->roleLabel());
    }
}
