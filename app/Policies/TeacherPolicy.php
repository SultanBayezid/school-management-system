<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $user->isAdmin() || $user->isTeacher() || $teacher->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->isAdmin() || $teacher->user_id === $user->id;
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->isAdmin();
    }
}
