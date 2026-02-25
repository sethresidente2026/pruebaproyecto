<?php

namespace App\Http\Requests\Actividad;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActividadRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
    
        $actividad = $this->route('actividad'); 

        return [
            'nombre' => 'required|string|max:100|unique:actividades,nombre,' . $actividad->id,
        ];
    }

    public function messages()
    {
        return [
            'nombre.unique'   => 'Ya existe otra actividad con este nombre.',
            'nombre.required' => 'El nombre no puede estar vacío.'
        ];
    }
}