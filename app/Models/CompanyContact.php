<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
    protected $fillable = ['company_id','name','title','email','phone','visibility','is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
