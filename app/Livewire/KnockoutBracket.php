<?php

namespace App\Livewire;

use App\Models\MatchModel;
use App\Models\Phase;
use App\Services\KnockoutGenerationService;
use App\Services\MatchSimulationService;
use Livewire\Component;

class KnockoutBracket extends Component
{
    public function simulatePhase(string $phaseNom): void
    {
        $phase = Phase::where('nom', $phaseNom)->first();
        if (!$phase) {
            return;
        }

        $service = app(MatchSimulationService::class);
        $service->simulatePhase($phase);
    }

    public function render()
    {
        // Récupérer toutes les phases à élimination directe dans l'ordre logique
        $phaseOrder = [
            'Seizièmes de finale',
            'Huitièmes de finale',
            'Quarts de finale',
            'Demi-finales',
            'Petite finale',
            'Finale',
        ];

        $phases = Phase::where('type_phase', 'knockout')
            ->whereIn('nom', $phaseOrder)
            ->get()
            ->sortBy(fn (Phase $phase) => array_search($phase->nom, $phaseOrder))
            ->values();

        // Charger les matchs de chaque phase avec les équipes
        $phasesWithMatches = $phases->map(function (Phase $phase) {
            $matchs = MatchModel::with(['equipeA', 'equipeB'])
                ->where('phase_id', $phase->id)
                ->orderBy('date_heure')
                ->get();

            // Vérifier si tous les matchs sont terminés
            $allFinished = $matchs->isNotEmpty() && $matchs->every(fn ($m) => $m->statut === 'finished');
            $hasScheduled = $matchs->contains(fn ($m) => $m->statut === 'scheduled');

            return [
                'phase' => $phase,
                'matchs' => $matchs,
                'allFinished' => $allFinished,
                'hasScheduled' => $hasScheduled,
            ];
        });

        return view('livewire.knockout-bracket', [
            'phasesWithMatches' => $phasesWithMatches,
        ])->layout('layouts.app');
    }
}

