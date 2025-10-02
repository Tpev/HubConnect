<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyIntent extends Model
{
    protected $fillable = [
        'company_id','intent_type','status','payload','effective_from','effective_to',
    ];

    protected $casts = [
        'payload' => 'array',
        'effective_from' => 'datetime',
        'effective_to'   => 'datetime',
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
