<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleplayEvaluation extends Model
{
protected $casts = [
    'rubric' => 'array',
    'metadata' => 'array',
];

public function application(){ return $this->belongsTo(\App\Models\Application::class); }
public function pack(){ return $this->belongsTo(\App\Models\RoleplayScenarioPack::class, 'scenario_pack_id'); }

}
