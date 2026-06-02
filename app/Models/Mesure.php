<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modèle pour la table mesures
class Mesure extends Model
{
    protected $table = 'mesures';

    protected $fillable = [
        'temperature',
        'humidite',
        'gaz',
        'pir_detecte',
        'salle_id',
        'equipement_id',
    ];
}
