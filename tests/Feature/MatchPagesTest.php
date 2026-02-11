<?php

namespace Tests\Feature;

use App\Models\Equipe;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_match_show_page_returns_200_for_existing_match(): void
    {
        $phase = Phase::factory()->create([
            'nom' => 'Phase de groupes',
            'type_phase' => 'group',
        ]);

        $poule = Poule::factory()->create([
            'phase_id' => $phase->id,
            'nom' => 'A',
        ]);

        $equipeA = Equipe::factory()->create(['nom' => 'France', 'code_pays' => 'FRA']);
        $equipeB = Equipe::factory()->create(['nom' => 'Brésil', 'code_pays' => 'BRA']);

        $match = MatchModel::factory()->create([
            'phase_id' => $phase->id,
            'poule_id' => $poule->id,
            'equipe_a_id' => $equipeA->id,
            'equipe_b_id' => $equipeB->id,
            'statut' => 'scheduled',
        ]);

        $response = $this->get("/matchs/{$match->id}");

        $response->assertStatus(200);
        $response->assertSee('France');
        $response->assertSee('Brésil');
    }

    public function test_match_show_page_returns_404_for_non_existing_match(): void
    {
        $response = $this->get('/matchs/99999');

        $response->assertStatus(404);
    }
}
