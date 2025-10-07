<?php
// app/Http/Controllers/Admin/KycReviewController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Notifications\KycApproved;
use App\Notifications\KycRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycReviewController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (!(Auth::user()?->is_admin ?? false)) abort(403);
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $status = $request->input('status', 'pending_review');

        $teams = Team::query()
            ->when($status, fn($q) => $q->where('kyc_status', $status))
            ->with([
                'owner:id,name,email',
                'users:id,name,email',
            ])
            ->withCount('users')
            ->orderByDesc('kyc_submitted_at')
            ->paginate(20);

        return view('admin.kyc.index', compact('teams','status'));
    }

    public function approve(Team $team)
    {
        $this->ensureAdmin();

        $team->kyc_status = 'approved';
        $team->kyc_verified_at = now();
        $team->kyc_reviewer_user_id = Auth::id();
        $team->save();

        $team->owner?->notify(new KycApproved($team));

        return back()->with('status','approved');
    }

    public function reject(Request $request, Team $team)
    {
        $this->ensureAdmin();

        $data = $request->validate(['reason' => ['required','string','max:2000']]);

        $team->kyc_status = 'rejected';
        $team->kyc_reviewer_user_id = Auth::id();
        $team->kyc_notes = $data['reason'];
        $team->save();

        $team->owner?->notify(new KycRejected($team, $data['reason']));

        return back()->with('status','rejected');
    }
}
