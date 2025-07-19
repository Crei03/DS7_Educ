<?php

namespace App\Domain\ContenidoDigital\Actions;

use App\Domain\ContenidoDigital\Models\Modulo;

class CrearModuloAction
{
    public function execute(array $datos): Modulo
    {
        return Modulo::create([
            'curso_id' => $datos['curso_id'],
            'titulo' => $datos['titulo'],
            'orden' => $datos['orden'] ?? $this->obtenerSiguienteOrden($datos['curso_id']),
        ]);
    }

    private function obtenerSiguienteOrden(int $cursoId): int
    {
        return Modulo::where('curso_id', $cursoId)->max('orden') + 1;
    }
}
