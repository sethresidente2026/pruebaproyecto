<?php

namespace App\Http\Requests\Asistencia;

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
            
            'docente_sustituto_id' => 'nullable|exists:docentes,id|different:docente_titular_id',
            'observaciones'        => 'nullable|string|max:255'
        ];
    }

    
    public function messages()
    {
        return [
            'docente_sustituto_id.required_if' => 'Debes seleccionar qué maestro cubrió la clase.',
            'docente_sustituto_id.different' => 'Un docente no puede sustituirse a sí mismo. Por favor, selecciona a un docente distinto.',
        ];
    }
}