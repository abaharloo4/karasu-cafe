<?php

namespace App\Policies;

use App\Models\User;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function use(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }
}
