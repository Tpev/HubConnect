<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealRoomFile extends Model
{
    protected $table = 'deal_room_files';

    protected $fillable = [
        'room_id',
        'path',
        'name',
        'type',
        'size',
        'uploaded_by',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(DealRoom::class, 'room_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    public function getIconAttribute(): string
    {
        $t = strtolower((string) $this->type);
        if (str_contains($t, 'pdf')) return 'pdf';
        if (str_contains($t, 'image') || preg_match('/\.(png|jpe?g|gif|webp|svg)$/i', $this->name)) return 'image';
        if (str_contains($t, 'excel') || preg_match('/\.(xlsx?|csv)$/i', $this->name)) return 'xls';
        if (preg_match('/\.(docx?)$/i', $this->name)) return 'doc';
        return 'file';
    }
}
