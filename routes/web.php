<?php

use App\Livewire\KnockoutBracket;
use App\Livewire\MatchShow;
use App\Livewire\PhaseList;
use App\Livewire\PhaseShow;
use App\Livewire\PouleShow;
use Illuminate\Support\Facades\Route;

// Page d'accueil : redirection vers la liste des phases
Route::redirect('/', '/phases');

// Phases
Route::get('/phases', PhaseList::class)->name('phases.index');
Route::get('/phases/{phase}', PhaseShow::class)->name('phases.show');

// Tableau des phases finales
Route::get('/tableau-final', KnockoutBracket::class)->name('knockout.bracket');

// Poules
Route::get('/poules/{poule}', PouleShow::class)->name('poules.show');

// Matchs
Route::get('/matchs/{match}', MatchShow::class)->name('matchs.show');

