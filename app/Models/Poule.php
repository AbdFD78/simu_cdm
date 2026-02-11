<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poule extends Model
{
    use HasFactory;

    protected $fillable = [
        'phase_id',
        'nom',
    ];

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function matchs()
    {
        return $this->hasMany(MatchModel::class);
    }

    public function classements()
    {
        return $this->hasMany(Classement::class);
    }
}

