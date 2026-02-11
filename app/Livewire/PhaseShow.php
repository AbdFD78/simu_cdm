<?php

namespace App\Livewire;

use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use App\Services\MatchSimulationService;
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

    /**
     * Simule toutes les poules de la phase de groupes en une seule action.
     */
    public function simulateAllPoules(): void
    {
        if ($this->phase->type_phase !== 'group') {
            return;
        }

        $service = app(MatchSimulationService::class);

        foreach ($this->phase->poules as $poule) {
            $service->simulatePoule($poule);
        }

        // Recharger la phase et ses relations après simulation
        $this->phase->refresh();
        $this->phase->load([
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

