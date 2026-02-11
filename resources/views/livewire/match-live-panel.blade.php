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

    @if($match->statut === 'live' || $isSimulating)
        {{-- Activer le polling toutes les 5 secondes quand le match est en cours --}}
        <div wire:poll.5s="updateMatch">
            <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
                <span class="badge text-bg-danger">En cours</span>
                @if($isSimulating)
                    <span class="fw-bold">{{ $currentMinute }}'</span>
                @endif
            </div>
        </div>
    @endif

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="h5 mb-0">Score</h3>
            @if($match->statut === 'scheduled' && !$isSimulating)
                <button type="button" class="btn btn-primary btn-sm" wire:click="startSimulation">
                    Lancer la simulation (30s)
                </button>
            @elseif($match->statut === 'live' || $isSimulating)
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-danger">En cours</span>
                    <span class="fw-bold">{{ $currentMinute }}'</span>
                </div>
            @endif
        </div>

        <div class="text-center py-3 border rounded bg-light">
            <div class="row align-items-center">
                <div class="col-5">
                    <div class="fw-bold">
                        @if($isBarrageA)
                            <span class="me-2" title="Barrages">⚔️</span>
                        @elseif($flagA)
                            <span class="fi {{ $flagA }} me-2"></span>
                        @endif
                        {{ $match->equipeA->nom ?? 'Équipe A' }}
                    </div>
                </div>
                <div class="col-2">
                    <div class="display-6 fw-bold">
                        {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                    </div>
                </div>
                <div class="col-5">
                    <div class="fw-bold">
                        @if($isBarrageB)
                            <span class="me-2" title="Barrages">⚔️</span>
                        @elseif($flagB)
                            <span class="fi {{ $flagB }} me-2"></span>
                        @endif
                        {{ $match->equipeB->nom ?? 'Équipe B' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h3 class="h5 mb-3">Timeline des événements</h3>

        @if($evenements->isEmpty())
            <p class="text-muted mb-0">Aucun événement pour le moment.</p>
        @else
            {{-- Timeline stylée avec ligne verticale centrale --}}
            <div class="position-relative" style="padding-left: 60px;">
                {{-- Ligne verticale centrale --}}
                <div class="position-absolute top-0 start-0" style="width: 2px; height: 100%; background-color: #dee2e6; left: 30px;"></div>

                @foreach($evenements as $index => $evenement)
                    <div class="position-relative mb-3" style="min-height: 50px;">
                        {{-- Point sur la ligne centrale --}}
                        <div class="position-absolute rounded-circle bg-primary" style="width: 12px; height: 12px; left: 24px; top: 4px; border: 2px solid white; box-shadow: 0 0 0 2px #dee2e6;"></div>

                        {{-- Contenu de l'événement --}}
                        <div class="d-flex align-items-start gap-3">
                            {{-- Icône et description à gauche --}}
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    @if($evenement->type === 'goal')
                                        <span class="me-3" style="font-size: 1.2em; min-width: 28px; text-align: center;">⚽</span>
                                    @elseif($evenement->type === 'yellow_card')
                                        <span class="badge text-bg-warning me-3" style="width: 24px; height: 24px; min-width: 24px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">🟨</span>
                                    @elseif($evenement->type === 'red_card')
                                        <span class="badge text-bg-danger me-3" style="width: 24px; height: 24px; min-width: 24px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">🟥</span>
                                    @elseif($evenement->type === 'substitution')
                                        <span class="me-3" style="font-size: 1.1em; min-width: 28px; text-align: center;">🔄</span>
                                    @endif

                                    <span class="fw-semibold">
                                        @if($evenement->type === 'goal')
                                            {{ $evenement->description ?? 'BUT' }}
                                        @else
                                            {{ $evenement->description ?? ucfirst(str_replace('_', ' ', $evenement->type)) }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Minute à droite --}}
                            <div class="text-end" style="min-width: 50px;">
                                <span class="badge text-bg-primary fw-bold">{{ $evenement->minute }}'</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
