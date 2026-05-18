<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesure extends Model
{
    protected $table = 'mesures';

    protected $fillable = [

        'temperature',
        'humidite',
        'gaz',
        'courant',
        'puissance'

    ];
}