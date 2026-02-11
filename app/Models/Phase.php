<?php

namespace App\Models;

use Database\Factories\PhaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PhaseFactory::new();
    }

    protected $fillable = [
        'nom',
        'ordre',
        'type_phase',
    ];

    /**
     * Les poules associées à cette phase (principalement pour la phase de groupes).
     */
    public function poules()
    {
        return $this->hasMany(Poule::class);
    }

    /**
     * Les matchs appartenant à cette phase.
     */
    public function matchs()
    {
        return $this->hasMany(MatchModel::class, 'phase_id');
    }
}

