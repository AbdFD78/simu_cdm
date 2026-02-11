<?php

namespace Database\Seeders;

use App\Models\Equipe;
use App\Models\Poule;
use Illuminate\Database\Seeder;

class EquipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Équipes par poule, d'après la répartition fournie (Groupes A à L)
        $equipesParPoule = [
            // Groupe A
            'A' => [
                ['nom' => 'MEX', 'code_pays' => 'MEX'],
                ['nom' => 'RSA', 'code_pays' => 'RSA'],
                ['nom' => 'KOR', 'code_pays' => 'KOR'],
                ['nom' => 'DEN/MKD/CZE/IRL', 'code_pays' => 'DEN'],
            ],
            // Groupe B
            'B' => [
                ['nom' => 'CAN', 'code_pays' => 'CAN'],
                ['nom' => 'QAT', 'code_pays' => 'QAT'],
                ['nom' => 'SUI', 'code_pays' => 'SUI'],
                ['nom' => 'ITA/NIR/WAL/BIH', 'code_pays' => 'ITA'],
            ],
            // Groupe C
            'C' => [
                ['nom' => 'BRA', 'code_pays' => 'BRA'],
                ['nom' => 'MAR', 'code_pays' => 'MAR'],
                ['nom' => 'HAI', 'code_pays' => 'HAI'],
                ['nom' => 'SCO', 'code_pays' => 'SCO'],
            ],
            // Groupe D
            'D' => [
                ['nom' => 'USA', 'code_pays' => 'USA'],
                ['nom' => 'PAR', 'code_pays' => 'PAR'],
                ['nom' => 'AUS', 'code_pays' => 'AUS'],
                ['nom' => 'TUR/ROU/SVK/KOS', 'code_pays' => 'TUR'],
            ],
            // Groupe E
            'E' => [
                ['nom' => 'GER', 'code_pays' => 'GER'],
                ['nom' => 'CUW', 'code_pays' => 'CUW'],
                ['nom' => 'CIV', 'code_pays' => 'CIV'],
                ['nom' => 'ECU', 'code_pays' => 'ECU'],
            ],
            // Groupe F
            'F' => [
                ['nom' => 'NED', 'code_pays' => 'NED'],
                ['nom' => 'JPN', 'code_pays' => 'JPN'],
                ['nom' => 'TUN', 'code_pays' => 'TUN'],
                ['nom' => 'UKR/SWE/POL/ALB', 'code_pays' => 'UKR'],
            ],
            // Groupe G
            'G' => [
                ['nom' => 'BEL', 'code_pays' => 'BEL'],
                ['nom' => 'EGY', 'code_pays' => 'EGY'],
                ['nom' => 'IRN', 'code_pays' => 'IRN'],
                ['nom' => 'NZL', 'code_pays' => 'NZL'],
            ],
            // Groupe H
            'H' => [
                ['nom' => 'ESP', 'code_pays' => 'ESP'],
                ['nom' => 'CPV', 'code_pays' => 'CPV'],
                ['nom' => 'KSA', 'code_pays' => 'KSA'],
                ['nom' => 'URU', 'code_pays' => 'URU'],
            ],
            // Groupe I
            'I' => [
                ['nom' => 'BOL/SUR/IRQ', 'code_pays' => 'BOL'],
                ['nom' => 'FRA', 'code_pays' => 'FRA'],
                ['nom' => 'SEN', 'code_pays' => 'SEN'],
                ['nom' => 'NOR', 'code_pays' => 'NOR'],
            ],
            // Groupe J
            'J' => [
                ['nom' => 'ARG', 'code_pays' => 'ARG'],
                ['nom' => 'ALG', 'code_pays' => 'ALG'],
                ['nom' => 'AUT', 'code_pays' => 'AUT'],
                ['nom' => 'JOR', 'code_pays' => 'JOR'],
            ],
            // Groupe K
            'K' => [
                ['nom' => 'NCL/JAM/COD', 'code_pays' => 'NCL'],
                ['nom' => 'POR', 'code_pays' => 'POR'],
                ['nom' => 'UZB', 'code_pays' => 'UZB'],
                ['nom' => 'COL', 'code_pays' => 'COL'],
            ],
            // Groupe L
            'L' => [
                ['nom' => 'ENG', 'code_pays' => 'ENG'],
                ['nom' => 'CRO', 'code_pays' => 'CRO'],
                ['nom' => 'GHA', 'code_pays' => 'GHA'],
                ['nom' => 'PAN', 'code_pays' => 'PAN'],
            ],
        ];

        foreach ($equipesParPoule as $lettrePoule => $equipes) {
            $poule = Poule::where('nom', $lettrePoule)->first();

            if (! $poule) {
                continue;
            }

            foreach ($equipes as $data) {
                Equipe::firstOrCreate(
                    [
                        'nom' => $data['nom'],
                    ],
                    [
                        'nom' => $data['nom'],
                        'code_pays' => $data['code_pays'],
                        'poule_id' => $poule->id,
                    ]
                );
            }
        }
    }
}

