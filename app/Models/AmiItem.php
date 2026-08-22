<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiItem extends Model
{
    protected $fillable = [
        'ami_standard_id',
        'code',
        'number',
        'title',
        'description',
        'sort_order',
    ];

    public function standard(): BelongsTo
    {
        return $this->belongsTo(AmiStandard::class, 'ami_standard_id');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(AmiIndicator::class, 'ami_item_id');
    }
}
