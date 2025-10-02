<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\CompStructure;
use App\Enums\OpeningType;
use App\Enums\ScreeningPolicy;

class Opening extends Model
{
protected $casts = [
    'specialty_ids' => 'array',
    'territory_ids' => 'array',
	'comp_structure' => CompStructure::class,
    'opening_type'   => OpeningType::class,
	'screening_policy'=> ScreeningPolicy::class,
    'screening_rules' => 'array',
];

public function team(){ return $this->belongsTo(\App\Models\Team::class); }
public function applications(){ return $this->hasMany(\App\Models\Application::class); }
public function scenarioPack(){ return $this->belongsTo(\App\Models\RoleplayScenarioPack::class, 'roleplay_scenario_pack_id'); }

}
