<?php

namespace App\Http\Requests\Docente;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocenteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'=> 'required|string|max:50',
            'apellidos'=> 'required|string|max:50',
            'email'=> 'required|email|unique:docentes,email',
            'estatus' => 'in:Activo,Inactivo,Baja Temporal'
        ];
    }
    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre del docente es obligatorio.',
            'nombre.max'         => 'El nombre no puede exceder los 50 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max'      => 'Los apellidos no pueden exceder los 50 caracteres.',
            'email.required'     => 'El correo electrónico es indispensable.',
            'email.email'        => 'Por favor, ingresa una dirección de correo válida.',
            'email.unique'       => 'Este correo ya está registrado por otro docente.',
            'estatus.in'         => 'El estatus seleccionado no es válido.'
        ];
    }
}
