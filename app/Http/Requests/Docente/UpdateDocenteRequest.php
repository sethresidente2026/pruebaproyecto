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
    public function messages()
    {
        return [
            'nombre.required'    => 'Debes proporcionar un nombre.',
            'nombre.max'         => 'El nombre es demasiado largo (máximo 50 caracteres).',
            'apellidos.required' => 'Los apellidos son campos requeridos.',
            'apellidos.max'      => 'Los apellidos no deben superar los 50 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio para el contacto.',
            'email.email'        => 'El formato del correo electrónico es incorrecto.',
            'email.unique'       => 'Este correo ya pertenece a otro docente en el sistema.',
            'estatus.in'         => 'Selecciona un estatus válido (Activo, Inactivo o Baja Temporal).'

        ];
    }
}
