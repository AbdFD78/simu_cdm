<?php

namespace App\Services;

use App\Models\Classement;
use App\Models\EvenementMatch;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use Illuminate\Support\Facades\DB;

class MatchSimulationService
{
    /**
     * Simule tous les matchs d'une poule qui sont encore "scheduled".
     * Génère des scores aléatoires et des événements cohérents instantanément.
     */
    public function simulatePoule(Poule $poule): void
    {
        DB::transaction(function () use ($poule) {
            $matchs = MatchModel::where('poule_id', $poule->id)
                ->where('statut', 'scheduled')
                ->with(['equipeA', 'equipeB'])
                ->get();

            foreach ($matchs as $match) {
                $this->simulateMatch($match);
            }

            // Recalculer les classements de la poule
            $this->recalculateClassements($poule);

            // Vérifier si toutes les poules sont terminées et générer les seizièmes automatiquement
            $knockoutService = app(\App\Services\KnockoutGenerationService::class);
            if ($knockoutService->areAllGroupMatchesFinished()) {
                $knockoutService->generateSeiziemesDeFinale();
            }
        });
    }

    /**
     * Simule tous les matchs \"scheduled\" d'une phase donnée (utile pour les seizièmes, huitièmes, etc.).
     */
    public function simulatePhase(Phase $phase): void
    {
        DB::transaction(function () use ($phase) {
            $matchs = MatchModel::where('phase_id', $phase->id)
                ->where('statut', 'scheduled')
                ->with(['equipeA', 'equipeB'])
                ->get();

            foreach ($matchs as $match) {
                $this->simulateMatch($match);
            }

            // Générer automatiquement la phase suivante si tous les matchs sont terminés
            $knockoutService = app(\App\Services\KnockoutGenerationService::class);
            
            if ($phase->nom === 'Seizièmes de finale' && $knockoutService->areAllPhaseMatchesFinished('Seizièmes de finale')) {
                $knockoutService->generateHuitiemesDeFinale();
            } elseif ($phase->nom === 'Huitièmes de finale' && $knockoutService->areAllPhaseMatchesFinished('Huitièmes de finale')) {
                $knockoutService->generateQuartsDeFinale();
            } elseif ($phase->nom === 'Quarts de finale' && $knockoutService->areAllPhaseMatchesFinished('Quarts de finale')) {
                $knockoutService->generateDemisDeFinale();
            } elseif ($phase->nom === 'Demi-finales' && $knockoutService->areAllPhaseMatchesFinished('Demi-finales')) {
                $knockoutService->generateFinale();
            }
        });
    }

    /**
     * Simule un match unique avec génération de score et d'événements.
     */
    public function simulateMatch(MatchModel $match): void
    {
        // Générer un score aléatoire (0-4 buts par équipe, distribution biaisée)
        $scoreA = $this->generateRandomScore();
        $scoreB = $this->generateRandomScore();

        // Mettre à jour le match
        $match->update([
            'score_equipe_a' => $scoreA,
            'score_equipe_b' => $scoreB,
            'statut' => 'finished',
        ]);

        // Générer les événements correspondants
        $this->generateEventsForMatch($match, $scoreA, $scoreB);
    }

    /**
     * Génère un score aléatoire avec distribution biaisée (scores faibles plus probables).
     */
    private function generateRandomScore(): int
    {
        $rand = rand(1, 100);

        // Distribution approximative :
        // 0 buts : 30%
        // 1 but : 35%
        // 2 buts : 20%
        // 3 buts : 10%
        // 4 buts : 5%

        if ($rand <= 30) {
            return 0;
        } elseif ($rand <= 65) {
            return 1;
        } elseif ($rand <= 85) {
            return 2;
        } elseif ($rand <= 95) {
            return 3;
        } else {
            return 4;
        }
    }

