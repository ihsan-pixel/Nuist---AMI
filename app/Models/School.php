<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'scod',
        'name',
        'education_level',
        'district',
        'address',
        'email',
        'phone',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AmiSchoolAssignment::class);
    }
}
