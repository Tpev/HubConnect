<?php

namespace App\Livewire\Requests;

use Livewire\Attributes\On;
use Livewire\Component;

class Panel extends Component
{
    public bool $open = false;

    public function mount(): void
    {
        // Deep-link support: /?panel=connections&tab=received
        if (request()->query('panel') === 'connections') {
            $this->open = true;
        }
    }

    #[On('open-connections')]   public function open(): void  { $this->open = true; }
    #[On('close-connections')]  public function close(): void { $this->open = false; }
    #[On('toggle-connections')] public function toggle(): void{ $this->open = ! $this->open; }

    public function render()
    {
        return view('livewire.requests.panel');
    }
}
