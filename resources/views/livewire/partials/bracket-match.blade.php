@php
    $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
    $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
    $flagA = (!$isBarrageA && $match->equipeA && $match->equipeA->code_pays) ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
    $flagB = (!$isBarrageB && $match->equipeB && $match->equipeB->code_pays) ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
@endphp
<div class="bracket-match-card">
    <div class="bracket-team-line">
        <div class="bracket-team-info">
            @if($isBarrageA)<span style="font-size: 1em;">&#x2694;</span>
            @elseif($flagA)<span class="fi {{ $flagA }}"></span>
            @endif
            <span class="text-truncate">{{ $match->equipeA->nom ?? 'Équipe A' }}</span>
        </div>
        <span class="bracket-score">{{ $match->score_equipe_a }}</span>
    </div>
    <div class="bracket-team-line">
        <div class="bracket-team-info">
            @if($isBarrageB)<span style="font-size: 1em;">&#x2694;</span>
            @elseif($flagB)<span class="fi {{ $flagB }}"></span>
            @endif
            <span class="text-truncate">{{ $match->equipeB->nom ?? 'Équipe B' }}</span>
        </div>
        <span class="bracket-score">{{ $match->score_equipe_b }}</span>
    </div>
</div>
