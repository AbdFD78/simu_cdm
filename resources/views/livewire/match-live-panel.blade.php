<div>
    @if($match->statut === 'live' || $isSimulating)
        {{-- Activer le polling toutes les 5 secondes quand le match est en cours --}}
        <div wire:poll.5s="updateMatch">
            <div class="alert alert-info mb-3">
                <strong>Match en direct</strong> &mdash; Mise à jour automatique toutes les 5 secondes
                @if($isSimulating)
                    <br><small>Simulation en cours (30 secondes)...</small>
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
                <span class="badge text-bg-danger">En cours</span>
            @endif
        </div>

        <div class="text-center py-3 border rounded bg-light">
            <div class="row align-items-center">
                <div class="col-5">
                    <div class="fw-bold">{{ $match->equipeA->nom ?? 'Équipe A' }}</div>
                </div>
                <div class="col-2">
                    <div class="display-6 fw-bold">
                        {{ $match->score_equipe_a }}&nbsp;-&nbsp;{{ $match->score_equipe_b }}
                    </div>
                </div>
                <div class="col-5">
                    <div class="fw-bold">{{ $match->equipeB->nom ?? 'Équipe B' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h3 class="h5 mb-3">Timeline des événements</h3>

        @if($evenements->isEmpty())
            <p class="text-muted mb-0">Aucun événement pour le moment.</p>
        @else
            <div class="list-group">
                @foreach($evenements as $evenement)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge text-bg-primary">{{ $evenement->minute }}'</span>
                                    <span class="badge
                                        @if($evenement->type === 'goal') text-bg-success
                                        @elseif($evenement->type === 'yellow_card') text-bg-warning
                                        @elseif($evenement->type === 'red_card') text-bg-danger
                                        @else text-bg-secondary
                                        @endif
                                    ">
                                        {{ match($evenement->type) {
                                            'goal' => 'BUT',
                                            'yellow_card' => 'Carton jaune',
                                            'red_card' => 'Carton rouge',
                                            'substitution' => 'Remplacement',
                                            default => $evenement->type,
                                        } }}
                                    </span>
                                </div>
                                @if($evenement->description)
                                    <div class="mt-1 small">{{ $evenement->description }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
