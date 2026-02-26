<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
   
    protected $guarded = [];

   
    public function docente() { 
        return $this->belongsTo(Docente::class, 'docente_id'); 
    }
    
    public function actividad() { 
        return $this->belongsTo(Actividad::class, 'actividad_id'); 
    }
    
    public function ciclo() { 
        return $this->belongsTo(CicloEscolar::class, 'ciclo_id'); 
    }
    
  public function nivelEducativo() 
{
    return $this->belongsTo(Nivel::class, 'nivel_id'); 
}
}