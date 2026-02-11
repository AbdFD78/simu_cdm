<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code_pays',
        'poule_id',
    ];

    public function poule()
    {
        return $this->belongsTo(Poule::class);
    }

    public function matchsCommeEquipeA()
    {
        return $this->hasMany(MatchModel::class, 'equipe_a_id');
    }

    public function matchsCommeEquipeB()
    {
        return $this->hasMany(MatchModel::class, 'equipe_b_id');
    }

    public function classements()
    {
        return $this->hasMany(Classement::class);
    }
}

