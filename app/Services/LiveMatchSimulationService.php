<?php

namespace App\Services;

use App\Models\EvenementMatch;
use App\Models\MatchModel;
use Illuminate\Support\Facades\Cache;

class LiveMatchSimulationService
{
    /**
     * Démarre une simulation live d'un match sur 30 secondes.
     * Stocke l'état dans le cache avec une clé unique.
     */
    public function startLiveSimulation(MatchModel $match): void
    {
        // S'assurer que le match commence à 0-0 pour éviter des scores illogiques
        $match->update([
            'statut' => 'live',
            'score_equipe_a' => 0,
            'score_equipe_b' => 0,
        ]);

        // Supprimer les anciens événements si le match était déjà simulé
        \App\Models\EvenementMatch::where('match_id', $match->id)->delete();

        // Stocker l'état de simulation dans le cache (expire après 60 secondes pour sécurité)
        $cacheKey = "live_simulation_match_{$match->id}";
        Cache::put($cacheKey, [
            'started_at' => now()->timestamp,
            'tick' => 0,
            'total_ticks' => 6, // 30 secondes / 5 secondes = 6 ticks
        ], 60);
    }

    /**
     * Traite un tick de simulation (appelé toutes les 5 secondes).
     * Génère des événements aléatoires et met à jour le score.
     */
    public function processTick(MatchModel $match): array
    {
        $cacheKey = "live_simulation_match_{$match->id}";
        $state = Cache::get($cacheKey);

        if (!$state) {
            return ['finished' => true];
        }

        $tick = $state['tick'];
        $totalTicks = $state['total_ticks'];

        // Calculer la minute simulée (projeter 30 secondes sur 90 minutes)
        $simulatedMinute = (int) floor(($tick / $totalTicks) * 90);

        // Probabilité d'événement à chaque tick (~40%)
        $eventOccurred = rand(1, 100) <= 40;

        if ($eventOccurred) {
            $this->generateRandomEvent($match, $simulatedMinute);
        }

        // Mettre à jour le cache pour le prochain tick
        $tick++;
        Cache::put($cacheKey, [
            'started_at' => $state['started_at'],
            'tick' => $tick,
            'total_ticks' => $totalTicks,
        ], 60);

        // Si on a atteint le dernier tick, terminer la simulation
        if ($tick >= $totalTicks) {
            $match->update(['statut' => 'finished']);
            Cache::forget($cacheKey);
            return ['finished' => true, 'minute' => 90];
        }

        // Recharger le match pour avoir les scores à jour
        $match->refresh();

        return [
            'finished' => false,
            'tick' => $tick,
            'minute' => $simulatedMinute,
        ];
    }

    /**
     * Génère un événement aléatoire pour le match en cours.
     */
    private function generateRandomEvent(MatchModel $match, int $minute): void
    {
        $eventType = $this->getRandomEventType();
        $equipe = rand(0, 1) === 0 ? $match->equipeA : $match->equipeB;

        $description = match($eventType) {
            'goal' => 'BUT - ' . ($equipe->nom ?? 'Équipe'),
            'yellow_card' => 'Carton jaune - ' . ($equipe->nom ?? 'Équipe'),
            'red_card' => 'Carton rouge - ' . ($equipe->nom ?? 'Équipe'),
            'substitution' => 'Remplacement - ' . ($equipe->nom ?? 'Équipe'),
            default => 'Événement - ' . ($equipe->nom ?? 'Équipe'),
        };

        EvenementMatch::create([
            'match_id' => $match->id,
            'minute' => $minute,
            'type' => $eventType,
            'description' => $description,
        ]);

        // Si c'est un but, mettre à jour le score
        if ($eventType === 'goal') {
            if ($equipe->id === $match->equipe_a_id) {
                $match->increment('score_equipe_a');
            } else {
                $match->increment('score_equipe_b');
            }
        }
    }

    /**
     * Retourne un type d'événement aléatoire avec distribution biaisée.
     */
    private function getRandomEventType(): string
    {
        $rand = rand(1, 100);

        // Distribution :
        // But : 60%
        // Carton jaune : 25%
        // Carton rouge : 5%
        // Remplacement : 10%

        if ($rand <= 60) {
            return 'goal';
        } elseif ($rand <= 85) {
            return 'yellow_card';
        } elseif ($rand <= 90) {
            return 'red_card';
        } else {
            return 'substitution';
        }
    }

    /**
     * Vérifie si une simulation live est en cours pour un match.
     */
    public function isLiveSimulationActive(MatchModel $match): bool
    {
        $cacheKey = "live_simulation_match_{$match->id}";
        return Cache::has($cacheKey);
    }
}
