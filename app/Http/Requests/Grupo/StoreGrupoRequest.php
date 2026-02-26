<?php

namespace App\Http\Requests\Grupo;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrupoRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            
            'nombre'        => 'required|string|max:50',
            'cupo_maximo'   => 'required|integer|min:1|max:100',
            'docente_id'    => 'required|exists:docentes,id',
            'actividad_id'  => 'required|exists:actividades,id',
            
            
            'ciclo_id'      => 'required|exists:ciclos_escolares,id', 
            
    
            'nivel_id'      => 'required|exists:niveles,id',          
            
            'estatus'       => 'nullable|in:Activo,Inactivo'
        ];
    }
    public function messages()
    {
        return [
            'nombre.required'      => 'El nombre del grupo es obligatorio.',
            'cupo_maximo.min'      => 'El cupo mínimo debe ser de al menos 1 persona.',
            'docente_id.exists'    => 'El docente seleccionado no es válido.',
            'actividad_id.exists'  => 'La actividad seleccionada no existe.',
            
            
        ];
    }
}