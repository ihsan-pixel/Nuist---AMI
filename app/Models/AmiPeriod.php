<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiPeriod extends Model
{
    protected $fillable = [
        'name',
        'year',
        'start_date',
        'end_date',
        'submission_start_at',
        'submission_end_at',
        'review_start_at',
        'review_end_at',
        'status',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'submission_start_at' => 'datetime',
            'submission_end_at' => 'datetime',
            'review_start_at' => 'datetime',
            'review_end_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public static function normalizeDateTimeInput(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return str_replace('T', ' ', $value).':00';
    }

    public function standards(): HasMany
    {
        return $this->hasMany(AmiStandard::class, 'ami_period_id')->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AmiSchoolAssignment::class, 'ami_period_id');
    }

    public function hasStandard(AmiStandard $standard): bool
    {
        return (int) $standard->ami_period_id === (int) $this->id;
    }
}
