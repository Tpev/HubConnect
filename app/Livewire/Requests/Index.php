<?php

namespace App\Livewire\Requests;

use App\Models\Company;
use App\Models\MatchRequest;
use App\Models\CompanyConnection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    #[Url] public string $tab = 'received'; // received|sent
    #[Url] public ?int $compose = null;     // <- read /requests?compose=ID

    public ?int $targetCompanyId = null;    // used by the composer UI
    public ?string $note = null;

    public function mount(): void
    {
        // If we arrived with ?compose=ID, open the composer for that company
        if ($this->compose && $this->compose > 0) {
            $this->targetCompanyId = (int) $this->compose;
        }
    }

    public function getCompanyProperty(): Company
    {
        $team = auth()->user()?->currentTeam;   // Jetstream Team
        abort_unless($team, 403);
        return Company::findOrFail($team->id);  // cast to Company model
    }

    public function compose(int $toCompanyId): void
    {
        $this->targetCompanyId = $toCompanyId;
        $this->dispatch('open-compose'); // (optional) if you hook a modal
    }

    public function send(): void
    {
        $from = $this->company->id;
        $to   = $this->targetCompanyId;

        if (!$to || $to === $from) return;

        // prevent duplicates (simple)
        $exists = MatchRequest::where('from_company_id', $from)
            ->where('to_company_id', $to)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            session()->flash('msg', 'A pending request already exists.');
            return;
        }

        MatchRequest::create([
            'from_company_id' => $from,
            'to_company_id'   => $to,
            'status'          => 'pending',
            'context'         => null,
            'note'            => trim((string) $this->note),
        ]);

        $this->reset(['note', 'targetCompanyId']);
        // keep ?compose in URL as-is; optional: $this->compose = null;
        session()->flash('msg', 'Request sent.');
    }

    public function accept(int $requestId): void
    {
        $req = MatchRequest::findOrFail($requestId);
        abort_unless($req->to_company_id === $this->company->id, 403);

        $req->update(['status' => 'accepted']);
        CompanyConnection::connectPair($req->from_company_id, $req->to_company_id);

        session()->flash('msg', 'Request accepted. Connection created.');
    }

    public function decline(int $requestId): void
    {
        $req = MatchRequest::findOrFail($requestId);
        abort_unless($req->to_company_id === $this->company->id, 403);

        $req->update(['status' => 'declined']);
        session()->flash('msg', 'Request declined.');
    }

    public function render()
    {
        $companyId = $this->company->id;

        $received = MatchRequest::with('fromCompany')
            ->where('to_company_id', $companyId)
            ->latest()
            ->get();

        $sent = MatchRequest::with('toCompany')
            ->where('from_company_id', $companyId)
            ->latest()
            ->get();

        return view('livewire.requests.index', compact('received', 'sent'))
            ->title('Requests')
            ->layout('layouts.app');
    }
}
