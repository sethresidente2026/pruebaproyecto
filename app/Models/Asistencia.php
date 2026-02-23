<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Asistencia extends Model
{
   use HasFactory;

    protected $fillable = [
        'grupo_id',
        'docente_id',
        'docente_sustituto_id',
        'fecha',
        'estado',
        'observaciones'
    ];
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }
    public function docentetitular()
    {
        return $this->belongsTo(Docente::class,'docente_id');
    }
    public function docenteSustituto()
    {
        return $this->belongsTo(Docente::class, 'docente_sustituto_id');
    }
}
