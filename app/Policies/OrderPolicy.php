<?php

namespace App\Policies;

use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function reports(User $user): bool
    {
        return $user->isAdmin();
    }
}
