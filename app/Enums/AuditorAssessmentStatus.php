<?php

namespace App\Enums;

enum AuditorAssessmentStatus: string
{
    case CONFORM = 'conform';
    case PARTIALLY_CONFORM = 'partially_conform';
    case NON_CONFORM = 'non_conform';
}
