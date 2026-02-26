<?php

namespace App\Http\Requests\Espacio;

use Illuminate\Foundation\Http\FormRequest;

class StoreEspacioRequest extends FormRequest
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
         
           'nombre' => 'required|string|max:100|unique:espacios,nombre',
            'capacidad' => 'required|integer|min:1'
        ];

    }
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del espacio es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'capacidad.required' => 'Debes indicar la capacidad del espacio.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser de al menos 1 persona.',
            'nombre.unique' => 'Ya existe un espacio registrado con este nombre.'
        ];
    }
}
