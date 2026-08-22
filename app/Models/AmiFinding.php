<?php

namespace App\Models;

use App\Enums\AuditorFindingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AmiFinding extends Model
{
    protected $table = 'ami_findings';

    protected $fillable = [
        'ami_school_assignment_id',
        'ami_indicator_id',
        'auditor_id',
        'type',
        'title',
        'description',
        'recommendation',
        'status',
    ];

    public function assignment(): BelongsTo { return $this->belongsTo(AmiSchoolAssignment::class, 'ami_school_assignment_id'); }
    public function indicator(): BelongsTo { return $this->belongsTo(AmiIndicator::class, 'ami_indicator_id'); }
    public function auditor(): BelongsTo { return $this->belongsTo(User::class, 'auditor_id'); }

    public function followUp(): HasOne
    {
        return $this->hasOne(AmiFollowUp::class, 'ami_finding_id');
    }

    public function requiresFollowUp(): bool
    {
        return AuditorFindingType::tryFrom($this->type)?->requiresFollowUp() ?? false;
    }
}
