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
        {{-- Liste des matchs de la phase à élimination directe --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">Matchs de la phase</h2>

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
                                    Aucun match n'est encore planifié pour cette phase.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

