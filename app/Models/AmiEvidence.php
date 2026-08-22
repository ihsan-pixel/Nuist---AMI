<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmiEvidence extends Model
{
    protected $table = 'ami_evidences';

    protected $fillable = [
        'ami_response_id',
        'title',
        'url',
        'description',
        'sort_order',
        'created_by',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(AmiResponse::class, 'ami_response_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
