<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatoryClearance extends Model
{
    protected $fillable = ['device_id','clearance_type','number','issue_date','link'];
    protected $casts = ['issue_date' => 'date'];
}

