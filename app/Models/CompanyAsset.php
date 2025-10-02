<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAsset extends Model
{
    protected $fillable = ['company_id','type','title','url'];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
