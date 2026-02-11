<?php

namespace Tests\Feature;

use App\Models\Classement;
use App\Models\Equipe;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use App\Services\MatchSimulationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_simulate_poule_updates_all_matches_to_finished(): void
    {
        $phase = Phase::factory()->create([
            'nom' => 'Phase de groupes',
            'type_phase' => 'group',
        ]);

        $poule = Poule::factory()->create([
            'phase_id' => $phase->id,
            'nom' => 'A',
        ]);

        $equipe1 = Equipe::factory()->create(['poule_id' => $poule->id]);
        $equipe2 = Equipe::factory()->create(['poule_id' => $poule->id]);

        $match = MatchModel::factory()->create([
            'phase_id' => $phase->id,
            'poule_id' => $poule->id,
            'equipe_a_id' => $equipe1->id,
            'equipe_b_id' => $equipe2->id,
            'statut' => 'scheduled',
            'score_equipe_a' => 0,
            'score_equipe_b' => 0,
        ]);

        $service = app(MatchSimulationService::class);
        $service->simulatePoule($poule);

        $match->refresh();
        $this->assertEquals('finished', $match->statut);
        $this->assertGreaterThanOrEqual(0, $match->score_equipe_a);
        $this->assertGreaterThanOrEqual(0, $match->score_equipe_b);
    }

    public function test_simulate_poule_recalculates_classements(): void
    {
        $phase = Phase::factory()->create([
            'nom' => 'Phase de groupes',
            'type_phase' => 'group',
        ]);

        $poule = Poule::factory()->create([
            'phase_id' => $phase->id,
            'nom' => 'A',
        ]);

        $equipe1 = Equipe::factory()->create(['poule_id' => $poule->id]);
        $equipe2 = Equipe::factory()->create(['poule_id' => $poule->id]);

        // Créer les classements initiaux
        Classement::factory()->create([
            'poule_id' => $poule->id,
            'equipe_id' => $equipe1->id,
            'points' => 0,
            'matchs_joues' => 0,
        ]);

        Classement::factory()->create([
            'poule_id' => $poule->id,
            'equipe_id' => $equipe2->id,
            'points' => 0,
            'matchs_joues' => 0,
        ]);

        $match = MatchModel::factory()->create([
            'phase_id' => $phase->id,
            'poule_id' => $poule->id,
            'equipe_a_id' => $equipe1->id,
            'equipe_b_id' => $equipe2->id,
            'statut' => 'scheduled',
        ]);

        $service = app(MatchSimulationService::class);
        $service->simulatePoule($poule);

        $classement1 = Classement::where('poule_id', $poule->id)
            ->where('equipe_id', $equipe1->id)
            ->first();

        $this->assertNotNull($classement1);
        $this->assertEquals(1, $classement1->matchs_joues);
        $this->assertGreaterThanOrEqual(0, $classement1->points);
    }
}
