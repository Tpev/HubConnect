<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return $user->belongsToTeam($company);
    }

    public function update(User $user, Company $company): bool
    {
        return $user->belongsToTeam($company) || $company->user_id === $user->id;
    }
}
