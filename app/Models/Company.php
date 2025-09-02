<?php
namespace App\Models;

use Laravel\Jetstream\Team;

class Company extends Team
{
    protected $table = 'teams';

    protected $casts = [
        'company_type' => 'string',
    ];
}
