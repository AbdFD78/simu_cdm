<?php

namespace Database\Factories;

use App\Models\Equipe;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MatchModel>
 */
class MatchModelFactory extends Factory
{
    protected $model = MatchModel::class;

    public function definition(): array
    {
        return [
            'phase_id' => Phase::factory(),
            'poule_id' => Poule::factory(),
            'equipe_a_id' => Equipe::factory(),
            'equipe_b_id' => Equipe::factory(),
            'date_heure' => fake()->dateTimeBetween('now', '+1 year'),
            'statut' => fake()->randomElement(['scheduled', 'live', 'finished']),
            'score_equipe_a' => fake()->numberBetween(0, 4),
            'score_equipe_b' => fake()->numberBetween(0, 4),
            'stade' => fake()->optional()->words(2, true),
            'ville' => fake()->optional()->city(),
        ];
    }
}
