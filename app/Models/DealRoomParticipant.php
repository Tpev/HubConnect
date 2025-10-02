<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealRoomParticipant extends Model
{
    protected $fillable = [
        'deal_room_id',
        'company_id',
        'last_seen_at',
        'last_read_at',
        'last_typing_at',
        'notify_mode',
        'email_cooldown_minutes',
        'last_email_at',
        'last_daily_email_at',
    ];

    protected $casts = [
        'last_seen_at'        => 'datetime',
        'last_read_at'        => 'datetime',
        'last_typing_at'      => 'datetime',
        'last_email_at'       => 'datetime',
        'last_daily_email_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(DealRoom::class, 'deal_room_id');
    }

    public function company()
    {
        return $this->belongsTo(Team::class, 'company_id');
    }
}
