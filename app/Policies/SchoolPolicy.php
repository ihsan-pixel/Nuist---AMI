<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'pengurus', 'sekolah'], true);
    }

    public function view(User $user, School $school): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'pengurus') {
            return true;
        }

        return $user->role === 'sekolah' && (int) $user->school_id === (int) $school->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user, School $school): bool
    {
        return $user->role === 'super_admin' || ($user->role === 'sekolah' && (int) $user->school_id === (int) $school->id);
    }

    public function delete(User $user, School $school): bool
    {
        return $user->role === 'super_admin';
    }
}
