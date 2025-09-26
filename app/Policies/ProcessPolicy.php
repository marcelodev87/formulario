<?php

namespace App\Policies;

use App\Models\Process;
use App\Models\User;

class ProcessPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->institution !== null;
    }

    public function view(User $user, Process $process): bool
    {
        return $this->belongsToUserInstitution($user, $process);
    }

    public function create(User $user): bool
    {
        return $user->institution !== null && $user->institution->owner_user_id === $user->id;
    }

    public function update(User $user, Process $process): bool
    {
        return $this->belongsToUserInstitution($user, $process);
    }

    public function delete(User $user, Process $process): bool
    {
        return false;
    }

    public function restore(User $user, Process $process): bool
    {
        return false;
    }

    public function forceDelete(User $user, Process $process): bool
    {
        return false;
    }

    private function belongsToUserInstitution(User $user, Process $process): bool
    {
        $institution = $user->institution;

        return $institution !== null && $process->institution_id === $institution->id;
    }
}
