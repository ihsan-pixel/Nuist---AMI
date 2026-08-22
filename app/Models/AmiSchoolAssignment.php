<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiSchoolAssignment extends Model
{
    protected $fillable = [
        'ami_period_id',
        'school_id',
        'auditor_id',
        'audit_status',
        'audit_started_at',
        'audit_completed_at',
        'audit_completed_by',
        'status',
        'started_at',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'audit_status' => 'string',
            'audit_started_at' => 'datetime',
            'audit_completed_at' => 'datetime',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AmiPeriod::class, 'ami_period_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function auditCompletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audit_completed_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AmiResponse::class, 'ami_school_assignment_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(AmiAssessment::class, 'ami_school_assignment_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(AmiFinding::class, 'ami_school_assignment_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(AmiFollowUp::class, 'ami_school_assignment_id');
    }

    public function scopeForAuditor($query, int $auditorId)
    {
        return $query->where('auditor_id', $auditorId);
    }
}
