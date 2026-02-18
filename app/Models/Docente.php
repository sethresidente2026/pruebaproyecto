<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Docente extends Model
{
    use HasFactory;
    protected $fillable = [
'nombre', 'apellidos', 'email', 'estatus'];

public function grupos(): HasMany
{
    return $this->hasMany(Grupo::class);
}

}
