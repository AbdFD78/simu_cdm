<?php

namespace Tests\Feature;

use App\Models\Phase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhasePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_phases_index_page_returns_200(): void
    {
        // Créer au moins une phase pour le test
        Phase::factory()->create([
            'nom' => 'Phase de groupes',
            'ordre' => 1,
            'type_phase' => 'group',
        ]);

        $response = $this->get('/phases');

        $response->assertStatus(200);
        $response->assertSee('Phase de groupes');
    }

    public function test_phase_show_page_returns_200_for_existing_phase(): void
    {
        $phase = Phase::factory()->create([
            'nom' => 'Phase de groupes',
            'ordre' => 1,
            'type_phase' => 'group',
        ]);

        $response = $this->get("/phases/{$phase->id}");

        $response->assertStatus(200);
        $response->assertSee('Phase de groupes');
    }

    public function test_phase_show_page_returns_404_for_non_existing_phase(): void
    {
        $response = $this->get('/phases/99999');

        $response->assertStatus(404);
    }
}
