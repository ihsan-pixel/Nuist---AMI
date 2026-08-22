<?php

namespace App\Policies;

use App\Models\AmiFollowUp;
use App\Models\User;

class AmiFollowUpPolicy
{
    public function view(User $user, AmiFollowUp $followUp): bool
    {
        if ($user->role === 'sekolah') {
            return (int) $user->school_id === (int) $followUp->school_id
                && (int) $followUp->assignment->school_id === (int) $user->school_id;
        }

        if ($user->role === 'auditor') {
            return (int) $followUp->assignment->auditor_id === (int) $user->id;
        }

        return false;
    }

    public function manage(User $user, AmiFollowUp $followUp): bool
    {
        $status = is_object($followUp->status) ? $followUp->status->value : $followUp->status;

        return $user->role === 'sekolah'
            && $this->view($user, $followUp)
            && in_array($status, ['draft', 'needs_revision'], true);
    }

    public function verify(User $user, AmiFollowUp $followUp): bool
    {
        $status = is_object($followUp->status) ? $followUp->status->value : $followUp->status;

        return $user->role === 'auditor'
            && $this->view($user, $followUp)
            && $status === 'submitted';
    }
}