    /**
     * Génère les événements d'un match en fonction des scores.
     */
    private function generateEventsForMatch(MatchModel $match, int $scoreA, int $scoreB): void
    {
        $events = [];

        // Générer les buts pour l'équipe A (avec minutes triées)
        for ($i = 0; $i < $scoreA; $i++) {
            $events[] = [
                'match_id' => $match->id,
                'minute' => rand(1, 90),
                'type' => 'goal',
                'description' => 'BUT - ' . ($match->equipeA->nom ?? 'Équipe A'),
            ];
        }

        // Générer les buts pour l'équipe B
        for ($i = 0; $i < $scoreB; $i++) {
            $events[] = [
                'match_id' => $match->id,
                'minute' => rand(1, 90),
                'type' => 'goal',
                'description' => 'BUT - ' . ($match->equipeB->nom ?? 'Équipe B'),
            ];
        }

        // Ajouter des cartons jaunes (probabilité ~60% par match)
        if (rand(1, 100) <= 60) {
            $events[] = [
                'match_id' => $match->id,
                'minute' => rand(1, 90),
                'type' => 'yellow_card',
                'description' => 'Carton jaune - ' . (rand(0, 1) === 0 ? ($match->equipeA->nom ?? 'Équipe A') : ($match->equipeB->nom ?? 'Équipe B')),
            ];
        }

        // Ajouter un carton rouge occasionnel (probabilité ~20%)
        if (rand(1, 100) <= 20) {
            $events[] = [
                'match_id' => $match->id,
                'minute' => rand(1, 90),
                'type' => 'red_card',
                'description' => 'Carton rouge - ' . (rand(0, 1) === 0 ? ($match->equipeA->nom ?? 'Équipe A') : ($match->equipeB->nom ?? 'Équipe B')),
            ];
        }

        // Ajouter des remplacements (probabilité ~50%, généralement en seconde période)
        if (rand(1, 100) <= 50) {
            $events[] = [
                'match_id' => $match->id,
                'minute' => rand(45, 85),
                'type' => 'substitution',
                'description' => 'Remplacement - ' . (rand(0, 1) === 0 ? ($match->equipeA->nom ?? 'Équipe A') : ($match->equipeB->nom ?? 'Équipe B')),
            ];
        }

        // Trier les événements par minute avant insertion
        usort($events, fn($a, $b) => $a['minute'] <=> $b['minute']);

        // Insérer tous les événements
        foreach ($events as $event) {
            EvenementMatch::create($event);
        }
    }

    /**
     * Recalcule les classements d'une poule à partir des matchs terminés.
     */
    public function recalculateClassements(Poule $poule): void
    {
        // Réinitialiser tous les classements de la poule
        Classement::where('poule_id', $poule->id)->update([
            'points' => 0,
            'matchs_joues' => 0,
            'victoires' => 0,
            'nuls' => 0,
            'defaites' => 0,
            'buts_marques' => 0,
            'buts_encaissees' => 0,
        ]);

        // Récupérer tous les matchs terminés de la poule
        $matchs = MatchModel::where('poule_id', $poule->id)
            ->where('statut', 'finished')
            ->with(['equipeA', 'equipeB'])
            ->get();

        foreach ($matchs as $match) {
            $equipeA = $match->equipeA;
            $equipeB = $match->equipeB;

            if (!$equipeA || !$equipeB) {
                continue;
            }

            // Mettre à jour le classement de l'équipe A
            $classementA = Classement::firstOrCreate(
                [
                    'poule_id' => $poule->id,
                    'equipe_id' => $equipeA->id,
                ],
                [
                    'points' => 0,
                    'matchs_joues' => 0,
                    'victoires' => 0,
                    'nuls' => 0,
                    'defaites' => 0,
                    'buts_marques' => 0,
                    'buts_encaissees' => 0,
                ]
            );

            $classementA->matchs_joues++;
            $classementA->buts_marques += $match->score_equipe_a;
            $classementA->buts_encaissees += $match->score_equipe_b;

            if ($match->score_equipe_a > $match->score_equipe_b) {
                $classementA->victoires++;
                $classementA->points += 3;
            } elseif ($match->score_equipe_a === $match->score_equipe_b) {
                $classementA->nuls++;
                $classementA->points += 1;
            } else {
                $classementA->defaites++;
            }

            $classementA->save();

            // Mettre à jour le classement de l'équipe B
            $classementB = Classement::firstOrCreate(
                [
                    'poule_id' => $poule->id,
                    'equipe_id' => $equipeB->id,
                ],
                [
                    'points' => 0,
                    'matchs_joues' => 0,
                    'victoires' => 0,
                    'nuls' => 0,
                    'defaites' => 0,
                    'buts_marques' => 0,
                    'buts_encaissees' => 0,
                ]
            );

            $classementB->matchs_joues++;
            $classementB->buts_marques += $match->score_equipe_b;
            $classementB->buts_encaissees += $match->score_equipe_a;

            if ($match->score_equipe_b > $match->score_equipe_a) {
                $classementB->victoires++;
                $classementB->points += 3;
            } elseif ($match->score_equipe_b === $match->score_equipe_a) {
                $classementB->nuls++;
                $classementB->points += 1;
            } else {
                $classementB->defaites++;
            }

            $classementB->save();
        }
    }
}
