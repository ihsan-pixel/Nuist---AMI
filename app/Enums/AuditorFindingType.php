<?php

namespace App\Enums;

enum AuditorFindingType: string
{
    case OBSERVATION = 'observation';
    case MINOR = 'minor';
    case MAJOR = 'major';

    public function requiresFollowUp(): bool
    {
        return in_array($this, [self::MINOR, self::MAJOR], true);
    }
}
