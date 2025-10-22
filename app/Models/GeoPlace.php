<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoPlace extends Model
{
    protected $fillable = [
        'country_id','subdivision_id','ext_source','ext_id',
        'name','ascii_name','lat','lng','captured_at'
    ];

    public function country(){ return $this->belongsTo(GeoCountry::class); }
    public function subdivision(){ return $this->belongsTo(GeoSubdivision::class); }
}
