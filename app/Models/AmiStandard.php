<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiStandard extends Model
{
    protected $fillable = [
        'ami_period_id',
        'code',
        'name',
        'description',
        'sort_order',
        'weight',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AmiPeriod::class, 'ami_period_id');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(AmiIndicator::class, 'ami_standard_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AmiItem::class, 'ami_standard_id')->orderBy('sort_order');
    }
}
