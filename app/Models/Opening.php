<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opening extends Model
{
protected $casts = [
    'specialty_ids' => 'array',
    'territory_ids' => 'array',
];

public function team(){ return $this->belongsTo(\App\Models\Team::class); }
public function applications(){ return $this->hasMany(\App\Models\Application::class); }
public function scenarioPack(){ return $this->belongsTo(\App\Models\RoleplayScenarioPack::class, 'roleplay_scenario_pack_id'); }

}
