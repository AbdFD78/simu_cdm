<?php

namespace App\Livewire;

use App\Models\MatchModel;
use Livewire\Component;

class MatchShow extends Component
{
    public MatchModel $match;

    public function mount(MatchModel $match): void
    {
        $this->match = $match->load(['phase', 'poule', 'equipeA', 'equipeB']);
    }

    public function render()
    {
        return view('livewire.match-show', [
            'match' => $this->match,
        ])->layout('layouts.app');
    }
}

