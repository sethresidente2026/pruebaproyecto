<?php

namespace App\Http\Requests\Grupo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrupoRequest extends FormRequest
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
            'nombre' => 'required|string|max:50|unique:grupos,nombre,' . $this->route('grupo')->id,
            'cupo_maximo'  => 'required|integer|min:1',
            'es_mixto'     => 'boolean',
            'docente_id'   => 'required|exists:docentes,id',
            'actividad_id' => 'required|exists:actividades,id',
            
            
        ];
    }
}
