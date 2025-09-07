<?php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\Opening;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ApplicantDrawer extends Component
{
    public int $applicationId;
    public int $openingId;

    public bool $show = true;

    public ?string $status = null;
    public ?float $score = null;

    public function mount(int $applicationId, int $openingId): void
    {
        $this->applicationId = $applicationId;
        $this->openingId     = $openingId;

        $app = $this->findForTeam($this->applicationId);
        $this->status = $app->status;
        $this->score  = $app->score;
    }

    protected function findForTeam(int $id): Application
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user   = Auth::user();
        $teamId = $user?->currentTeam?->id;

        return Application::where('team_id', $teamId)
            ->where('opening_id', $this->openingId)
            ->findOrFail($id);
    }

    public function close(): void
    {
        $this->show = false;
        $this->dispatch('applicant-drawer:closed');
    }

    public function updateStatus(): void
    {
        $app = $this->findForTeam($this->applicationId);
        $app->status = $this->status ?: 'new';
        $app->score  = $this->score;
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Applicant updated.');
    }

    public function sendInvite(): void
    {
        $app = $this->findForTeam($this->applicationId);
        if (!$app->invite_token) {
            $app->invite_token = Str::random(40);
        }
        $app->invited_at = now();
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite ready.');
    }

    public function regenerateInvite(): void
    {
        $app = $this->findForTeam($this->applicationId);
        $app->invite_token = Str::random(40);
        $app->invited_at   = now();
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite regenerated.');
    }

    public function removeInvite(): void
    {
        $app = $this->findForTeam($this->applicationId);
        $app->invite_token = null;
        $app->invited_at   = null;
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite removed.');
    }

    public function render()
    {
        $app     = $this->findForTeam($this->applicationId);
        $opening = Opening::find($this->openingId);

        $inviteUrl = $app->invite_token
            ? route('roleplay.invite.show', ['token' => $app->invite_token])
            : null;

        $cvUrl = $app->cv_path
            ? route('applications.cv', ['application' => $app->id])
            : null;

        return view('livewire.recruitment.applicant-drawer', [
            'app'       => $app,
            'opening'   => $opening,
            'inviteUrl' => $inviteUrl,
            'cvUrl'     => $cvUrl,
        ]);
    }
}
