<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\KycSubmission; // Individual KYC model
use App\Notifications\KycApproved;
use App\Notifications\KycRejected;
use App\Notifications\IndividualKycApproved;
use App\Notifications\IndividualKycRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycReviewController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (!(Auth::user()?->is_admin ?? false)) abort(403);
    }

    /**
     * Companies KYC index (existing)
     */
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

    /**
     * Approve a company/team KYC
     */
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

    /**
     * Reject a company/team KYC
     */
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

    /**
     * Individuals KYC index (NEW)
     */
    public function individuals(Request $request)
    {
        $this->ensureAdmin();

        $status = $request->input('status', 'pending_review');

        $subs = KycSubmission::query()
            ->with(['user:id,name,email'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return view('admin.kyc.individuals.index', compact('subs','status'));
    }

    /**
     * Approve an individual's KYC (NEW)
     */
    public function approveIndividual(KycSubmission $submission)
    {
        $this->ensureAdmin();

        if ($submission->status !== 'pending_review') {
            return back()->with('error', 'Submission must be pending_review.');
        }

        $submission->status = 'approved';
        $submission->reviewed_at = now();
        $submission->reviewed_by = Auth::id();
        $submission->rejected_reason = null;
        $submission->save();

        if (class_exists(IndividualKycApproved::class)) {
            $submission->user?->notify(new IndividualKycApproved($submission));
        }

        return back()->with('status','approved');
    }

    /**
     * Reject an individual's KYC (NEW)
     */
    public function rejectIndividual(Request $request, KycSubmission $submission)
    {
        $this->ensureAdmin();

        $data = $request->validate(['reason' => ['required','string','max:2000']]);

        $submission->status = 'rejected';
        $submission->reviewed_at = now();
        $submission->reviewed_by = Auth::id();
        $submission->rejected_reason = $data['reason'];
        $submission->save();

        if (class_exists(IndividualKycRejected::class)) {
            $submission->user?->notify(new IndividualKycRejected($submission, $data['reason']));
        }

        return back()->with('status','rejected');
    }
}
