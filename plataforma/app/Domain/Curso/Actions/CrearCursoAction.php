<?php

namespace App\Domain\Curso\Actions;

use App\Domain\Curso\Models\Curso;

class CrearCursoAction
{
    public function execute(array $datos): Curso
    {
        // Validar que el profesor existe
        $profesor = \App\Domain\Curso\Models\Profesor::findOrFail($datos['profesor_id']);

        return Curso::create([
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'profesor_id' => $profesor->id,
        ]);
    }
}
