<?php

namespace App\Policies;

use App\Models\AmiFinding;
use App\Models\User;

class AuditorFindingPolicy
{
    public function manage(User $user, AmiFinding $finding): bool
    {
        return $user->role === 'auditor'
            && (int) $finding->auditor_id === (int) $user->id
            && $finding->assignment->auditor_id === $user->id
            && $finding->assignment->audit_status !== 'completed';
    }
}
