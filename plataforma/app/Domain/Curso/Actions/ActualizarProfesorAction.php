<?php

namespace App\Domain\Curso\Actions;

use App\Domain\Curso\Models\Profesor;

class ActualizarProfesorAction
{
    public function execute(Profesor $profesor, array $datos): Profesor
    {
        $datosActualizacion = [
            'nombrePrimario' => $datos['nombrePrimario'],
            'nombreSecundario' => $datos['nombreSecundario'] ?? null,
            'apellidoPrimario' => $datos['apellidoPrimario'],
            'apellidoSecundario' => $datos['apellidoSecundario'] ?? null,
            'cedula' => $datos['cedula'],
            'correo' => $datos['correo'],
        ];

        // Solo actualizar contraseña si se proporciona
        if (isset($datos['contrasena']) && !empty($datos['contrasena'])) {
            $datosActualizacion['contrasena'] = bcrypt($datos['contrasena']);
        }

        $profesor->update($datosActualizacion);

        return $profesor->fresh();
    }
}
