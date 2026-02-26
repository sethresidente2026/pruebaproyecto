<?php

namespace App\Http\Requests\Horario;

use Illuminate\Foundation\Http\FormRequest;

class StoreHorarioRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
        'grupo_id'    => 'required|exists:grupos,id',
        'espacio_id'  => 'required|exists:espacios,id',
        'dias'        => 'required|array|min:1', 
        'dias.*'      => 'string',              
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin'    => 'required|date_format:H:i|after:hora_inicio',
    ];
    }
    public function messages()
    {
        return [
            'grupo_id.required'    => 'Debes seleccionar un grupo para asignar el horario.',
            'grupo_id.exists'      => 'El grupo seleccionado no es válido.',
            'espacio_id.required'  => 'Es necesario asignar un espacio o salón.',
            'espacio_id.exists'    => 'El espacio seleccionado no existe en el catálogo.',
            'dia_semana.required'  => 'Debes indicar el día de la semana.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'El formato de hora de inicio debe ser HH:mm (ej. 08:00).',
            'hora_fin.required'    => 'La hora de finalización es obligatoria.',
            'hora_fin.date_format' => 'El formato de hora de fin debe ser HH:mm (ej. 10:00).',
            'hora_fin.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}