<?php

namespace App\Http\Controllers;

use App\Models\Team;

class CompanyController extends Controller
{
    public function show(Team $team)
    {
        return view('companies.show', ['team' => $team]);
    }
}
