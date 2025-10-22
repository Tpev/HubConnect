<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycSubmission extends Model
{
    protected $fillable = [
        'user_id',

        // Status
        'status',            // draft | pending_review | approved | rejected
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'rejected_reason',

        // Minimal KYC info (no documents)
        'full_name',
        'country',
        'region',
        'city',
        'phone',

        // Optional notes by user
        'notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
