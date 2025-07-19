<?php

namespace App\Domain\Curso\Actions;

use App\Domain\Curso\Models\Profesor;

class CrearProfesorAction
{
    public function execute(array $datos): Profesor
    {
        return Profesor::create([
            'nombrePrimario' => $datos['nombrePrimario'],
            'nombreSecundario' => $datos['nombreSecundario'] ?? null,
            'apellidoPrimario' => $datos['apellidoPrimario'],
            'apellidoSecundario' => $datos['apellidoSecundario'] ?? null,
            'cedula' => $datos['cedula'],
            'correo' => $datos['correo'],
            'contrasena' => bcrypt($datos['contrasena']),
        ]);
    }
}
