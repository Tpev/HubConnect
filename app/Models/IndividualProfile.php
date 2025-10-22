<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'location',
        'years_experience',
        'skills',
        'links',
        'cv_path',
        'visibility',
        'completed_at',
    ];

    protected $casts = [
        'skills'       => 'array',
        'links'        => 'array',
        'completed_at' => 'datetime',
        'years_experience' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsCompleteAttribute(): bool
    {
        return !is_null($this->completed_at)
            && filled($this->headline)
            && filled($this->location);
    }
}
