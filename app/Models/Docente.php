<?php

namespace App\Models;
use App\Observers\DocenteObserver; 
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
#[ObservedBy([DocenteObserver::class])]
class Docente extends Model
{
    use HasFactory;
    
protected $guarded = [];

public function grupos(): HasMany
{
    return $this->hasMany(Grupo::class,'docente_id');
}

}
