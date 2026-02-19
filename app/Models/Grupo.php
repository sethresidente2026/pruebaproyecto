<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    // Permitimos que se guarden todos los datos masivamente
    protected $guarded = [];

    // Relaciones explícitas con las llaves foráneas correctas
    public function docente() { 
        return $this->belongsTo(Docente::class, 'docente_id'); 
    }
    
    public function actividad() { 
        return $this->belongsTo(Actividad::class, 'actividad_id'); 
    }
    
    public function ciclo() { 
        return $this->belongsTo(CicloEscolar::class, 'ciclo_id'); 
    }
    
    public function nivel() { 
        return $this->belongsTo(Nivel::class, 'nivel_id'); 
    }
}