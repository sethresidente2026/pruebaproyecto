<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Grupo extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre', 
        'cupo_maximo', 
        'actividad_id', 
        'docente_id', 
        'ciclo_id', 
        'nivel_id'];
    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }
    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }
    public function ciclo():BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class,'ciclo_id');

    }
    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }
    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }
}
