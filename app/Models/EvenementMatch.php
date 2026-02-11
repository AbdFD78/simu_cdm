<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvenementMatch extends Model
{
    use HasFactory;

    protected $table = 'evenement_matchs';

    protected $fillable = [
        'match_id',
        'minute',
        'type',
        'description',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }
}

