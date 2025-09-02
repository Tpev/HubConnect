<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceDocument extends Model
{
   protected $fillable = [
        'device_id',
        'type',          // brochure|ifus|training|evidence
        'path',
        'original_name',
    ];
}
