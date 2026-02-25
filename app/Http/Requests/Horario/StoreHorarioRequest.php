<?php

namespace App\Http\Requests\Horario;

use Illuminate\Foundation\Http\FormRequest;

class StoreHorarioRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'grupo_id'   => 'required|exists:grupos,id',
            'espacio_id' => 'required|exists:espacios,id',
            'dia_semana' => 'required|string',
            'hora_inicio'=> 'required|date_format:H:i',
            'hora_fin'   => 'required|date_format:H:i|after:hora_inicio',
        ];
    }
}