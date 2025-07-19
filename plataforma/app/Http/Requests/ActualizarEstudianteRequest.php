<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarEstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $estudianteId = $this->route('estudiante')->id ?? null;

        return [
            'nombrePrimario' => [
                'sometimes',
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'nombreSecundario' => [
                'nullable',
                'string',
                'max:60',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'apellidoPrimario' => [
                'sometimes',
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'apellidoSecundario' => [
                'nullable',
                'string',
                'max:60',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'correo' => [
                'sometimes',
                'required',
                'email',
                'max:120',
                'unique:estudiantes,correo,' . $estudianteId,
                'regex:/^.+\.pa$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombrePrimario.regex' => 'El nombre primario solo puede contener letras y espacios.',
            'nombreSecundario.regex' => 'El nombre secundario solo puede contener letras y espacios.',
            'apellidoPrimario.regex' => 'El apellido primario solo puede contener letras y espacios.',
            'apellidoSecundario.regex' => 'El apellido secundario solo puede contener letras y espacios.',
            'correo.regex' => 'El correo debe tener dominio panameño (.pa).',
        ];
    }
}
