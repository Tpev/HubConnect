<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealRoomMessage extends Model
{
    protected $fillable = [
        'deal_room_id',
        'company_id',
        'user_id',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(DealRoom::class, 'deal_room_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
