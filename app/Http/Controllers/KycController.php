<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function gate()
    {
        $team = Auth::user()?->currentTeam;
        abort_unless($team, 403);

        return view('kyc.gate', ['team' => $team]);
    }
}
