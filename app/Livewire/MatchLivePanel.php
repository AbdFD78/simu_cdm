<?php

namespace App\Livewire;

use App\Models\MatchModel;
use App\Services\LiveMatchSimulationService;
use Livewire\Component;

class MatchLivePanel extends Component
{
    public MatchModel $match;
    public bool $isSimulating = false;

    public function mount(MatchModel $match): void
    {
        $this->match = $match->load(['equipeA', 'equipeB', 'evenements']);
    }

    public function startSimulation(): void
    {
        // Vérifier que le match n'est pas déjà terminé
        if ($this->match->statut === 'finished') {
            return;
        }

        $service = app(LiveMatchSimulationService::class);
        $service->startLiveSimulation($this->match);

        // Initialiser la simulation
        $this->isSimulating = true;

        // Rafraîchir le match depuis la BDD
        $this->match->refresh();
    }

    public function updateMatch(): void
    {
        $service = app(LiveMatchSimulationService::class);
        $isLiveActive = $service->isLiveSimulationActive($this->match);

        // Si une simulation live est active, traiter le tick
        if ($isLiveActive && $this->match->statut === 'live') {
            $result = $service->processTick($this->match);

            // Si la simulation est terminée, mettre à jour l'état
            if ($result['finished'] ?? false) {
                $this->isSimulating = false;
            }
        }

        // Rafraîchir le match
        $this->match->refresh();
        $this->match->load(['equipeA', 'equipeB', 'evenements']);
    }

    /**
     * Calcule le temps réglementaire actuel (en minutes) pour un match en cours de simulation live.
     */
    private function getCurrentMatchTime(): int
    {
        $service = app(LiveMatchSimulationService::class);
        $cacheKey = "live_simulation_match_{$this->match->id}";
        $state = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if ($state) {
            $tick = $state['tick'] ?? 0;
            $totalTicks = $state['total_ticks'] ?? 6;
            // 30 secondes réelles = 90 minutes de match
            return (int) floor(($tick / $totalTicks) * 90);
        }

        return 0;
    }

    public function render()
    {
        $service = app(LiveMatchSimulationService::class);
        $isLiveActive = $service->isLiveSimulationActive($this->match);

        // Recharger le match et ses événements à chaque render (notamment lors du polling)
        $this->match->refresh();
        $this->match->load(['equipeA', 'equipeB', 'evenements']);

        // Trier les événements par minute (croissant) puis par id (croissant) pour garantir l'ordre
        $evenements = $this->match->evenements
            ->sortBy('id')
            ->sortBy('minute')
            ->values();

        // Calculer le temps réglementaire si le match est en cours
        $currentMinute = 0;
        if ($isLiveActive || $this->match->statut === 'live') {
            $currentMinute = $this->getCurrentMatchTime();
        }

        return view('livewire.match-live-panel', [
            'match' => $this->match,
            'evenements' => $evenements,
            'isSimulating' => $isLiveActive || $this->match->statut === 'live',
            'currentMinute' => $currentMinute,
        ]);
    }
}
