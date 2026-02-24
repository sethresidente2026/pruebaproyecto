<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocenteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
           
            'nombre'       => 'required|string|max:100',
            'cupo_maximo'  => 'required|integer|min:1',
            'es_mixto'     => 'boolean', 
            'docente_id'   => 'required|exists:docentes,id',
            'actividad_id' => 'required|exists:actividades,id',
            'nivel_id'     => 'required|exists:nivels,id' 
        ];
    }
}
