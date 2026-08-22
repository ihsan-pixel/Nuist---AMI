<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmiFollowUpEvidence extends Model
{
    protected $table = 'ami_follow_up_evidences';

    protected $fillable = ['ami_follow_up_id', 'title', 'url', 'note'];

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(AmiFollowUp::class, 'ami_follow_up_id');
    }
}
