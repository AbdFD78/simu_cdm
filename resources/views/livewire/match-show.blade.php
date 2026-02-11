<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">
                {{ $match->equipeA->nom ?? 'Équipe A' }}
                vs
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
                        <dd class="col-7">{{ $match->equipeA->nom ?? 'N/A' }}</dd>

                        <dt class="col-5">Équipe B</dt>
                        <dd class="col-7">{{ $match->equipeB->nom ?? 'N/A' }}</dd>

                        <dt class="col-5">Score</dt>
                        <dd class="col-7">
                            {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                        </dd>

                        <dt class="col-5">Statut</dt>
                        <dd class="col-7">
                            <span class="badge text-bg-secondary text-capitalize">
                                {{ $match->statut }}
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

