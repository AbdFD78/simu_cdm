<?php

namespace Database\Factories;

use App\Models\Phase;
use App\Models\Poule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poule>
 */
class PouleFactory extends Factory
{
    protected $model = Poule::class;

    public function definition(): array
    {
        return [
            'phase_id' => Phase::factory(),
            'nom' => fake()->randomLetter(),
        ];
    }
}
