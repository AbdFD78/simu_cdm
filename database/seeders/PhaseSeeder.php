<?php

namespace Database\Seeders;

use App\Models\Phase;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phases = [
            ['nom' => 'Phase de groupes', 'ordre' => 1, 'type_phase' => 'group'],
            ['nom' => 'Seizièmes de finale', 'ordre' => 2, 'type_phase' => 'knockout'],
            ['nom' => 'Huitièmes de finale', 'ordre' => 3, 'type_phase' => 'knockout'],
            ['nom' => 'Quarts de finale', 'ordre' => 4, 'type_phase' => 'knockout'],
            ['nom' => 'Demi-finales', 'ordre' => 5, 'type_phase' => 'knockout'],
            ['nom' => 'Petite finale', 'ordre' => 6, 'type_phase' => 'knockout'],
            ['nom' => 'Finale', 'ordre' => 7, 'type_phase' => 'knockout'],
        ];

        foreach ($phases as $data) {
            Phase::firstOrCreate(
                ['nom' => $data['nom']],
                $data
            );
        }
    }
}

