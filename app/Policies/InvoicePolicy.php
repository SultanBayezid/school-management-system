<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // students/parents see only their own via scoping
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return match (true) {
            $user->isAdmin() || $user->isTeacher() => true,
            $user->isStudent() => $user->student?->id === $invoice->student_id,
            $user->isParent()  => $user->children->contains($invoice->student_id),
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }
}
