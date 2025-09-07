<?php

namespace App\Livewire;

use App\Services\RoleplayApi;
use Livewire\Component;

class RoleplaySimulator extends Component
{
    public bool $started = false;
    public bool $done    = false;

    public array $scenario = [];
    public string $dossierText = '';
    public array $transcript = [];   // [{role:'buyer'|'candidate', text:'...'}]
    public string $input = '';

    public ?array $score = null;

    public function start(RoleplayApi $api): void
    {
        $data = $api->start();
        $this->scenario    = $data['scenario'] ?? [];
        $this->dossierText = $data['dossier_text'] ?? '';
        $opening           = $data['opening_buyer'] ?? 'Hello — tell me why we should listen.';
        $this->transcript  = [['role' => 'buyer', 'text' => $opening]];
        $this->started     = true;
        $this->done        = false;
        $this->score       = null;
        $this->input       = '';

        $this->dispatch('transcript-updated');
    }

    public function send(RoleplayApi $api): void
    {
        $message = trim($this->input);
        if ($message === '' || $this->done) return;

        $this->transcript[] = ['role' => 'candidate', 'text' => $message];
        $this->input = '';

        $resp = $api->turn($this->transcript, $message);
        $this->transcript[] = ['role' => 'buyer', 'text' => $resp['buyer_text'] ?? ''];
        $this->done = (bool)($resp['done'] ?? false);

        $this->dispatch('transcript-updated');
    }

    public function finish(RoleplayApi $api): void
    {
        $this->done = true;
        $this->score = $api->evaluate($this->transcript);
        $this->dispatch('transcript-updated');
    }

    /** Quick insert helpers for suggestion chips */
    public function insert(string $snippet): void
    {
        $this->input = trim(($this->input ? $this->input.' ' : '').$snippet);
    }

    public function render()
    {
        return view('livewire.roleplay-simulator')->layout('layouts.guest');
    }
}
