<?php

namespace Tests\Feature;

use App\Models\Phase;
use App\Models\Poule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoulePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_poule_show_page_returns_200_for_existing_poule(): void
    {
        $phase = Phase::factory()->create([
            'nom' => 'Phase de groupes',
            'type_phase' => 'group',
        ]);

        $poule = Poule::factory()->create([
            'phase_id' => $phase->id,
            'nom' => 'A',
        ]);

        $response = $this->get("/poules/{$poule->id}");

        $response->assertStatus(200);
        $response->assertSee('Poule A');
    }

    public function test_poule_show_page_returns_404_for_non_existing_poule(): void
    {
        $response = $this->get('/poules/99999');

        $response->assertStatus(404);
    }
}
