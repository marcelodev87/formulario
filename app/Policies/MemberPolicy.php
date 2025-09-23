<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->institution !== null;
    }

    public function view(User $user, Member $member): bool
    {
        return $this->belongsToUserInstitution($user, $member);
    }

    public function create(User $user): bool
    {
        return $user->institution !== null;
    }

    public function update(User $user, Member $member): bool
    {
        return $this->belongsToUserInstitution($user, $member);
    }

    public function delete(User $user, Member $member): bool
    {
        return $this->belongsToUserInstitution($user, $member);
    }

    public function restore(User $user, Member $member): bool
    {
        return false;
    }

    public function forceDelete(User $user, Member $member): bool
    {
        return false;
    }

    private function belongsToUserInstitution(User $user, Member $member): bool
    {
        $institutionId = $user->institution?->id;

        return $institutionId !== null && $member->institution_id === $institutionId;
    }
}
