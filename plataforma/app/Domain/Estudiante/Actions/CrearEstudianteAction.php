<?php

namespace App\Domain\Estudiante\Actions;

use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CrearEstudianteAction
{
    public function execute(array $datos): Estudiante
    {
        // Validar que la cédula tenga el formato panameño
        if (!preg_match('/^(1[0-3]|[1-9])-\d{1,6}-\d{1,7}$/', $datos['cedula'])) {
            throw ValidationException::withMessages([
                'cedula' => ['La cédula debe tener el formato panameño: X-XXXX-XXXXX']
            ]);
        }

        // Validar que el correo tenga dominio .pa
        if (!str_ends_with($datos['correo'], '.pa')) {
            throw ValidationException::withMessages([
                'correo' => ['El correo debe tener dominio panameño (.pa)']
            ]);
        }

        // Hashear la contraseña
        $datos['contrasena'] = Hash::make($datos['contrasena']);

        return Estudiante::create($datos);
    }
}
