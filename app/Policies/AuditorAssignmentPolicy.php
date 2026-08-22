<?php

namespace App\Policies;

use App\Models\AmiSchoolAssignment;
use App\Models\User;

class AuditorAssignmentPolicy
{
    public function view(User $user, AmiSchoolAssignment $assignment): bool
    {
        return $user->role === 'auditor' && (int) $assignment->auditor_id === (int) $user->id;
    }

    public function manage(User $user, AmiSchoolAssignment $assignment): bool
    {
        return $this->view($user, $assignment) && $assignment->audit_status !== 'completed';
    }
}
