<div>
    @php
        $flagClasses = [
            'MEX' => 'fi-mx', 'RSA' => 'fi-za', 'KOR' => 'fi-kr', 'DEN' => 'fi-dk',
            'CAN' => 'fi-ca', 'QAT' => 'fi-qa', 'SUI' => 'fi-ch', 'ITA' => 'fi-it',
            'BRA' => 'fi-br', 'MAR' => 'fi-ma', 'HAI' => 'fi-ht', 'SCO' => 'fi-gb',
            'USA' => 'fi-us', 'PAR' => 'fi-py', 'AUS' => 'fi-au', 'TUR' => 'fi-tr',
            'GER' => 'fi-de', 'CUW' => 'fi-cw', 'CIV' => 'fi-ci', 'ECU' => 'fi-ec',
            'NED' => 'fi-nl', 'JPN' => 'fi-jp', 'TUN' => 'fi-tn', 'UKR' => 'fi-ua',
            'BEL' => 'fi-be', 'EGY' => 'fi-eg', 'IRN' => 'fi-ir', 'NZL' => 'fi-nz',
            'ESP' => 'fi-es', 'CPV' => 'fi-cv', 'KSA' => 'fi-sa', 'URU' => 'fi-uy',
            'BOL' => 'fi-bo', 'FRA' => 'fi-fr', 'SEN' => 'fi-sn', 'NOR' => 'fi-no',
            'ARG' => 'fi-ar', 'ALG' => 'fi-dz', 'AUT' => 'fi-at', 'JOR' => 'fi-jo',
            'NCL' => 'fi-nc', 'POR' => 'fi-pt', 'UZB' => 'fi-uz', 'COL' => 'fi-co',
            'ENG' => 'fi-gb', 'CRO' => 'fi-hr', 'GHA' => 'fi-gh', 'PAN' => 'fi-pa',
        ];
    @endphp

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
                                @php
                                    $equipe = $classement->equipe;
                                    $isPlayoff = $equipe && str_contains($equipe->nom ?? '', '/');
                                    $flagClass = !$isPlayoff && $equipe && $equipe->code_pays ? ($flagClasses[$equipe->code_pays] ?? null) : null;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($isPlayoff)
                                            <span class="me-2" title="Barrages">⚔️</span>
                                        @elseif($flagClass)
                                            <span class="fi {{ $flagClass }} me-2"></span>
                                        @endif
                                        {{ $classement->equipe->nom ?? 'N/A' }}
                                    </td>
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
                                <th class="text-end"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($matchs as $match)
                                @php
                                    $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
                                    $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
                                    $flagA = !$isBarrageA && $match->equipeA && $match->equipeA->code_pays ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
                                    $flagB = !$isBarrageB && $match->equipeB && $match->equipeB->code_pays ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
                                @endphp
                                <tr role="button" tabindex="0" onclick="window.location='{{ route('matchs.show', $match) }}'" style="cursor: pointer;">
                                    <td>
                                        @if($isBarrageA)
                                            <span class="me-1" title="Barrages">⚔️</span>
                                        @elseif($flagA)
                                            <span class="fi {{ $flagA }} me-1"></span>
                                        @endif
                                        {{ $match->equipeA->nom ?? 'Équipe A' }}
                                        vs
                                        @if($isBarrageB)
                                            <span class="me-1" title="Barrages">⚔️</span>
                                        @elseif($flagB)
                                            <span class="fi {{ $flagB }} me-1"></span>
                                        @endif
                                        {{ $match->equipeB->nom ?? 'Équipe B' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge text-bg-secondary">
                                            {{ $match->statut_libelle }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ optional($match->date_heure)->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="text-end" onclick="event.stopPropagation();">
                                        <a href="{{ route('matchs.show', $match) }}" class="btn btn-outline-primary btn-sm">
                                            Voir
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

