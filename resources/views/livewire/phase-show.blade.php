<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">{{ $phase->nom }}</h1>
            <p class="text-muted mb-0">
                @if($phase->type_phase === 'group')
                    Phase de groupes
                @else
                    Phase à élimination directe
                @endif
            </p>
        </div>
        <a href="{{ route('phases.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Retour aux phases
        </a>
    </div>

    @if($phase->type_phase === 'group')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Phase de groupes</h2>

            @php
                $hasScheduled = $phase->poules->flatMap->matchs->where('statut', 'scheduled')->count() > 0;
            @endphp

            @if($hasScheduled)
                <button type="button"
                        class="btn btn-primary btn-sm"
                        wire:click="simulateAllPoules"
                        onclick="return confirm('Simuler tous les matchs de toutes les poules ?');">
                    Simuler toutes les poules
                </button>
            @else
                <span class="badge text-bg-success">Tous les matchs de groupes sont terminés</span>
            @endif
        </div>

        {{-- Liste des poules avec mini classement --}}
        <div class="row g-3">
            @forelse($phase->poules as $poule)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title mb-3">Poule {{ $poule->nom }}</h2>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                    <tr>
                                        <th>Équipe</th>
                                        <th class="text-center">Pts</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        // Mapping code_pays (3 lettres) -> classe flag-icons (ISO alpha-2)
                                        $flagClasses = [
                                            'MEX' => 'fi-mx',
                                            'RSA' => 'fi-za',
                                            'KOR' => 'fi-kr',
                                            'DEN' => 'fi-dk',
                                            'CAN' => 'fi-ca',
                                            'QAT' => 'fi-qa',
                                            'SUI' => 'fi-ch',
                                            'ITA' => 'fi-it',
                                            'BRA' => 'fi-br',
                                            'MAR' => 'fi-ma',
                                            'HAI' => 'fi-ht',
                                            'SCO' => 'fi-gb', // Écosse : on utilise le drapeau UK
                                            'USA' => 'fi-us',
                                            'PAR' => 'fi-py',
                                            'AUS' => 'fi-au',
                                            'TUR' => 'fi-tr',
                                            'GER' => 'fi-de',
                                            'CUW' => 'fi-cw',
                                            'CIV' => 'fi-ci',
                                            'ECU' => 'fi-ec',
                                            'NED' => 'fi-nl',
                                            'JPN' => 'fi-jp',
                                            'TUN' => 'fi-tn',
                                            'UKR' => 'fi-ua',
                                            'BEL' => 'fi-be',
                                            'EGY' => 'fi-eg',
                                            'IRN' => 'fi-ir',
                                            'NZL' => 'fi-nz',
                                            'ESP' => 'fi-es',
                                            'CPV' => 'fi-cv',
                                            'KSA' => 'fi-sa',
                                            'URU' => 'fi-uy',
                                            'BOL' => 'fi-bo',
                                            'FRA' => 'fi-fr',
                                            'SEN' => 'fi-sn',
                                            'NOR' => 'fi-no',
                                            'ARG' => 'fi-ar',
                                            'ALG' => 'fi-dz',
                                            'AUT' => 'fi-at',
                                            'JOR' => 'fi-jo',
                                            'NCL' => 'fi-nc', // Nouvelle-Calédonie
                                            'POR' => 'fi-pt',
                                            'UZB' => 'fi-uz',
                                            'COL' => 'fi-co',
                                            'ENG' => 'fi-gb', // Angleterre : drapeau UK
                                            'CRO' => 'fi-hr',
                                            'GHA' => 'fi-gh',
                                            'PAN' => 'fi-pa',
                                        ];
                                    @endphp

                                    @foreach($poule->classements as $classement)
                                        <tr>
                                            <td>
                                                @php
                                                    $equipe = $classement->equipe;
                                                    $isPlayoff = $equipe && str_contains($equipe->nom, '/');
                                                @endphp

                                                {{-- Drapeau de l'équipe (flag-icons) ou icône spéciale pour les barrages --}}
                                                @if($isPlayoff)
                                                    <span class="me-2" title="Barrages">⚔️</span>
                                                @else
                                                    @php
                                                        $flagClass = $equipe && $equipe->code_pays
                                                            ? ($flagClasses[$equipe->code_pays] ?? null)
                                                            : null;
                                                    @endphp
                                                    @if($flagClass)
                                                        <span class="fi {{ $flagClass }} me-2"></span>
                                                    @else
                                                        <span class="me-2">🏳️</span>
                                                    @endif
                                                @endif

                                                {{ $equipe->nom ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">{{ $classement->points }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-auto">
                                <a href="{{ route('poules.show', $poule) }}" class="btn btn-primary btn-sm">
                                    Voir la poule
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        Aucune poule n'est encore associée à cette phase.
                    </div>
                </div>
            @endforelse
        </div>
    @else
        {{-- Affichage type tableau (bracket) pour les phases à élimination directe --}}
        @php
            $total = $matchs->count();
            $half = (int) ceil($total / 2);
            $leftMatches = $matchs->slice(0, $half);
            $rightMatches = $matchs->slice($half)->values();
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

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                @forelse($leftMatches as $match)
                    @php
                        $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
                        $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
                        $flagA = !$isBarrageA && $match->equipeA && $match->equipeA->code_pays ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
                        $flagB = !$isBarrageB && $match->equipeB && $match->equipeB->code_pays ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
                    @endphp
                    <a href="{{ route('matchs.show', $match) }}" class="text-decoration-none text-dark d-block">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">
                                            @if($isBarrageA)
                                                <span class="me-1" title="Barrages">⚔️</span>
                                            @elseif($flagA)
                                                <span class="fi {{ $flagA }} me-1"></span>
                                            @endif
                                            {{ $match->equipeA->nom ?? 'Équipe A' }}
                                            <span class="text-muted">vs</span>
                                            @if($isBarrageB)
                                                <span class="me-1" title="Barrages">⚔️</span>
                                            @elseif($flagB)
                                                <span class="fi {{ $flagB }} me-1"></span>
                                            @endif
                                            {{ $match->equipeB->nom ?? 'Équipe B' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ optional($match->date_heure)->format('d/m/Y H:i') ?? 'Date à définir' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">
                                            {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                                        </div>
                                        <div class="small">
                                            <span class="badge text-bg-secondary">
                                                {{ $match->statut_libelle }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-muted">Aucun match n'est encore planifié pour cette phase.</p>
                @endforelse
            </div>

            <div class="col-12 col-lg-6">
                @foreach($rightMatches as $match)
                    @php
                        $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
                        $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
                        $flagA = !$isBarrageA && $match->equipeA && $match->equipeA->code_pays ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
                        $flagB = !$isBarrageB && $match->equipeB && $match->equipeB->code_pays ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
                    @endphp
                    <a href="{{ route('matchs.show', $match) }}" class="text-decoration-none text-dark d-block">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">
                                            @if($isBarrageA)
                                                <span class="me-1" title="Barrages">⚔️</span>
                                            @elseif($flagA)
                                                <span class="fi {{ $flagA }} me-1"></span>
                                            @endif
                                            {{ $match->equipeA->nom ?? 'Équipe A' }}
                                            <span class="text-muted">vs</span>
                                            @if($isBarrageB)
                                                <span class="me-1" title="Barrages">⚔️</span>
                                            @elseif($flagB)
                                                <span class="fi {{ $flagB }} me-1"></span>
                                            @endif
                                            {{ $match->equipeB->nom ?? 'Équipe B' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ optional($match->date_heure)->format('d/m/Y H:i') ?? 'Date à définir' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">
                                            {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                                        </div>
                                        <div class="small">
                                            <span class="badge text-bg-secondary">
                                                {{ $match->statut_libelle }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

