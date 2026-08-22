<?php

namespace App\Models;

use App\Enums\AmiAssessmentRating;
use App\Enums\AuditorAssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmiAssessment extends Model
{
    protected $fillable = [
        'ami_school_assignment_id',
        'ami_indicator_id',
        'auditor_id',
        'status',
        'rating',
        'score',
        'auditor_note',
        'verification_methods',
        'verification_note',
        'assessed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AuditorAssessmentStatus::class,
            'rating' => AmiAssessmentRating::class,
            'score' => 'decimal:2',
            'verification_methods' => 'array',
            'assessed_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AmiSchoolAssignment::class, 'ami_school_assignment_id');
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(AmiIndicator::class, 'ami_indicator_id');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
