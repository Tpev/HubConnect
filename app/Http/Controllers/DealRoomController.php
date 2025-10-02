<?php

namespace App\Http\Controllers;

use App\Models\DealRoom;
use Illuminate\Support\Facades\Auth;

class DealRoomController extends Controller
{
    public function index()
    {
        $companyId = $this->currentCompanyId();
        abort_unless($companyId, 403);

        $rooms = DealRoom::query()
            ->forCompany($companyId)
            ->with(['companySmall','companyLarge','participants'])
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1); // last message for preview
            }])
            ->orderByDesc('updated_at')
            ->get();

        return view('deal-rooms.index', compact('companyId', 'rooms'));
    }

    protected function currentCompanyId(): ?int
    {
        $user = Auth::user();
        if (!$user) return null;

        if (method_exists($user, 'currentTeam') && $user->currentTeam) {
            return (int) $user->currentTeam->id;
        }
        if (method_exists($user, 'ownedTeams')) {
            $owned = $user->ownedTeams()->first();
            if ($owned) return (int) $owned->id;
        }
        if (method_exists($user, 'teams')) {
            $any = $user->teams()->first();
            if ($any) return (int) $any->id;
        }
        if (isset($user->team_id) && $user->team_id) {
            return (int) $user->team_id;
        }
        if (isset($user->company_id) && $user->company_id) {
            return (int) $user->company_id;
        }

        return null;
    }
}
