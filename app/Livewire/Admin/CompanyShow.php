<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Livewire\Component;

class CompanyShow extends Component
{
    public Company $company;

    public function mount(Company $company)
    {
        $this->company->load([
            'members',
            'specialties',
            'certifications',
            'contacts',
            'assets',
            'intents',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.company-show')
            ->layout('layouts.app');
    }
}
