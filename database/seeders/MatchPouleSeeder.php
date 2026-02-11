<?php

namespace Database\Seeders;

use App\Models\Classement;
use App\Models\Equipe;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MatchPouleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phaseGroupes = Phase::where('type_phase', 'group')->firstOrFail();
        $poules = Poule::where('phase_id', $phaseGroupes->id)->get();

        $baseDate = Carbon::create(2026, 6, 1, 12, 0, 0);
        $matchOffsetMinutes = 0;

        foreach ($poules as $poule) {
            $equipes = Equipe::where('poule_id', $poule->id)->get();

            // Initialiser les classements pour chaque équipe de la poule
            foreach ($equipes as $equipe) {
                Classement::firstOrCreate(
                    [
                        'poule_id' => $poule->id,
                        'equipe_id' => $equipe->id,
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
            }

            // Générer les matchs en round-robin simple (chaque équipe rencontre les autres une fois)
            $equipesIds = $equipes->pluck('id')->values();

            for ($i = 0; $i < $equipesIds->count(); $i++) {
                for ($j = $i + 1; $j < $equipesIds->count(); $j++) {
                    $dateMatch = $baseDate->copy()->addMinutes($matchOffsetMinutes);
                    $matchOffsetMinutes += 120; // espacement artificiel

                    MatchModel::firstOrCreate(
                        [
                            'phase_id' => $phaseGroupes->id,
                            'poule_id' => $poule->id,
                            'equipe_a_id' => $equipesIds[$i],
                            'equipe_b_id' => $equipesIds[$j],
                        ],
                        [
                            'date_heure' => $dateMatch,
                            'statut' => 'scheduled',
                            'score_equipe_a' => 0,
                            'score_equipe_b' => 0,
                            'stade' => 'Stade ' . $poule->nom,
                            'ville' => 'Ville ' . $poule->nom,
                        ]
                    );
                }
            }
        }
    }
}

