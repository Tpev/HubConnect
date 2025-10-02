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
		           // Newly added fields
        'years_total','years_med_device','candidate_specialties','state','travel_percent_max','overnight_ok',
        'driver_license','opening_type_accepts','comp_structure_accepts','expected_base','expected_ote',
        'cold_outreach_ok','work_auth','start_date','has_noncompete_conflict','background_check_ok',
        'candidate_profile','screening_result','screening_pass','screening_fail_count','screening_flag_count',
        'auto_rejected_at',
    ];

    protected $casts = [
        'candidate_specialties'   => 'array',
        'opening_type_accepts'    => 'array',
        'comp_structure_accepts'  => 'array',
        'candidate_profile'       => 'array',
        'screening_result'        => 'array',
		 'screening_answers'       => 'array',

        'overnight_ok'            => 'boolean',
        'driver_license'          => 'boolean',
        'cold_outreach_ok'        => 'boolean',
        'has_noncompete_conflict' => 'boolean',
        'background_check_ok'     => 'boolean',
        'screening_pass'          => 'boolean',

        'start_date'              => 'date',
        'invited_at'              => 'datetime',
        'completed_at'            => 'datetime',
        'auto_rejected_at'        => 'datetime',
        'score'          => 'decimal:2',
        'roleplay_score' => 'decimal:2',
		        'screening_fail_count'  => 'integer',
        'screening_flag_count'  => 'integer',
        'screening_overridden'  => 'boolean',
        'screening_answers'     => 'array',
    ];
    // optional: relationships
    public function opening()
    {
        return $this->belongsTo(Opening::class);
    }


public function candidate(){ return $this->belongsTo(\App\Models\User::class, 'candidate_user_id'); }
public function evaluations(){ return $this->hasMany(\App\Models\RoleplayEvaluation::class); }

}
