<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoCountry extends Model
{
    protected $fillable = ['iso2','iso3','name'];

    public function subdivisions(){ return $this->hasMany(GeoSubdivision::class); }
    public function places(){ return $this->hasMany(GeoPlace::class); }
}
