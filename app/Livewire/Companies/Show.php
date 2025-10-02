<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use App\Models\CompanyConnection;
use App\Models\MatchRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    /** The ID passed in from Blade/Route */
    public int $companyId;

    /** Resolved Company model used everywhere else */
    public Company $company;

    // UI state
    public bool $showCompose = false;
    public ?string $note = null;

    public function mount(int $companyId): void
    {
        $this->companyId = $companyId;

        // Resolve Company by ID (works whether route bound a Team or a Company)
        $this->company = Company::findOrFail($this->companyId);

        // Auto-open composer when coming from directory with ?compose=1
        $viewer = $this->viewerCompany;
        if (request()->boolean('compose') && $viewer && $viewer->id !== $this->company->id) {
            if (! $this->isConnected && ! $this->hasPending) {
                $this->note = "Hi {$this->company->name} — interested in exploring a partnership?";
                $this->showCompose = true;
            }
        }
    }

    /** Viewer’s current Company (Jetstream Team cast) */
    public function getViewerCompanyProperty(): ?Company
    {
        $team = auth()->user()?->currentTeam;
        return $team ? Company::find($team->id) : null;
    }

    /** Can the viewer see private contacts? */
    public function getCanSeeContactsProperty(): bool
    {
        $viewer = $this->viewerCompany;
        if (! $viewer) return false;
        if ($viewer->id === $this->company->id) return true;
        return CompanyConnection::areConnected($viewer->id, $this->company->id);
    }

    /** Already connected? */
    public function getIsConnectedProperty(): bool
    {
        $viewer = $this->viewerCompany;
        return $viewer
            ? CompanyConnection::areConnected($viewer->id, $this->company->id)
            : false;
    }

    /** Any pending request in either direction? */
    public function getHasPendingProperty(): bool
    {
        $viewer = $this->viewerCompany;
        if (! $viewer) return false;

        return MatchRequest::where('status','pending')
            ->whereIn('from_company_id', [$viewer->id, $this->company->id])
            ->whereIn('to_company_id',   [$viewer->id, $this->company->id])
            ->exists();
    }

    public function sendRequest(): void
    {
        $viewer = $this->viewerCompany;
        if (! $viewer) return;

        $from = $viewer->id;
        $to   = $this->company->id;
        if ($from === $to) return;

        // Block if already connected or pending either way
        if ($this->isConnected || $this->hasPending) {
            session()->flash('msg', $this->isConnected ? 'You are already connected.' : 'A pending request already exists.');
            $this->showCompose = false;
            return;
        }

        $this->validate([
            'note' => ['nullable','string','max:5000'],
        ]);

        MatchRequest::create([
            'from_company_id' => $from,
            'to_company_id'   => $to,
            'status'          => 'pending',
            'context'         => null,
            'note'            => trim((string) $this->note),
        ]);

        $this->reset(['showCompose','note']);
        session()->flash('msg','Request sent.');
    }

    public function render()
    {
        $company = $this->company->load(['specialties:id,name','certifications:id,name','contacts','assets']);
        $intent  = $company->activeIntent();

        return view('livewire.companies.show', compact('company','intent'))
            ->title($company->name)
            ->layout('layouts.app');
    }
}
