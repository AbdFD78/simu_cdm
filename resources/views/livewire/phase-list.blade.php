<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Phases de la Coupe du Monde 2026</h1>
    </div>

    <div class="row g-3">
        @forelse($phases as $phase)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 card-title">{{ $phase->nom }}</h2>
                        <p class="card-text text-muted mb-2">
                            Type :
                            @if($phase->type_phase === 'group')
                                Phase de groupes
                            @else
                                Phase à élimination directe
                            @endif
                        </p>
                        <p class="card-text text-muted small mb-3">
                            Ordre : {{ $phase->ordre }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('phases.show', $phase) }}" class="btn btn-primary btn-sm">
                                Voir la phase
                            </a>
                        </div>
                    </div>
                </div>
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

