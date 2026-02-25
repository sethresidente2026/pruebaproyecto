<?php

namespace App\Http\Requests\Actividad;

use Illuminate\Foundation\Http\FormRequest;

class StoreActividadRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100|unique:actividades,nombre',
        ];
    }

    public function messages()
    {
        return [
            'nombre.unique'   => 'Esta actividad ya está registrada.',
            'nombre.required' => 'El nombre es obligatorio.'
        ];
    }
}