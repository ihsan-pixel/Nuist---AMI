<?php

namespace App\Enums;

enum AmiAssessmentRating: string
{
    case KURANG = 'kurang';
    case CUKUP_BAIK = 'cukup_baik';
    case BAIK = 'baik';
    case SANGAT_BAIK = 'sangat_baik';
}
