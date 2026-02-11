<?php

namespace App\Services;

use App\Models\Classement;
use App\Models\Equipe;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KnockoutGenerationService
{
    /**
     * Génère automatiquement les matchs de seizièmes de finale
     * à partir des classements des poules de la phase de groupes.
     */
    public function generateSeiziemesDeFinale(): void
    {
        DB::transaction(function () {
            // Récupérer la phase de groupes et la phase "Seizièmes de finale"
            $phaseGroupes = Phase::where('type_phase', 'group')->first();
            $phaseSeiziemes = Phase::where('nom', 'Seizièmes de finale')->first();

            if (!$phaseGroupes || !$phaseSeiziemes) {
                return;
            }

            // Vérifier que tous les matchs de groupes sont terminés
            $poules = Poule::where('phase_id', $phaseGroupes->id)->get();
            foreach ($poules as $poule) {
                $scheduledCount = MatchModel::where('poule_id', $poule->id)
                    ->where('statut', 'scheduled')
                    ->count();
                
                if ($scheduledCount > 0) {
                    // Tous les matchs ne sont pas terminés, on ne génère pas les seizièmes
                    return;
                }
            }

            // Vérifier si les seizièmes ont déjà été générés
            $existingMatches = MatchModel::where('phase_id', $phaseSeiziemes->id)->count();
            if ($existingMatches > 0) {
                // Les seizièmes ont déjà été générés, on ne les régénère pas
                return;
            }

            // Récupérer tous les classements triés par poule
            $poulesData = [];
            foreach ($poules as $poule) {
                $classements = Classement::where('poule_id', $poule->id)
                    ->with('equipe')
                    ->get()
                    ->sortByDesc(function ($c) {
                        return [
                            $c->points,
                            $c->buts_marques - $c->buts_encaissees,
                            $c->buts_marques,
                        ];
                    })
                    ->values();

                $poulesData[$poule->nom] = [
                    '1er' => $classements[0] ?? null,
                    '2e' => $classements[1] ?? null,
                    '3e' => $classements[2] ?? null,
                ];
            }

            // Récupérer les 8 meilleurs 3e
            $allThirds = [];
            foreach ($poulesData as $pouleNom => $data) {
                if ($data['3e']) {
                    $allThirds[] = [
                        'poule' => $pouleNom,
                        'classement' => $data['3e'],
                    ];
                }
            }

            // Trier les 3e par points, différence de buts, buts marqués
            usort($allThirds, function ($a, $b) {
                $c1 = $a['classement'];
                $c2 = $b['classement'];
                
                if ($c1->points !== $c2->points) {
                    return $c2->points <=> $c1->points;
                }
                
                $diff1 = $c1->buts_marques - $c1->buts_encaissees;
                $diff2 = $c2->buts_marques - $c2->buts_encaissees;
                if ($diff1 !== $diff2) {
                    return $diff2 <=> $diff1;
                }
                
                return $c2->buts_marques <=> $c1->buts_marques;
            });

            $bestThirds = array_slice($allThirds, 0, 8);

            // Mapping selon le schéma fourni
            $matches = [
                // M1 à M9 : 1er de groupe vs 3e (meilleurs 3e)
                ['equipe_a' => $poulesData['A']['1er'], 'equipe_b' => $bestThirds[0]['classement'] ?? null], // M1: 1A vs 3H (premier meilleur 3e)
                ['equipe_a' => $poulesData['A']['2e'], 'equipe_b' => $poulesData['B']['2e']], // M2: 2A vs 2B
                ['equipe_a' => $poulesData['B']['1er'], 'equipe_b' => $bestThirds[1]['classement'] ?? null], // M3: 1B vs 3G
                ['equipe_a' => $poulesData['C']['1er'], 'equipe_b' => $bestThirds[2]['classement'] ?? null], // M4: 1C vs 3F
                ['equipe_a' => $poulesData['D']['1er'], 'equipe_b' => $bestThirds[3]['classement'] ?? null], // M5: 1D vs 3E
                ['equipe_a' => $poulesData['E']['1er'], 'equipe_b' => $bestThirds[4]['classement'] ?? null], // M6: 1E vs 3D
                ['equipe_a' => $poulesData['F']['1er'], 'equipe_b' => $bestThirds[5]['classement'] ?? null], // M7: 1F vs 3C
                ['equipe_a' => $poulesData['G']['1er'], 'equipe_b' => $bestThirds[6]['classement'] ?? null], // M8: 1G vs 3B
                ['equipe_a' => $poulesData['H']['1er'], 'equipe_b' => $bestThirds[7]['classement'] ?? null], // M9: 1H vs 3A
                // M10 à M13 : 1er vs 2e (groupes I à L)
                ['equipe_a' => $poulesData['I']['1er'], 'equipe_b' => $poulesData['J']['2e']], // M10: 1I vs 2J
                ['equipe_a' => $poulesData['J']['1er'], 'equipe_b' => $poulesData['I']['2e']], // M11: 1J vs 2I
                ['equipe_a' => $poulesData['K']['1er'], 'equipe_b' => $poulesData['L']['2e']], // M12: 1K vs 2L
                ['equipe_a' => $poulesData['L']['1er'], 'equipe_b' => $poulesData['K']['2e']], // M13: 1L vs 2K
                // M14 à M16 : 2e vs 2e
                ['equipe_a' => $poulesData['C']['2e'], 'equipe_b' => $poulesData['D']['2e']], // M14: 2C vs 2D
                ['equipe_a' => $poulesData['E']['2e'], 'equipe_b' => $poulesData['F']['2e']], // M15: 2E vs 2F
                ['equipe_a' => $poulesData['G']['2e'], 'equipe_b' => $poulesData['H']['2e']], // M16: 2G vs 2H
            ];

            // Créer les matchs
            $baseDate = Carbon::create(2026, 7, 1, 12, 0, 0);
            $matchOffsetMinutes = 0;

            foreach ($matches as $index => $matchData) {
                if (!$matchData['equipe_a'] || !$matchData['equipe_b']) {
                    continue; // Skip si une équipe manque
                }

                $dateMatch = $baseDate->copy()->addMinutes($matchOffsetMinutes);
                $matchOffsetMinutes += 120; // Espacement de 2h entre chaque match

                MatchModel::create([
                    'phase_id' => $phaseSeiziemes->id,
                    'poule_id' => null, // Pas de poule pour les phases à élimination directe
                    'equipe_a_id' => $matchData['equipe_a']->equipe_id,
                    'equipe_b_id' => $matchData['equipe_b']->equipe_id,
                    'date_heure' => $dateMatch,
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                    'stade' => 'Stade des Seizièmes ' . ($index + 1),
                    'ville' => 'Ville ' . ($index + 1),
                ]);
            }
        });
    }

    /**
     * Vérifie si toutes les poules de la phase de groupes sont terminées.
     */
    public function areAllGroupMatchesFinished(): bool
    {
        $phaseGroupes = Phase::where('type_phase', 'group')->first();
        
        if (!$phaseGroupes) {
            return false;
        }

        $poules = Poule::where('phase_id', $phaseGroupes->id)->get();
        
        foreach ($poules as $poule) {
            $scheduledCount = MatchModel::where('poule_id', $poule->id)
                ->where('statut', 'scheduled')
                ->count();
            
            if ($scheduledCount > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie si tous les matchs d'une phase sont terminés.
     */
    public function areAllPhaseMatchesFinished(string $phaseNom): bool
    {
        $phase = Phase::where('nom', $phaseNom)->first();
        
        if (!$phase) {
            return false;
        }

        $scheduledCount = MatchModel::where('phase_id', $phase->id)
            ->where('statut', 'scheduled')
            ->count();

        return $scheduledCount === 0;
    }

    /**
     * Génère automatiquement les matchs de huitièmes de finale à partir des seizièmes.
     */
    public function generateHuitiemesDeFinale(): void
    {
        DB::transaction(function () {
            $phaseSeiziemes = Phase::where('nom', 'Seizièmes de finale')->first();
            $phaseHuitiemes = Phase::where('nom', 'Huitièmes de finale')->first();

            if (!$phaseSeiziemes || !$phaseHuitiemes) {
                return;
            }

            // Vérifier que tous les seizièmes sont terminés
            if (!$this->areAllPhaseMatchesFinished('Seizièmes de finale')) {
                return;
            }

            // Vérifier si les huitièmes ont déjà été générés
            $existingMatches = MatchModel::where('phase_id', $phaseHuitiemes->id)->count();
            if ($existingMatches > 0) {
                return;
            }

            // Récupérer tous les matchs de seizièmes terminés, triés par date
            $seiziemesMatches = MatchModel::where('phase_id', $phaseSeiziemes->id)
                ->where('statut', 'finished')
                ->with(['equipeA', 'equipeB'])
                ->orderBy('date_heure')
                ->get();

            if ($seiziemesMatches->count() !== 16) {
                return; // Pas tous les matchs sont terminés
            }

            // Créer les 8 matchs de huitièmes (M1 vs M2, M3 vs M4, etc.)
            $baseDate = Carbon::create(2026, 7, 5, 12, 0, 0);
            $matchOffsetMinutes = 0;

            for ($i = 0; $i < 8; $i++) {
                $match1 = $seiziemesMatches[$i * 2];
                $match2 = $seiziemesMatches[$i * 2 + 1];

                // Déterminer le vainqueur de chaque match
                $winner1 = $this->getMatchWinner($match1);
                $winner2 = $this->getMatchWinner($match2);

                if (!$winner1 || !$winner2) {
                    continue;
                }

                $dateMatch = $baseDate->copy()->addMinutes($matchOffsetMinutes);
                $matchOffsetMinutes += 120;

                MatchModel::create([
                    'phase_id' => $phaseHuitiemes->id,
                    'poule_id' => null,
                    'equipe_a_id' => $winner1->id,
                    'equipe_b_id' => $winner2->id,
                    'date_heure' => $dateMatch,
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                    'stade' => 'Stade des Huitièmes ' . ($i + 1),
                    'ville' => 'Ville ' . ($i + 1),
                ]);
            }
        });
    }

    /**
     * Génère automatiquement les matchs de quarts de finale à partir des huitièmes.
     */
    public function generateQuartsDeFinale(): void
    {
        DB::transaction(function () {
            $phaseHuitiemes = Phase::where('nom', 'Huitièmes de finale')->first();
            $phaseQuarts = Phase::where('nom', 'Quarts de finale')->first();

            if (!$phaseHuitiemes || !$phaseQuarts) {
                return;
            }

            if (!$this->areAllPhaseMatchesFinished('Huitièmes de finale')) {
                return;
            }

            $existingMatches = MatchModel::where('phase_id', $phaseQuarts->id)->count();
            if ($existingMatches > 0) {
                return;
            }

            $huitiemesMatches = MatchModel::where('phase_id', $phaseHuitiemes->id)
                ->where('statut', 'finished')
                ->with(['equipeA', 'equipeB'])
                ->orderBy('date_heure')
                ->get();

            if ($huitiemesMatches->count() !== 8) {
                return;
            }

            $baseDate = Carbon::create(2026, 7, 9, 12, 0, 0);
            $matchOffsetMinutes = 0;

            for ($i = 0; $i < 4; $i++) {
                $match1 = $huitiemesMatches[$i * 2];
                $match2 = $huitiemesMatches[$i * 2 + 1];

                $winner1 = $this->getMatchWinner($match1);
                $winner2 = $this->getMatchWinner($match2);

                if (!$winner1 || !$winner2) {
                    continue;
                }

                $dateMatch = $baseDate->copy()->addMinutes($matchOffsetMinutes);
                $matchOffsetMinutes += 120;

                MatchModel::create([
                    'phase_id' => $phaseQuarts->id,
                    'poule_id' => null,
                    'equipe_a_id' => $winner1->id,
                    'equipe_b_id' => $winner2->id,
                    'date_heure' => $dateMatch,
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                    'stade' => 'Stade des Quarts ' . ($i + 1),
                    'ville' => 'Ville ' . ($i + 1),
                ]);
            }
        });
    }

    /**
     * Génère automatiquement les matchs de demi-finales à partir des quarts.
     */
    public function generateDemisDeFinale(): void
    {
        DB::transaction(function () {
            $phaseQuarts = Phase::where('nom', 'Quarts de finale')->first();
            $phaseDemis = Phase::where('nom', 'Demi-finales')->first();

            if (!$phaseQuarts || !$phaseDemis) {
                return;
            }

            if (!$this->areAllPhaseMatchesFinished('Quarts de finale')) {
                return;
            }

            $existingMatches = MatchModel::where('phase_id', $phaseDemis->id)->count();
            if ($existingMatches > 0) {
                return;
            }

            $quartsMatches = MatchModel::where('phase_id', $phaseQuarts->id)
                ->where('statut', 'finished')
                ->with(['equipeA', 'equipeB'])
                ->orderBy('date_heure')
                ->get();

            if ($quartsMatches->count() !== 4) {
                return;
            }

            $baseDate = Carbon::create(2026, 7, 13, 12, 0, 0);

            for ($i = 0; $i < 2; $i++) {
                $match1 = $quartsMatches[$i * 2];
                $match2 = $quartsMatches[$i * 2 + 1];

                $winner1 = $this->getMatchWinner($match1);
                $winner2 = $this->getMatchWinner($match2);

                if (!$winner1 || !$winner2) {
                    continue;
                }

                $dateMatch = $baseDate->copy()->addMinutes($i * 120);

                MatchModel::create([
                    'phase_id' => $phaseDemis->id,
                    'poule_id' => null,
                    'equipe_a_id' => $winner1->id,
                    'equipe_b_id' => $winner2->id,
                    'date_heure' => $dateMatch,
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                    'stade' => 'Stade des Demi-finales ' . ($i + 1),
                    'ville' => 'Ville ' . ($i + 1),
                ]);
            }
        });
    }

    /**
     * Génère automatiquement la finale et la petite finale à partir des demi-finales.
     */
    public function generateFinale(): void
    {
        DB::transaction(function () {
            $phaseDemis = Phase::where('nom', 'Demi-finales')->first();
            $phaseFinale = Phase::where('nom', 'Finale')->first();
            $phasePetiteFinale = Phase::where('nom', 'Petite finale')->first();

            if (!$phaseDemis || !$phaseFinale) {
                return;
            }

            if (!$this->areAllPhaseMatchesFinished('Demi-finales')) {
                return;
            }

            $existingFinale = MatchModel::where('phase_id', $phaseFinale->id)->count();
            if ($existingFinale > 0) {
                return;
            }

            $demisMatches = MatchModel::where('phase_id', $phaseDemis->id)
                ->where('statut', 'finished')
                ->with(['equipeA', 'equipeB'])
                ->orderBy('date_heure')
                ->get();

            if ($demisMatches->count() !== 2) {
                return;
            }

            $demi1 = $demisMatches[0];
            $demi2 = $demisMatches[1];

            $winner1 = $this->getMatchWinner($demi1);
            $loser1 = $this->getMatchLoser($demi1);
            $winner2 = $this->getMatchWinner($demi2);
            $loser2 = $this->getMatchLoser($demi2);

            if (!$winner1 || !$winner2) {
                return;
            }

            // Finale
            MatchModel::create([
                'phase_id' => $phaseFinale->id,
                'poule_id' => null,
                'equipe_a_id' => $winner1->id,
                'equipe_b_id' => $winner2->id,
                'date_heure' => Carbon::create(2026, 7, 19, 18, 0, 0),
                'statut' => 'scheduled',
                'score_equipe_a' => 0,
                'score_equipe_b' => 0,
                'stade' => 'Stade de la Finale',
                'ville' => 'Ville Finale',
            ]);

            // Petite finale (si la phase existe)
            if ($phasePetiteFinale && $loser1 && $loser2) {
                MatchModel::create([
                    'phase_id' => $phasePetiteFinale->id,
                    'poule_id' => null,
                    'equipe_a_id' => $loser1->id,
                    'equipe_b_id' => $loser2->id,
                    'date_heure' => Carbon::create(2026, 7, 18, 18, 0, 0),
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                    'stade' => 'Stade de la Petite Finale',
                    'ville' => 'Ville Petite Finale',
                ]);
            }
        });
    }

    /**
     * Retourne le vainqueur d'un match terminé.
     */
    private function getMatchWinner(MatchModel $match): ?Equipe
    {
        if ($match->statut !== 'finished') {
            return null;
        }

        if ($match->score_equipe_a > $match->score_equipe_b) {
            return $match->equipeA;
        } elseif ($match->score_equipe_b > $match->score_equipe_a) {
            return $match->equipeB;
        }

        // En cas d'égalité, on prend l'équipe A par défaut (ou on pourrait gérer les tirs au but)
        return $match->equipeA;
    }

    /**
     * Retourne le perdant d'un match terminé.
     */
    private function getMatchLoser(MatchModel $match): ?Equipe
    {
        if ($match->statut !== 'finished') {
            return null;
        }

        if ($match->score_equipe_a > $match->score_equipe_b) {
            return $match->equipeB;
        } elseif ($match->score_equipe_b > $match->score_equipe_a) {
            return $match->equipeA;
        }

        // En cas d'égalité, on prend l'équipe B par défaut
        return $match->equipeB;
    }
}
