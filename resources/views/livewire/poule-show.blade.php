<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Poule {{ $poule->nom }}</h1>
            <p class="text-muted mb-0">
                Phase : {{ $poule->phase->nom ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('phases.show', $poule->phase) }}" class="btn btn-outline-secondary btn-sm">
            &larr; Retour à la phase
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Classement</h2>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Équipe</th>
                                <th class="text-center">Pts</th>
                                <th class="text-center">J</th>
                                <th class="text-center">V</th>
                                <th class="text-center">N</th>
                                <th class="text-center">D</th>
                                <th class="text-center">BM</th>
                                <th class="text-center">BE</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($classements as $index => $classement)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $classement->equipe->nom ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $classement->points }}</td>
                                    <td class="text-center">{{ $classement->matchs_joues }}</td>
                                    <td class="text-center">{{ $classement->victoires }}</td>
                                    <td class="text-center">{{ $classement->nuls }}</td>
                                    <td class="text-center">{{ $classement->defaites }}</td>
                                    <td class="text-center">{{ $classement->buts_marques }}</td>
                                    <td class="text-center">{{ $classement->buts_encaissees }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Aucun classement n'est encore disponible pour cette poule.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Matchs de la poule</h2>

                        {{-- Bouton de simulation globale --}}
                        @php
                            $hasScheduledMatches = $matchs->where('statut', 'scheduled')->count() > 0;
                        @endphp
                        @if($hasScheduledMatches)
                            <button type="button" class="btn btn-primary btn-sm" wire:click="simulatePoule">
                                Simuler toute la poule
                            </button>
                        @else
                            <span class="badge text-bg-success">Tous les matchs sont terminés</span>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Match</th>
                                <th class="text-center">Score</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Date / heure</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($matchs as $match)
                                <tr>
                                    <td>
                                        {{ $match->equipeA->nom ?? 'Équipe A' }}
                                        vs
                                        {{ $match->equipeB->nom ?? 'Équipe B' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge text-bg-secondary text-capitalize">
                                            {{ $match->statut }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ optional($match->date_heure)->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('matchs.show', $match) }}" class="btn btn-outline-primary btn-sm">
                                            Détail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Aucun match n'est encore planifié pour cette poule.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

