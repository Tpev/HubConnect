<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteDevice extends Model
{
    protected $fillable = ['device_id','company_id'];

    public function device()  { return $this->belongsTo(Device::class); }
    public function company() { return $this->belongsTo(Company::class, 'company_id'); }
}
