<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\Poule;
use Illuminate\Database\Seeder;

class PouleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phaseGroupes = Phase::where('type_phase', 'group')->firstOrFail();

        // Poules A à L (12 poules pour la phase de groupes)
        foreach (range('A', 'L') as $lettre) {
            Poule::firstOrCreate(
                [
                    'phase_id' => $phaseGroupes->id,
                    'nom' => $lettre,
                ],
                [
                    'phase_id' => $phaseGroupes->id,
                    'nom' => $lettre,
                ]
            );
        }
    }
}

