<?php

namespace App\Livewire;

use App\Models\Classement;
use App\Models\MatchModel;
use App\Models\Phase;
use App\Models\Poule;
use App\Services\KnockoutGenerationService;
use App\Services\LiveMatchSimulationService;
use App\Services\MatchSimulationService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PhaseList extends Component
{
    /**
     * Réinitialise la compétition dans un état proche juste après les seeders :
     * - suppression de tous les événements de match
     * - remise à zéro des scores et statuts des matchs de poules
     * - suppression des matchs des phases à élimination directe (ils seront régénérés)
     * - remise à zéro des classements.
     */
    public function resetCompetition(): void
    {
        DB::transaction(function () {
            // Supprimer tous les événements
            DB::table('evenement_matchs')->delete();

            // Identifier la phase de groupes
            $phaseGroupes = Phase::where('type_phase', 'group')->first();

            // Remettre à zéro tous les matchs
            $query = MatchModel::query();

            if ($phaseGroupes) {
                // On supprime les matchs des phases à élimination directe
                $query->where('phase_id', '!=', $phaseGroupes->id)->delete();

                // Et on remet à zéro uniquement les matchs de groupes
                MatchModel::where('phase_id', $phaseGroupes->id)->update([
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                ]);
            } else {
                // Cas de fallback : on remet à zéro tous les matchs
                MatchModel::query()->update([
                    'statut' => 'scheduled',
                    'score_equipe_a' => 0,
                    'score_equipe_b' => 0,
                ]);
            }

            // Remise à zéro de tous les classements
            Classement::query()->update([
                'points' => 0,
                'matchs_joues' => 0,
                'victoires' => 0,
                'nuls' => 0,
                'defaites' => 0,
                'buts_marques' => 0,
                'buts_encaissees' => 0,
            ]);
        });
    }

    /**
     * Simule instantanément tous les matchs de seizièmes encore "scheduled".
     */
    public function simulateAllSeiziemes(): void
    {
        $phaseSeiziemes = Phase::where('nom', 'Seizièmes de finale')->first();
        if (! $phaseSeiziemes) {
            return;
        }

        $service = app(MatchSimulationService::class);
        $service->simulatePhase($phaseSeiziemes);
    }

    /**
     * Traite les ticks de simulation pour tous les matchs live (appelé via wire:poll).
     */
    public function processLiveMatches(): void
    {
        $liveService = app(LiveMatchSimulationService::class);
        $matchSimulationService = app(MatchSimulationService::class);
        $knockoutService = app(KnockoutGenerationService::class);

        // Récupérer tous les matchs live
        $liveMatches = MatchModel::where('statut', 'live')->get();

        foreach ($liveMatches as $match) {
            if ($liveService->isLiveSimulationActive($match)) {
                $result = $liveService->processTick($match);

                // Si le match est terminé et c'est un match de poule, recalculer les classements
                if ($result['finished'] ?? false) {
                    if ($match->poule_id) {
                        $poule = Poule::find($match->poule_id);
                        if ($poule) {
                            $matchSimulationService->recalculateClassements($poule);

                            // Vérifier si toutes les poules sont terminées et générer les seizièmes
                            if ($knockoutService->areAllGroupMatchesFinished()) {
                                $knockoutService->generateSeiziemesDeFinale();
                            }
                        }
                    }
                }
            }
        }
    }

    public function render()
    {
        // Traiter les matchs live si nécessaire (via polling)
        $this->processLiveMatches();

        $phases = Phase::orderBy('ordre')->get();

        // Prochains matchs : seizièmes de finale (scheduled)
        $phaseSeiziemes = Phase::where('nom', 'Seizièmes de finale')->first();
        $nextSeiziemes = collect();
        if ($phaseSeiziemes) {
            $nextSeiziemes = MatchModel::with(['equipeA', 'equipeB'])
                ->where('phase_id', $phaseSeiziemes->id)
                ->where('statut', 'scheduled')
                ->orderBy('date_heure')
                ->get();
        }

        // Matchs en cours (toutes phases confondues)
        $liveMatches = MatchModel::with(['equipeA', 'equipeB', 'phase', 'poule'])
            ->where('statut', 'live')
            ->orderBy('date_heure')
            ->get();

        return view('livewire.phase-list', [
            'phases' => $phases,
            'nextSeiziemes' => $nextSeiziemes,
            'liveMatches' => $liveMatches,
        ])->layout('layouts.app');
    }
}

