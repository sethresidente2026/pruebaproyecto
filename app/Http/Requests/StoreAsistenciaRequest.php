<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsistenciaRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'fecha'                => 'required|date',
            'grupo_id'             => 'required|exists:grupos,id',
            'docente_id'           => 'required|exists:docentes,id',
            'estado'               => 'required|in:Asistió,Falta,Retardo,Sustitución',
            // El sustituto solo es obligatorio si el estado es 'Sustitución'
            'docente_sustituto_id' => 'required_if:estado,Sustitución|nullable|exists:docentes,id',
            'observaciones'        => 'nullable|string|max:255'
        ];
    }

    // Opcional: Mensajes en español si el usuario se equivoca
    public function messages()
    {
        return [
            'docente_sustituto_id.required_if' => 'Debes seleccionar qué maestro cubrió la clase.'
        ];
    }
}