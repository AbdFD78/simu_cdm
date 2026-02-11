<?php

namespace Database\Factories;

use App\Models\Classement;
use App\Models\Equipe;
use App\Models\Poule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classement>
 */
class ClassementFactory extends Factory
{
    protected $model = Classement::class;

    public function definition(): array
    {
        return [
            'poule_id' => Poule::factory(),
            'equipe_id' => Equipe::factory(),
            'points' => fake()->numberBetween(0, 9),
            'matchs_joues' => fake()->numberBetween(0, 3),
            'victoires' => fake()->numberBetween(0, 3),
            'nuls' => fake()->numberBetween(0, 3),
            'defaites' => fake()->numberBetween(0, 3),
            'buts_marques' => fake()->numberBetween(0, 10),
            'buts_encaissees' => fake()->numberBetween(0, 10),
        ];
    }
}
