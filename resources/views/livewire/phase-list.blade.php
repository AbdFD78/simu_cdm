<div wire:poll.5s="processLiveMatches">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Phases de la Coupe du Monde 2026</h1>

        <button type="button"
                class="btn btn-outline-danger btn-sm"
                wire:click="resetCompetition"
                wire:confirm="Réinitialiser toute la compétition ? Tous les scores et événements seront perdus.">
            Réinitialiser la compétition
        </button>
    </div>

    {{-- Matchs en cours (toutes phases confondues) --}}
    @if(isset($liveMatches) && $liveMatches->isNotEmpty())
        <div class="mb-4">
            <h2 class="h5 mb-3">Matchs en cours</h2>
            <div class="row g-3">
                @foreach($liveMatches as $match)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body py-2">
                                <div class="small text-muted mb-1">
                                    {{ $match->phase->nom ?? 'Phase' }}
                                    @if($match->poule)
                                        &middot; Poule {{ $match->poule->nom }}
                                    @endif
                                </div>
                                <div class="fw-semibold mb-1">
                                    {{ $match->equipeA->nom ?? 'Équipe A' }}
                                    <span class="text-muted">vs</span>
                                    {{ $match->equipeB->nom ?? 'Équipe B' }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-5">
                                        {{ $match->score_equipe_a }} - {{ $match->score_equipe_b }}
                                    </span>
                                    <span class="badge text-bg-danger text-capitalize">en direct</span>
                                </div>
                                <div class="small text-muted mb-2">
                                    {{ optional($match->date_heure)->format('d/m H:i') ?? 'En cours' }}
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('matchs.show', $match) }}" class="btn btn-outline-primary btn-sm">
                                        Voir le live
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Prochains matchs : Seizièmes de finale (avec collapse) --}}
    @if(isset($nextSeiziemes) && $nextSeiziemes->isNotEmpty())
        <div class="mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" wire:ignore>
                    <button class="btn btn-link text-decoration-none text-dark p-0 fw-semibold d-flex align-items-center gap-2"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseNextMatches"
                            aria-expanded="false"
                            aria-controls="collapseNextMatches"
                            id="collapseBtnNextMatches">
                        <span class="collapse-arrow" style="transition: transform 0.3s; display: inline-block;">▼</span>
                        <h2 class="h5 mb-0">Prochains matchs &mdash; Seizièmes de finale</h2>
                    </button>
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            wire:click="simulateAllSeiziemes">
                        Simuler tous les seizièmes (instantané)
                    </button>
                </div>
                <div class="collapse" id="collapseNextMatches" wire:ignore.self>
                    <div class="card-body">
                        @php
                            // Mapping code_pays (3 lettres) -> classe flag-icons (ISO alpha-2)
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
                            @foreach($nextSeiziemes as $match)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body py-2 d-flex flex-column">
                                            <div class="small text-muted mb-1">
                                                Seizièmes de finale
                                            </div>
                                            <div class="fw-semibold mb-1">
                                                @php
                                                    // Vérifier si c'est une équipe de barrages (nom contient "/")
                                                    $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
                                                    $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
                                                    
                                                    $flagA = null;
                                                    $flagB = null;
                                                    if (!$isBarrageA && $match->equipeA && $match->equipeA->code_pays) {
                                                        $flagA = $flagClasses[$match->equipeA->code_pays] ?? null;
                                                    }
                                                    if (!$isBarrageB && $match->equipeB && $match->equipeB->code_pays) {
                                                        $flagB = $flagClasses[$match->equipeB->code_pays] ?? null;
                                                    }
                                                @endphp
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
                                            <div class="small text-muted mb-2">
                                                {{ optional($match->date_heure)->format('d/m H:i') ?? 'Date à définir' }}
                                            </div>
                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <a href="{{ route('matchs.show', $match) }}" class="btn btn-outline-secondary btn-sm">
                                                    Détail / Simuler
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php
        $phaseColors = [
            'Phase de groupes' => ['bg' => 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)', 'border' => '#0a58ca', 'text' => '#fff'],
            'Seizièmes de finale' => ['bg' => 'linear-gradient(135deg, #8b0000 0%, #6b0000 100%)', 'border' => '#6b0000', 'text' => '#fff'],
            'Huitièmes de finale' => ['bg' => 'linear-gradient(135deg, #d2691e 0%, #a0522d 100%)', 'border' => '#a0522d', 'text' => '#fff'],
            'Quarts de finale' => ['bg' => 'linear-gradient(135deg, #ff8c00 0%, #cc7000 100%)', 'border' => '#cc7000', 'text' => '#fff'],
            'Demi-finales' => ['bg' => 'linear-gradient(135deg, #dc3545 0%, #b02a37 100%)', 'border' => '#b02a37', 'text' => '#fff'],
            'Petite finale' => ['bg' => 'linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%)', 'border' => '#5a32a3', 'text' => '#fff'],
            'Finale' => ['bg' => 'linear-gradient(135deg, #ffc107 0%, #d4a005 100%)', 'border' => '#d4a005', 'text' => '#212529'],
        ];
    @endphp

    <div class="row g-3">
        @forelse($phases as $phase)
            @php
                $style = $phaseColors[$phase->nom] ?? ['bg' => 'linear-gradient(135deg, #6c757d 0%, #495057 100%)', 'border' => '#495057', 'text' => '#fff'];
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('phases.show', $phase) }}" class="text-decoration-none d-block h-100 phase-card-link">
                    <div class="card h-100 shadow-sm border-0 phase-card"
                         style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; border-left: 4px solid {{ $style['border'] }} !important; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div class="card-body d-flex flex-column justify-content-center py-4">
                            <h2 class="h5 card-title mb-0 fw-bold">{{ $phase->nom }}</h2>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    Aucune phase n'est encore configurée.
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const collapseBtn = document.getElementById('collapseBtnNextMatches');
        const collapseElement = document.getElementById('collapseNextMatches');
        const arrow = collapseBtn?.querySelector('.collapse-arrow');
        
        if (collapseBtn && collapseElement && arrow) {
            collapseElement.addEventListener('show.bs.collapse', function() {
                arrow.style.transform = 'rotate(180deg)';
            });
            
            collapseElement.addEventListener('hide.bs.collapse', function() {
                arrow.style.transform = 'rotate(0deg)';
            });
        }
    });
</script>

