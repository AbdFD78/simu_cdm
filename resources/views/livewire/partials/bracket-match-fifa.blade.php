@php
    $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
    $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
    $flagA = (!$isBarrageA && $match->equipeA && $match->equipeA->code_pays) ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
    $flagB = (!$isBarrageB && $match->equipeB && $match->equipeB->code_pays) ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
@endphp
<div class="fifa-match">
    <div class="fifa-match-row">
        <span class="team">
            @if($isBarrageA)<span aria-hidden="true">&#x2694;</span>
            @elseif($flagA)<span class="fi {{ $flagA }}"></span>
            @endif
            <span class="text-truncate">{{ $match->equipeA->nom ?? 'Équipe A' }}</span>
        </span>
        <span class="score">{{ $match->score_equipe_a }}</span>
    </div>
    <div class="fifa-match-row">
        <span class="team">
            @if($isBarrageB)<span aria-hidden="true">&#x2694;</span>
            @elseif($flagB)<span class="fi {{ $flagB }}"></span>
            @endif
            <span class="text-truncate">{{ $match->equipeB->nom ?? 'Équipe B' }}</span>
        </span>
        <span class="score">{{ $match->score_equipe_b }}</span>
    </div>
</div>
