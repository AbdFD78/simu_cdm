<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * On utilise MatchModel comme nom de classe pour éviter les collisions
 * éventuelles avec des mots-clés ou helpers.
 */
class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matchs';

    protected $fillable = [
        'phase_id',
        'poule_id',
        'equipe_a_id',
        'equipe_b_id',
        'date_heure',
        'statut',
        'score_equipe_a',
        'score_equipe_b',
        'stade',
        'ville',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
    ];

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function poule()
    {
        return $this->belongsTo(Poule::class);
    }

    public function equipeA()
    {
        return $this->belongsTo(Equipe::class, 'equipe_a_id');
    }

    public function equipeB()
    {
        return $this->belongsTo(Equipe::class, 'equipe_b_id');
    }

    public function evenements()
    {
        return $this->hasMany(EvenementMatch::class, 'match_id');
    }
}

