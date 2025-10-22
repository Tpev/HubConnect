<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoSubdivision extends Model
{
    protected $fillable = ['country_id','iso_3166_2','name'];

    public function country(){ return $this->belongsTo(GeoCountry::class); }
    public function places(){ return $this->hasMany(GeoPlace::class); }
}
