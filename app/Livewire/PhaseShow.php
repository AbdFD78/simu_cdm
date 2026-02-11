<?php

namespace App\Livewire;

use App\Models\MatchModel;
use App\Models\Phase;
use Livewire\Component;

class PhaseShow extends Component
{
    public Phase $phase;

    public function mount(Phase $phase): void
    {
        $this->phase = $phase->load([
            'poules.classements.equipe',
            'poules.matchs.equipeA',
            'poules.matchs.equipeB',
            'matchs.equipeA',
            'matchs.equipeB',
        ]);
    }

    public function render()
    {
        $phase = $this->phase;

        // Si phase de groupes, on s'appuie sur les poules déjà chargées.
        // Sinon, on affiche simplement les matchs de la phase.
        $matchs = collect();
        if ($phase->type_phase === 'knockout') {
            $matchs = MatchModel::with(['equipeA', 'equipeB'])
                ->where('phase_id', $phase->id)
                ->orderBy('date_heure')
                ->get();
        }

        return view('livewire.phase-show', [
            'phase' => $phase,
            'matchs' => $matchs,
        ])->layout('layouts.app');
    }
}

