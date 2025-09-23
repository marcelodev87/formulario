<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->institution !== null;
    }

    public function view(User $user, Institution $institution): bool
    {
        return $institution->owner_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->institution === null;
    }

    public function update(User $user, Institution $institution): bool
    {
        return $institution->owner_user_id === $user->id;
    }

    public function delete(User $user, Institution $institution): bool
    {
        return false;
    }

    public function restore(User $user, Institution $institution): bool
    {
        return false;
    }

    public function forceDelete(User $user, Institution $institution): bool
    {
        return false;
    }
}
