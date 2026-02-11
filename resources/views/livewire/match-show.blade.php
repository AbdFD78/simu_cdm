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
        $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
        $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
        $flagA = !$isBarrageA && $match->equipeA && $match->equipeA->code_pays ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
        $flagB = !$isBarrageB && $match->equipeB && $match->equipeB->code_pays ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">
                @if($isBarrageA)
                    <span class="me-2" title="Barrages">⚔️</span>
                @elseif($flagA)
                    <span class="fi {{ $flagA }} me-2"></span>
                @endif
                {{ $match->equipeA->nom ?? 'Équipe A' }}
                vs
                @if($isBarrageB)
                    <span class="me-2" title="Barrages">⚔️</span>
                @elseif($flagB)
                    <span class="fi {{ $flagB }} me-2"></span>
                @endif
                {{ $match->equipeB->nom ?? 'Équipe B' }}
            </h1>
            <p class="text-muted mb-0">
                Phase : {{ $match->phase->nom ?? 'N/A' }}
                @if($match->poule)
                    &middot; Poule {{ $match->poule->nom }}
                @endif
            </p>
        </div>
        @if($match->poule)
            <a href="{{ route('poules.show', $match->poule) }}"
               class="btn btn-outline-secondary btn-sm">
                &larr; Retour
            </a>
        @elseif($match->phase)
            <a href="{{ route('phases.show', $match->phase) }}"
               class="btn btn-outline-secondary btn-sm">
                &larr; Retour
            </a>
        @else
            <a href="{{ route('phases.index') }}"
               class="btn btn-outline-secondary btn-sm">
                &larr; Retour
            </a>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Informations générales</h2>

                    <dl class="row mb-0">
                        <dt class="col-5">Équipe A</dt>
                        <dd class="col-7">
                            @if($isBarrageA)
                                <span class="me-2" title="Barrages">⚔️</span>
                            @elseif($flagA)
                                <span class="fi {{ $flagA }} me-2"></span>
                            @endif
                            {{ $match->equipeA->nom ?? 'N/A' }}
                        </dd>

                        <dt class="col-5">Équipe B</dt>
                        <dd class="col-7">
                            @if($isBarrageB)
                                <span class="me-2" title="Barrages">⚔️</span>
                            @elseif($flagB)
                                <span class="fi {{ $flagB }} me-2"></span>
                            @endif
                            {{ $match->equipeB->nom ?? 'N/A' }}
                        </dd>

                        <dt class="col-5">Score</dt>
                        <dd class="col-7">
                            {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                        </dd>

                        <dt class="col-5">Statut</dt>
                        <dd class="col-7">
                            <span class="badge text-bg-secondary">
                                {{ $match->statut_libelle }}
                            </span>
                        </dd>

                        <dt class="col-5">Date / heure</dt>
                        <dd class="col-7">
                            {{ optional($match->date_heure)->format('d/m/Y H:i') ?? '—' }}
                        </dd>

                        <dt class="col-5">Stade</dt>
                        <dd class="col-7">{{ $match->stade ?? '—' }}</dd>

                        <dt class="col-5">Ville</dt>
                        <dd class="col-7">{{ $match->ville ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    @livewire('match-live-panel', ['match' => $match])
                </div>
            </div>
        </div>
    </div>
</div>

