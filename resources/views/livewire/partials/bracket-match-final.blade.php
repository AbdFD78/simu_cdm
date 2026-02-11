@php
    $isBarrageA = $match->equipeA && str_contains($match->equipeA->nom ?? '', '/');
    $isBarrageB = $match->equipeB && str_contains($match->equipeB->nom ?? '', '/');
    $flagA = (!$isBarrageA && $match->equipeA && $match->equipeA->code_pays) ? ($flagClasses[$match->equipeA->code_pays] ?? null) : null;
    $flagB = (!$isBarrageB && $match->equipeB && $match->equipeB->code_pays) ? ($flagClasses[$match->equipeB->code_pays] ?? null) : null;
@endphp
<div class="bracket-final-card">
    <div class="bracket-final-line">
        <div class="bracket-final-team">
            @if($isBarrageA)<span style="font-size: 1.2em;">&#x2694;</span>
            @elseif($flagA)<span class="fi {{ $flagA }}" style="font-size: 1.1em;"></span>
            @endif
            <span class="text-truncate">{{ $match->equipeA->nom ?? 'Équipe A' }}</span>
        </div>
        <span class="bracket-final-score">{{ $match->score_equipe_a }}</span>
    </div>
    <div class="bracket-final-line">
        <div class="bracket-final-team">
            @if($isBarrageB)<span style="font-size: 1.2em;">&#x2694;</span>
            @elseif($flagB)<span class="fi {{ $flagB }}" style="font-size: 1.1em;"></span>
            @endif
            <span class="text-truncate">{{ $match->equipeB->nom ?? 'Équipe B' }}</span>
        </div>
        <span class="bracket-final-score">{{ $match->score_equipe_b }}</span>
    </div>
</div>
