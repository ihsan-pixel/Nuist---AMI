<?php

namespace App\Enums;

enum AmiFollowUpStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case NEEDS_REVISION = 'needs_revision';
    case ACCEPTED = 'accepted';
}
