<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrearEstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombrePrimario' => [
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/' // Solo letras y espacios
            ],
            'nombreSecundario' => [
                'nullable',
                'string',
                'max:60',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'apellidoPrimario' => [
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
            'cedula' => [
                'required',
                'string',
                'max:20',
                'unique:estudiantes',
                'regex:/^(1[0-3]|[1-9])-\d{1,6}-\d{1,7}$/' // Formato cédula panameña con rango
            ],
            'correo' => [
                'required',
                'email',
                'max:120',
                'unique:estudiantes',
                'regex:/^.+\.pa$/' // Debe terminar en .pa
            ],
            'contrasena' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/' // Contraseña fuerte
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
            'cedula.regex' => 'La cédula debe tener el formato panameño: X-XXXX-XXXXX.',
            'correo.regex' => 'El correo debe tener dominio panameño (.pa).',
            'contrasena.regex' => 'La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial.',
        ];
    }
}
