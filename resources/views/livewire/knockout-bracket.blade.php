<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Tableau des phases finales</h1>
    </div>

    @if($phasesWithMatches->isEmpty())
        <div class="alert alert-info">
            Aucun match de phase finale n'a encore été généré. Simule d'abord tous les matchs de groupes.
        </div>
    @else
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
            $seiziemes = $phasesWithMatches->firstWhere('phase.nom', 'Seizièmes de finale')['matchs'] ?? collect();
            $huitiemes = $phasesWithMatches->firstWhere('phase.nom', 'Huitièmes de finale')['matchs'] ?? collect();
            $quarts = $phasesWithMatches->firstWhere('phase.nom', 'Quarts de finale')['matchs'] ?? collect();
            $demies = $phasesWithMatches->firstWhere('phase.nom', 'Demi-finales')['matchs'] ?? collect();
            $petiteFinale = $phasesWithMatches->firstWhere('phase.nom', 'Petite finale')['matchs'] ?? collect();
            $finale = $phasesWithMatches->firstWhere('phase.nom', 'Finale')['matchs'] ?? collect();
            $seiziemesInfo = $phasesWithMatches->firstWhere('phase.nom', 'Seizièmes de finale') ?? null;
            $huitiemesInfo = $phasesWithMatches->firstWhere('phase.nom', 'Huitièmes de finale') ?? null;
            $quartsInfo = $phasesWithMatches->firstWhere('phase.nom', 'Quarts de finale') ?? null;
            $demiesInfo = $phasesWithMatches->firstWhere('phase.nom', 'Demi-finales') ?? null;
            $finaleInfo = $phasesWithMatches->firstWhere('phase.nom', 'Finale') ?? null;

            $headerH = 44;
            $matchH = 68;
            $matchGap = 8;
            $slotH = $matchH + $matchGap;
            $pairH = $slotH * 2;
        @endphp

        <style>
            .fifa-bracket { background: #e8e8e8; padding: 24px; border-radius: 8px; overflow-x: auto; }
            .fifa-bracket-inner { display: flex; justify-content: center; align-items: flex-start; gap: 0; min-width: min-content; max-width: 100%; }
            .fifa-side { display: flex; align-items: stretch; }
            .fifa-round { display: flex; flex-direction: column; width: 170px; flex-shrink: 0; }
            .fifa-round-header { background: #8b1538; color: #fff; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.02em; padding: 10px 8px; text-align: center; border-radius: 4px 4px 0 0; min-height: {{ $headerH }}px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; flex-wrap: wrap; }
            .fifa-round-body { display: flex; flex-direction: column; flex: 1; }
            .fifa-pair { height: {{ $pairH }}px; flex-shrink: 0; display: flex; flex-direction: column; padding: 4px 0; box-sizing: border-box; }
            .fifa-pair .fifa-match + .fifa-match { margin-top: {{ $matchGap }}px; }
            .fifa-pair-slot { flex-shrink: 0; display: flex; align-items: flex-start; }
            .fifa-pair-slot .fifa-match-wrap { width: 100%; }
            .fifa-pair-slot.fifa-slot-one { height: {{ $pairH }}px; }
            .fifa-pair-slot.fifa-slot-two { height: {{ $pairH * 2 }}px; }
            .fifa-pair-slot.fifa-slot-quad { height: {{ $pairH * 4 }}px; }
            .fifa-connector { width: 28px; flex-shrink: 0; display: flex; flex-direction: column; }
            .fifa-connector-head { height: {{ $headerH }}px; flex-shrink: 0; }
            .fifa-connector-seg { height: {{ $pairH }}px; position: relative; flex-shrink: 0; }
            .fifa-connector-seg.fifa-connector-seg-double { height: {{ $pairH * 2 }}px; }
            .fifa-connector-seg.fifa-connector-seg-quad { height: {{ $pairH * 4 }}px; }
            .fifa-connector-seg svg { position: absolute; inset: 0; width: 100%; height: 100%; display: block; }
            .fifa-match { background: #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); border: 1px solid #e0e0e0; padding: 8px 10px; height: {{ $matchH }}px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: center; gap: 8px; overflow: hidden; flex-shrink: 0; }
            .fifa-match-wrap .fifa-match:not(:first-child) { margin-top: {{ $matchGap }}px; }
            .fifa-match-row { display: flex; align-items: center; justify-content: space-between; gap: 6px; min-height: 22px; font-size: 0.8rem; font-weight: 600; flex-shrink: 0; }
            .fifa-match-row .team { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 0; }
            .fifa-match-row .score { font-weight: 700; color: #333; flex-shrink: 0; }
            .fifa-center { display: flex; flex-direction: column; align-items: center; width: 200px; flex-shrink: 0; padding: 0 12px; }
            .fifa-center .fifa-round-header { background: #1a472a; }
            .fifa-center-body { height: {{ $pairH * 4 }}px; position: relative; width: 100%; }
            .fifa-center-body::before { content: ''; position: absolute; left: -12px; top: 50%; width: calc(100% + 24px); height: 1px; margin-top: -0.5px; background: #9ca3af; z-index: 0; }
            .fifa-final-match { position: absolute; top: 50%; left: 0; right: 0; transform: translateY(-50%); min-height: 100px; padding: 18px 16px; width: 100%; box-sizing: border-box; border-radius: 10px; border: 3px solid #b8860b; box-shadow: 0 4px 12px rgba(0,0,0,0.2); background: #d4af37; z-index: 1; }
            .fifa-final-match .fifa-match-row { font-size: 1.05rem; min-height: 34px; font-weight: 700; }
            .fifa-third { display: none !important; }
        </style>

        <div class="fifa-bracket">
            <div class="fifa-bracket-inner">
                {{-- CÔTÉ GAUCHE --}}
                <div class="fifa-side">
                    @php
                        $roundsL = [
                            ['title' => 'Seizièmes', 'phase' => 'Seizièmes de finale', 'info' => $seiziemesInfo, 'matches' => $seiziemes->take(8), 'pairs' => 4],
                            ['title' => 'Huitièmes', 'phase' => 'Huitièmes de finale', 'info' => $huitiemesInfo, 'matches' => $huitiemes->take(4), 'pairs' => 2],
                            ['title' => 'Quarts', 'phase' => 'Quarts de finale', 'info' => $quartsInfo, 'matches' => $quarts->take(2), 'pairs' => 1],
                            ['title' => 'Demi-finales', 'phase' => 'Demi-finales', 'info' => $demiesInfo, 'matches' => $demies->take(1), 'pairs' => 0],
                        ];
                        // Connecteur gauche: nb segments, classe hauteur (double=304px, quad=608px), type SVG
                        $connectorL = [
                            ['n' => 4, 'class' => '', 'svg' => 'merge'],           // avant Huitièmes: 4 seg × 152
                            ['n' => 2, 'class' => 'fifa-connector-seg-double', 'svg' => 'merge'],  // avant Quarts: 2 seg × 304
                            ['n' => 1, 'class' => 'fifa-connector-seg-quad', 'svg' => 'merge50'],  // avant Demi: 1 seg × 608 (2 inputs 25%+75% → 50%)
                        ];
                    @endphp
                    @foreach($roundsL as $ri => $r)
                        @if($ri > 0)
                            @php $cfg = $connectorL[$ri - 1]; @endphp
                            <div class="fifa-connector">
                                <div class="fifa-connector-head"></div>
                                @for($k = 0; $k < $cfg['n']; $k++)
                                    <div class="fifa-connector-seg {{ $cfg['class'] }}">
                                        @if($cfg['svg'] === 'merge50')
                                            <svg viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="none" stroke="#9ca3af" stroke-width="0.8" d="M0 25 L50 25 L50 50 L100 50 M0 75 L50 75 L50 50"/></svg>
                                        @else
                                            <svg viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="none" stroke="#9ca3af" stroke-width="0.8" d="M0 25 L50 25 L50 50 L100 50 M0 75 L50 75 L50 50"/></svg>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        @endif
                        <div class="fifa-round">
                            <div class="fifa-round-header">
                                {{ $r['title'] }}
                                @if($r['info'] && $r['info']['hasScheduled'])
                                    <button type="button" class="btn btn-light btn-sm ms-1" style="font-size: 0.6rem; padding: 2px 6px;" wire:click="simulatePhase('{{ $r['phase'] }}')">Simuler</button>
                                @endif
                            </div>
                            <div class="fifa-round-body">
                                @php
                                    $slotOneMargin = (int)(($pairH - $matchH) / 2);
                                    $slotTwoMargin = (int)(($pairH * 2 - $matchH) / 2);
                                    $slotQuadMargin = (int)(($pairH * 4 - $matchH) / 2);
                                @endphp
                                @if($r['pairs'] === 0)
                                    <div class="fifa-pair-slot fifa-slot-quad">
                                        <div class="fifa-match-wrap" style="margin-top: {{ $slotQuadMargin }}px;">
                                            @if($r['matches']->isNotEmpty())
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $r['matches']->first(), 'flagClasses' => $flagClasses])
                                            @else
                                                <div class="fifa-match" style="color: #999; font-size: 0.8rem; text-align: center;">—</div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($r['pairs'] === 1)
                                    @foreach($r['matches'] as $m)
                                        <div class="fifa-pair-slot fifa-slot-two">
                                            <div class="fifa-match-wrap" style="margin-top: {{ $slotTwoMargin }}px;">
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $m, 'flagClasses' => $flagClasses])
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($r['pairs'] === 2)
                                    @foreach($r['matches'] as $m)
                                        <div class="fifa-pair-slot fifa-slot-one">
                                            <div class="fifa-match-wrap" style="margin-top: {{ $slotOneMargin }}px;">
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $m, 'flagClasses' => $flagClasses])
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($r['matches']->chunk(2) as $pair)
                                        <div class="fifa-pair">
                                            @foreach($pair as $m)
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $m, 'flagClasses' => $flagClasses])
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="fifa-connector">
                        <div class="fifa-connector-head"></div>
                        <div class="fifa-connector-seg fifa-connector-seg-quad">
                            <svg viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="none" stroke="#9ca3af" stroke-width="0.8" d="M0 50 L100 50"/></svg>
                        </div>
                    </div>
                </div>

                {{-- CENTRE --}}
                <div class="fifa-center">
                    <div class="fifa-round-header">
                        Finale
                        @if($finaleInfo && $finaleInfo['hasScheduled'])
                            <button type="button" class="btn btn-light btn-sm ms-1" style="font-size: 0.6rem; padding: 2px 6px;" wire:click="simulatePhase('Finale')">Simuler</button>
                        @endif
                    </div>
                    <div class="fifa-center-body">
                        @if($finale->isNotEmpty())
                            <div class="fifa-final-match">
                                @include('livewire.partials.bracket-match-fifa-final', ['match' => $finale->first(), 'flagClasses' => $flagClasses])
                            </div>
                        @else
                            <div class="fifa-final-match" style="text-align: center; color: #5a4a2a;">Finale à venir</div>
                        @endif
                    </div>
                </div>

                {{-- CÔTÉ DROIT --}}
                <div class="fifa-side">
                    <div class="fifa-connector">
                        <div class="fifa-connector-head"></div>
                        <div class="fifa-connector-seg fifa-connector-seg-quad">
                            <svg viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="none" stroke="#9ca3af" stroke-width="0.8" d="M0 50 L100 50"/></svg>
                        </div>
                    </div>
                    @php
                        $roundsR = [
                            ['title' => 'Demi-finales', 'phase' => 'Demi-finales', 'info' => $demiesInfo, 'matches' => $demies->slice(1, 1), 'pairs' => 0],
                            ['title' => 'Quarts', 'phase' => 'Quarts de finale', 'info' => $quartsInfo, 'matches' => $quarts->slice(2, 2), 'pairs' => 1],
                            ['title' => 'Huitièmes', 'phase' => 'Huitièmes de finale', 'info' => $huitiemesInfo, 'matches' => $huitiemes->slice(4, 4), 'pairs' => 2],
                            ['title' => 'Seizièmes', 'phase' => 'Seizièmes de finale', 'info' => $seiziemesInfo, 'matches' => $seiziemes->slice(8, 8), 'pairs' => 4],
                        ];
                        // Connecteur droit: après chaque round (sauf dernier). quad = 608px (1 seg), double = 304px
                        $connectorR = [
                            ['n' => 1, 'class' => 'fifa-connector-seg-quad', 'svg' => 'split50'],  // Demi→Quarts: 1 seg 608px, input 50% → out 25% et 75%
                            ['n' => 2, 'class' => 'fifa-connector-seg-double', 'svg' => 'split'],  // Quarts→Huitièmes: 2 seg 304px
                            ['n' => 4, 'class' => '', 'svg' => 'split'],  // Huitièmes→Seizièmes: 4 seg 152px
                        ];
                    @endphp
                    @foreach($roundsR as $ri => $r)
                        <div class="fifa-round">
                            <div class="fifa-round-header">{{ $r['title'] }}
                                @if($r['info'] && $r['info']['hasScheduled'])
                                    <button type="button" class="btn btn-light btn-sm ms-1" style="font-size: 0.6rem; padding: 2px 6px;" wire:click="simulatePhase('{{ $r['phase'] }}')">Simuler</button>
                                @endif
                            </div>
                            <div class="fifa-round-body">
                                @php
                                    $slotOneMarginR = (int)(($pairH - $matchH) / 2);
                                    $slotTwoMarginR = (int)(($pairH * 2 - $matchH) / 2);
                                    $slotQuadMarginR = (int)(($pairH * 4 - $matchH) / 2);
                                @endphp
                                @if($r['pairs'] === 0)
                                    <div class="fifa-pair-slot fifa-slot-quad">
                                        <div class="fifa-match-wrap" style="margin-top: {{ $slotQuadMarginR }}px;">
                                            @if($r['matches']->isNotEmpty())
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $r['matches']->first(), 'flagClasses' => $flagClasses])
                                            @else
                                                <div class="fifa-match" style="color: #999;">—</div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($r['pairs'] === 1)
                                    @foreach($r['matches'] as $m)
                                        <div class="fifa-pair-slot fifa-slot-two">
                                            <div class="fifa-match-wrap" style="margin-top: {{ $slotTwoMarginR }}px;">
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $m, 'flagClasses' => $flagClasses])
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($r['pairs'] === 2)
                                    @foreach($r['matches'] as $m)
                                        <div class="fifa-pair-slot fifa-slot-one">
                                            <div class="fifa-match-wrap" style="margin-top: {{ $slotOneMarginR }}px;">
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $m, 'flagClasses' => $flagClasses])
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($r['matches']->chunk(2) as $pair)
                                        <div class="fifa-pair">
                                            @foreach($pair as $m)
                                                @include('livewire.partials.bracket-match-fifa', ['match' => $m, 'flagClasses' => $flagClasses])
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @if($ri < count($roundsR) - 1)
                            @php $cfgR = $connectorR[$ri]; @endphp
                            <div class="fifa-connector">
                                <div class="fifa-connector-head"></div>
                                @for($k = 0; $k < $cfgR['n']; $k++)
                                    <div class="fifa-connector-seg {{ $cfgR['class'] }}">
                                        @if($cfgR['svg'] === 'split50')
                                            <svg viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="none" stroke="#9ca3af" stroke-width="0.8" d="M0 50 L50 50 L50 25 L100 25 M50 50 L50 75 L100 75"/></svg>
                                        @else
                                            <svg viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="none" stroke="#9ca3af" stroke-width="0.8" d="M0 50 L50 50 L50 25 L100 25 M50 50 L50 75 L100 75"/></svg>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
