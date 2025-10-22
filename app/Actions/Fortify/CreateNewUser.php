<?php

namespace App\Actions\Fortify;

use App\Models\Company; // your Team-extended model
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'account_type'  => ['required', Rule::in(['company', 'individual'])],
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => $this->passwordRules(),

            // Company-only — validate only if present & required for company
            'company_name'  => ['sometimes','required_if:account_type,company','string','max:255'],
            'company_type'  => ['sometimes','required_if:account_type,company', Rule::in(['manufacturer','distributor','both'])],

            'terms'         => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            /** @var \App\Models\User $user */
            $user = User::create([
                'name'         => $input['name'],
                'email'        => $input['email'],
                'password'     => Hash::make($input['password']),
                'account_type' => $input['account_type'] ?? 'company',
            ]);

            if (($input['account_type'] ?? 'company') === 'company') {
                // Create company/Team as before
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

                if (! $user->teams()->where('teams.id', $team->id)->exists()) {
                    $user->teams()->attach($team->id, ['role' => 'admin']);
                }
                $user->forceFill(['current_team_id' => $team->id])->save();
            }

            // Individual: no team creation; redirect handled by your Register/LoginResponse
            return $user;
        });
    }
}
