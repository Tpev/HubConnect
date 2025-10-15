<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealRoomFileDownload extends Model
{
    protected $fillable = [
        'file_id','user_id','company_id','ip','user_agent',
    ];

    public function file()
    {
        return $this->belongsTo(DealRoomFile::class, 'file_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
