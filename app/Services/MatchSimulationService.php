<?php

namespace App\Services;

use App\Models\Classement;
use App\Models\EvenementMatch;
use App\Models\MatchModel;
use App\Models\Poule;
use Illuminate\Support\Facades\DB;

class MatchSimulationService
{
    /**
     * Simule tous les matchs d'une poule qui sont encore "scheduled".
     * Génère des scores aléatoires et des événements cohérents.
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
        // Générer les buts pour l'équipe A
        for ($i = 0; $i < $scoreA; $i++) {
            $minute = rand(1, 90);
            EvenementMatch::create([
                'match_id' => $match->id,
                'minute' => $minute,
                'type' => 'goal',
                'description' => 'BUT - ' . ($match->equipeA->nom ?? 'Équipe A'),
            ]);
        }

        // Générer les buts pour l'équipe B
        for ($i = 0; $i < $scoreB; $i++) {
            $minute = rand(1, 90);
            EvenementMatch::create([
                'match_id' => $match->id,
                'minute' => $minute,
                'type' => 'goal',
                'description' => 'BUT - ' . ($match->equipeB->nom ?? 'Équipe B'),
            ]);
        }

        // Ajouter quelques cartons jaunes aléatoires (probabilité ~30%)
        if (rand(1, 100) <= 30) {
            $minute = rand(1, 90);
            $equipe = rand(0, 1) === 0 ? $match->equipeA : $match->equipeB;
            EvenementMatch::create([
                'match_id' => $match->id,
                'minute' => $minute,
                'type' => 'yellow_card',
                'description' => 'Carton jaune - ' . ($equipe->nom ?? 'Équipe'),
            ]);
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
