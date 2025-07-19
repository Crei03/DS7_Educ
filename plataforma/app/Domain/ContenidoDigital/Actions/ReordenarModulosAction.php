<?php

namespace App\Domain\ContenidoDigital\Actions;

use App\Domain\ContenidoDigital\Models\Modulo;

class ReordenarModulosAction
{
    public function execute(int $cursoId, array $ordenModulos): void
    {
        foreach ($ordenModulos as $orden => $moduloId) {
            Modulo::where('id', $moduloId)
                ->where('curso_id', $cursoId)
                ->update(['orden' => $orden + 1]);
        }
    }
}
