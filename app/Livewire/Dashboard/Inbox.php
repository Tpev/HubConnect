<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Inbox extends Component
{
    /** @var array<int, array> */
    public array $pendingReceived = [];   // requests TO my company (I need to accept/decline)
    /** @var array<int, array> */
    public array $pendingSent = [];       // requests FROM my company (waiting on them)
    /** @var array<int, array> */
    public array $recentConnections = []; // recent connections involving my company
    /** @var array<int, array{id:int,name:string|null,photo:string|null}> */
    public array $companyMap = [];        // company_id => ['id'=>..,'name'=>..,'photo'=>..]

    public function mount(): void
    {
        $team = Auth::user()?->currentTeam;

        if (! $team) {
            return;
        }

        $received = [];
        $sent = [];
        $connections = [];

        if (class_exists(\App\Models\MatchRequest::class)) {
            // Received (to me)
            $received = \App\Models\MatchRequest::query()
                ->where('to_company_id', $team->id)
                ->where('status', 'pending')
                ->latest('id')
                ->take(5)
                ->get(['id','from_company_id','to_company_id','created_at'])
                ->toArray();

            // Sent (from me)
            $sent = \App\Models\MatchRequest::query()
                ->where('from_company_id', $team->id)
                ->where('status', 'pending')
                ->latest('id')
                ->take(5)
                ->get(['id','from_company_id','to_company_id','created_at'])
                ->toArray();
        }

        if (class_exists(\App\Models\CompanyConnection::class)) {
            $connections = \App\Models\CompanyConnection::query()
                ->where(function($q) use ($team) {
                    $q->where('company_a_id', $team->id)
                      ->orWhere('company_b_id', $team->id);
                })
                ->latest('id')
                ->take(5)
                ->get(['id','company_a_id','company_b_id','created_at'])
                ->toArray();
        }

        $this->pendingReceived   = $received;
        $this->pendingSent       = $sent;
        $this->recentConnections = $connections;

        // Build a single company map for names/avatars (no N+1)
        $ids = [];

        foreach ($received as $r) {
            $ids[] = $r['from_company_id'] ?? null;
            $ids[] = $r['to_company_id'] ?? null;
        }
        foreach ($sent as $r) {
            $ids[] = $r['from_company_id'] ?? null;
            $ids[] = $r['to_company_id'] ?? null;
        }
        foreach ($connections as $c) {
            $ids[] = $c['company_a_id'] ?? null;
            $ids[] = $c['company_b_id'] ?? null;
        }

        $ids = array_values(array_unique(array_filter($ids)));

        if ($ids && class_exists(\App\Models\Company::class)) {
            // IMPORTANT: don't hard-select unknown columns. Fetch full model and probe fields safely.
            $companies = \App\Models\Company::query()
                ->whereIn('id', $ids)
                ->get(); // no select([...]) avoids "unknown column" issues

            foreach ($companies as $co) {
                // Support both schemas: team_profile_photo_path OR profile_photo_path
                $photoPath = data_get($co, 'team_profile_photo_path')
                           ?? data_get($co, 'profile_photo_path');

                $photoUrl = $photoPath ? Storage::url($photoPath) : null;

                $this->companyMap[(int) $co->id] = [
                    'id'    => (int) $co->id,
                    'name'  => $co->name,
                    'photo' => $photoUrl,
                ];
            }
        }
    }

    /** Helper: get a compact display (avatar + name) for a company id. */
    public function companyMeta(?int $id): array
    {
        return $this->companyMap[$id] ?? ['id' => (int) $id, 'name' => 'Company #'.$id, 'photo' => null];
    }

    public function render()
    {
        return view('livewire.dashboard.inbox');
    }
}
