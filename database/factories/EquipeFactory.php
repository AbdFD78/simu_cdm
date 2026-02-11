<?php

namespace Database\Factories;

use App\Models\Equipe;
use App\Models\Poule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipe>
 */
class EquipeFactory extends Factory
{
    protected $model = Equipe::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->country(),
            'code_pays' => strtoupper(fake()->unique()->lexify('???')),
            'poule_id' => Poule::factory(),
        ];
    }
}
