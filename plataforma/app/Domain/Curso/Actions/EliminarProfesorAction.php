<?php

namespace App\Domain\Curso\Actions;

use App\Domain\Curso\Models\Profesor;

class EliminarProfesorAction
{
    public function execute(Profesor $profesor): bool
    {
        // Verificar si el profesor tiene cursos asignados
        if ($profesor->cursos()->exists()) {
            throw new \Exception('No se puede eliminar el profesor porque tiene cursos asignados');
        }

        return $profesor->delete();
    }
}
