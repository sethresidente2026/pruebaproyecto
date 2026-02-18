<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    protected $fillable = [
        'nombre',
        'capacidad'
    ];
}
