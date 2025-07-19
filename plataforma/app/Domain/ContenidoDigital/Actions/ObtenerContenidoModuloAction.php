<?php

namespace App\Domain\ContenidoDigital\Actions;

use App\Domain\ContenidoDigital\Models\Modulo;

class ObtenerContenidoModuloAction
{
    public function execute(int $moduloId): array
    {
        $modulo = Modulo::with(['materiales', 'curso'])
            ->findOrFail($moduloId);

        return [
            'modulo' => $modulo,
            'materiales_por_tipo' => $this->agruparMaterialesPorTipo($modulo->materiales),
            'estadisticas' => [
                'total_materiales' => $modulo->materiales->count(),
                'tipos_disponibles' => $modulo->materiales->pluck('tipo')->unique()->values()->toArray(),
            ]
        ];
    }

    private function agruparMaterialesPorTipo($materiales): array
    {
        return $materiales->groupBy('tipo')->map(function ($items, $tipo) {
            return [
                'tipo' => $tipo,
                'cantidad' => $items->count(),
                'materiales' => $items->values()->toArray()
            ];
        })->toArray();
    }
}
