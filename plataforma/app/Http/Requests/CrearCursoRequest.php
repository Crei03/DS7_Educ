<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearCursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => [
                'required',
                'string',
                'max:255',
                'unique:cursos'
            ],
            'descripcion' => [
                'required',
                'string',
                'min:50',
                'max:1000'
            ],
            'profesor_id' => [
                'required',
                'exists:profesores,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.unique' => 'Ya existe un curso con este título.',
            'descripcion.min' => 'La descripción debe tener al menos 50 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'profesor_id.exists' => 'El profesor seleccionado no existe.',
        ];
    }
}
