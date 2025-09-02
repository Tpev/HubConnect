<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReimbursementCode extends Model
{
       protected $fillable = [
        'device_id',
        'system',        // 'CPT' | 'HCPCS' | 'ICD10' etc.
        'code',
        'description',
    ];
}
