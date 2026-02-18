<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Horario extends Model
{
    use HasFactory;
    protected $fillable = [
        'grupo_id',
        'espacio_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin'];
        public function grupo(): BelongsTo
        {
            return $this->belongsTo(Grupo::class);
        }
        public function espacio(): BelongsTo
        {
            return $this->belongsTo(Espacio::class);
        }
    
}

