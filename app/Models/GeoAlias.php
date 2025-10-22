<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoAlias extends Model
{
    protected $fillable = ['entity_type','entity_id','alias'];
}
