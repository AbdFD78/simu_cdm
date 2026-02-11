<?php

namespace App\Livewire;

use App\Models\Poule;
use App\Services\MatchSimulationService;
use Livewire\Component;

class PouleShow extends Component
{
    public Poule $poule;

    public function mount(Poule $poule): void
    {
        $this->poule = $poule->load([
            'phase',
            'classements.equipe',
            'matchs.equipeA',
            'matchs.equipeB',
        ]);
    }

    public function simulatePoule(): void
    {
        $service = app(MatchSimulationService::class);
        $service->simulatePoule($this->poule);

        // Recharger la poule et ses relations
        $this->poule->refresh();
        $this->poule->load([
            'phase',
            'classements.equipe',
            'matchs.equipeA',
            'matchs.equipeB',
        ]);
    }

    public function render()
    {
        $poule = $this->poule;

        // Trier les classements : d'abord par points décroissants, puis par différence de buts décroissante
        $classements = $poule->classements
            ->sortByDesc(fn ($c) => [
                $c->points,
                $c->buts_marques - $c->buts_encaissees,
                $c->buts_marques,
            ]);

        return view('livewire.poule-show', [
            'poule' => $poule,
            'classements' => $classements,
            'matchs' => $poule->matchs,
        ])->layout('layouts.app');
    }
}

