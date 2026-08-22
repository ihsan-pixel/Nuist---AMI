<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'school_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function auditorAssignments(): HasMany
    {
        return $this->hasMany(AmiSchoolAssignment::class, 'auditor_id');
    }

    public function amiAssessments(): HasMany
    {
        return $this->hasMany(AmiAssessment::class, 'auditor_id');
    }

    public function amiFindings(): HasMany
    {
        return $this->hasMany(AmiFinding::class, 'auditor_id');
    }

    public function verifiedFollowUps(): HasMany
    {
        return $this->hasMany(AmiFollowUp::class, 'verified_by');
    }
}
