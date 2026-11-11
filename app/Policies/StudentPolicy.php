<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Student $student): bool
    {
        return match (true) {
            $user->isAdmin(), $user->isTeacher() => true,
            $user->isStudent() => $user->student?->id === $student->id,
            $user->isParent()  => $user->children->contains($student->id),
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }
}
