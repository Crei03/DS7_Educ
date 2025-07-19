<?php

namespace App\Domain\Curso\Actions;

use App\Domain\Curso\Models\Profesor;
use Illuminate\Pagination\LengthAwarePaginator;

class ObtenerProfesoresAction
{
    public function execute(array $filtros = []): LengthAwarePaginator
    {
        $query = Profesor::query();

        // Filtro por nombre completo
        if (isset($filtros['buscar']) && !empty($filtros['buscar'])) {
            $termino = $filtros['buscar'];
            $query->where(function ($q) use ($termino) {
                $q->where('nombrePrimario', 'LIKE', "%{$termino}%")
                    ->orWhere('nombreSecundario', 'LIKE', "%{$termino}%")
                    ->orWhere('apellidoPrimario', 'LIKE', "%{$termino}%")
                    ->orWhere('apellidoSecundario', 'LIKE', "%{$termino}%")
                    ->orWhere('correo', 'LIKE', "%{$termino}%")
                    ->orWhere('cedula', 'LIKE', "%{$termino}%");
            });
        }

        // Incluir información de cursos
        $query->withCount('cursos');

        return $query->orderBy('apellidoPrimario')
            ->orderBy('nombrePrimario')
            ->paginate(15);
    }
}
