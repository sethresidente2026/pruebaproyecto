<?php

namespace App\Http\Requests\Docente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateDocenteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {$docenteId = $this->route('docente');
        return [
           
            'nombre'    => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
            
            'email'     => ['required', 'email', Rule::unique('docentes', 'email')->ignore($docenteId)],
            'estatus'   => 'in:Activo,Inactivo,Baja Temporal'
        ];
    }
}
