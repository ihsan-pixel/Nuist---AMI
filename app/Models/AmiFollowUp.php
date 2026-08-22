<?php

namespace App\Models;

use App\Enums\AmiFollowUpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiFollowUp extends Model
{
    protected $table = 'ami_follow_ups';

    protected $fillable = [
        'ami_finding_id',
        'ami_school_assignment_id',
        'school_id',
        'action_plan',
        'status',
        'submitted_at',
        'verified_at',
        'verified_by',
        'verifier_note',
    ];

    protected function casts(): array
    {
        return [
            'status' => AmiFollowUpStatus::class,
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function finding(): BelongsTo { return $this->belongsTo(AmiFinding::class, 'ami_finding_id'); }
    public function assignment(): BelongsTo { return $this->belongsTo(AmiSchoolAssignment::class, 'ami_school_assignment_id'); }
    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class, 'verified_by'); }
    public function evidences(): HasMany { return $this->hasMany(AmiFollowUpEvidence::class, 'ami_follow_up_id'); }
}
