<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'team_id',
        'opening_id',
        'candidate_name',
        'email',
        'phone',
        'location',
        'cover_letter',
        'cv_path',
        'status',
        'score',
        'invited_at',
        'invite_token',
        'completed_at',
        'roleplay_score',
		   'name',  
    ];

    protected $casts = [
        'invited_at'     => 'datetime',
        'completed_at'   => 'datetime',
        'score'          => 'decimal:2',
        'roleplay_score' => 'decimal:2',
    ];
    // optional: relationships
    public function opening()
    {
        return $this->belongsTo(Opening::class);
    }


public function candidate(){ return $this->belongsTo(\App\Models\User::class, 'candidate_user_id'); }
public function evaluations(){ return $this->hasMany(\App\Models\RoleplayEvaluation::class); }

}
