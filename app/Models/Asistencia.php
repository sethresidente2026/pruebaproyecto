<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Asistencia extends Model
{
   use HasFactory;

    protected $fillable = [
       'fecha',
        'estado',
        'observaciones',
        'grupo_id',
        'docente_id', // En la base de datos se llama docente_id
        'docente_sustituto_id',
    ];
    public function grupo()
    {
        return $this->belongsTo(Grupo::class,'grupo_id');
    }


    public function docente_titular()
    {
        return $this->belongsTo(Docente::class,'docente_id');
    }
    public function docente_Sustituto()
    {
        return $this->belongsTo(Docente::class, 'docente_sustituto_id');
    }
}
