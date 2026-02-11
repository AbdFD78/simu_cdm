<?php

namespace App\Livewire;

use App\Models\Phase;
use Livewire\Component;

class PhaseList extends Component
{
    public function render()
    {
        $phases = Phase::orderBy('ordre')->get();

        return view('livewire.phase-list', [
            'phases' => $phases,
        ])->layout('layouts.app');
    }
}

