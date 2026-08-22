<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiIndicator extends Model
{
    protected $fillable = [
        'ami_item_id',
        'ami_standard_id',
        'code',
        'title',
        'statement',
        'operational_definition',
        'description',
        'explanation',
        'fulfillment_criteria',
        'snp_reference',
        'guidance',
        'evidence_guidance',
        'rubric_kurang',
        'rubric_cukup_baik',
        'rubric_baik',
        'rubric_sangat_baik',
        'weight',
        'max_score',
        'sort_order',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'max_score' => 'integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(AmiStandard::class, 'ami_standard_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(AmiItem::class, 'ami_item_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AmiResponse::class, 'ami_indicator_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(AmiAssessment::class, 'ami_indicator_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(AmiFinding::class, 'ami_indicator_id');
    }
}
