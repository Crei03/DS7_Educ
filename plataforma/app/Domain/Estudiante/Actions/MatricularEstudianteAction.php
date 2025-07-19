<?php

namespace App\Domain\Estudiante\Actions;

use App\Domain\Curso\Models\Curso;
use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MatricularEstudianteAction
{
    public function execute(int $estudianteId, int $cursoId): void
    {
        $estudiante = Estudiante::findOrFail($estudianteId);
        $curso = Curso::findOrFail($cursoId);

        // Verificar si ya está matriculado
        if ($estudiante->cursos()->where('curso_id', $cursoId)->exists()) {
            throw new \Exception('El estudiante ya está matriculado en este curso');
        }

        // Matricular con progreso inicial en 0
        $estudiante->cursos()->attach($cursoId, [
            'fecha' => now(),
            'progreso' => 0
        ]);
    }
}
