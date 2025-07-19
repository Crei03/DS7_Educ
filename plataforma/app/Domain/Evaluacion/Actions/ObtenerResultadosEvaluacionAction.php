<?php

namespace App\Domain\Evaluacion\Actions;

use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Resultado;
use App\Domain\Estudiante\Models\Estudiante;

class ObtenerResultadosEvaluacionAction
{
    public function execute(int $evaluacionId): array
    {
        $evaluacion = Evaluacion::with(['preguntas', 'resultados.estudiante'])
            ->findOrFail($evaluacionId);

        $resultados = $evaluacion->resultados->map(function ($resultado) {
            return [
                'estudiante' => [
                    'id' => $resultado->estudiante->id,
                    'nombre' => $resultado->estudiante->fullName(),
                    'correo' => $resultado->estudiante->correo,
                ],
                'puntaje' => $resultado->puntaje,
                'porcentaje' => $resultado->porcentaje ?? 0,
                'fecha' => $resultado->fecha ?? $resultado->completado_en,
            ];
        });

        $estadisticas = $this->calcularEstadisticas($evaluacion->resultados);

        return [
            'evaluacion' => [
                'id' => $evaluacion->id,
                'titulo' => $evaluacion->titulo,
                'total_preguntas' => $evaluacion->preguntas->count(),
            ],
            'resultados' => $resultados,
            'estadisticas' => $estadisticas,
        ];
    }

    private function calcularEstadisticas($resultados): array
    {
        if ($resultados->isEmpty()) {
            return [
                'total_participantes' => 0,
                'promedio' => 0,
                'puntaje_maximo' => 0,
                'puntaje_minimo' => 0,
            ];
        }

        $puntajes = $resultados->pluck('puntaje');

        return [
            'total_participantes' => $resultados->count(),
            'promedio' => round($puntajes->average(), 2),
            'puntaje_maximo' => $puntajes->max(),
            'puntaje_minimo' => $puntajes->min(),
        ];
    }
}
