<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classement extends Model
{
    use HasFactory;

    protected $fillable = [
        'poule_id',
        'equipe_id',
        'points',
        'matchs_joues',
        'victoires',
        'nuls',
        'defaites',
        'buts_marques',
        'buts_encaissees',
    ];

    public function poule()
    {
        return $this->belongsTo(Poule::class);
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }
}

