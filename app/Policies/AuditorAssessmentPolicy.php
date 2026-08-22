<?php

namespace App\Policies;

use App\Models\AmiAssessment;
use App\Models\User;

class AuditorAssessmentPolicy
{
    public function manage(User $user, AmiAssessment $assessment): bool
    {
        return $user->role === 'auditor'
            && (int) $assessment->auditor_id === (int) $user->id
            && $assessment->assignment->auditor_id === $user->id
            && $assessment->assignment->audit_status !== 'completed';
    }
}
