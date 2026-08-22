<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiResponse extends Model
{
    protected $table = 'ami_responses';

    protected $fillable = [
        'ami_school_assignment_id',
        'ami_indicator_id',
        'self_score',
        'answer',
        'note',
        'status',
        'saved_at',
    ];

    protected function casts(): array
    {
        return [
            'self_score' => 'integer',
            'saved_at' => 'datetime',
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

    public function evidences(): HasMany
    {
        return $this->hasMany(AmiEvidence::class, 'ami_response_id');
    }
}
