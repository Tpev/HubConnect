<?php

namespace App\Actions\Fortify;

use App\Models\Company; // ← your Team-extended model
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => $this->passwordRules(),
            'company_name'  => ['required', 'string', 'max:255'],
            'company_type'  => ['required', 'in:manufacturer,distributor,both'],
            'terms'         => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name'     => $input['name'],
                'email'    => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // === create company team with unique slug ===
            $base = Str::slug($input['company_name']);
            $slug = $base; $i = 1;
            while (Company::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }

            /** @var \App\Models\Company $team */
            $team = $user->ownedTeams()->create([
                'name'          => $input['company_name'],
                'slug'          => $slug,
                'company_type'  => $input['company_type'],
                'personal_team' => false,
            ]);

            // ensure membership row exists + set current team
            if (! $user->teams()->where('teams.id', $team->id)->exists()) {
                $user->teams()->attach($team->id, ['role' => 'admin']);
            }
            $user->forceFill(['current_team_id' => $team->id])->save();

            return $user;
        });
    }
}
