<?php

namespace Database\Factories;

use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phase>
 */
class PhaseFactory extends Factory
{
    protected $model = Phase::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->words(2, true),
            'ordre' => fake()->numberBetween(1, 10),
            'type_phase' => fake()->randomElement(['group', 'knockout']),
        ];
    }
}
