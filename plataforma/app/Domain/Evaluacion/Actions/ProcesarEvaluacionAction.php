<?php

namespace App\Domain\Evaluacion\Actions;

use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Resultado;
use App\Domain\Estudiante\Models\Estudiante;

class ProcesarEvaluacionAction
{
    public function execute(int $estudianteId, int $evaluacionId, array $respuestas): Resultado
    {
        $estudiante = Estudiante::findOrFail($estudianteId);
        $evaluacion = Evaluacion::with('preguntas.opciones')->findOrFail($evaluacionId);

        $puntajeTotal = 0;
        $respuestasCorrectas = 0;
        $totalPreguntas = $evaluacion->preguntas->count();

        foreach ($respuestas as $preguntaId => $opcionId) {
            $pregunta = $evaluacion->preguntas->where('id', $preguntaId)->first();
            if ($pregunta) {
                $opcionCorrecta = $pregunta->opciones->where('es_correcta', true)->first();
                if ($opcionCorrecta && $opcionCorrecta->id == $opcionId) {
                    $respuestasCorrectas++;
                    $puntajeTotal += $pregunta->puntos;
                }
            }
        }

        $porcentaje = $totalPreguntas > 0 ? ($respuestasCorrectas / $totalPreguntas) * 100 : 0;

        return Resultado::create([
            'estudiante_id' => $estudianteId,
            'evaluacion_id' => $evaluacionId,
            'puntaje' => $puntajeTotal,
            'porcentaje' => $porcentaje,
            'respuestas' => json_encode($respuestas),
            'completado_en' => now(),
        ]);
    }
}
