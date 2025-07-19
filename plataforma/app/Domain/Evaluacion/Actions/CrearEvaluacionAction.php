<?php

namespace App\Domain\Evaluacion\Actions;

use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Pregunta;
use App\Domain\Evaluacion\Models\Opcion;
use App\Domain\Curso\Models\Curso;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrearEvaluacionAction
{
    public function execute(array $datos): Evaluacion
    {
        // Validar que el curso existe
        $curso = Curso::findOrFail($datos['curso_id']);

        // Crear la evaluación
        $evaluacion = Evaluacion::create([
            'curso_id' => $curso->id,
            'titulo' => $datos['titulo'],
            'duracion_min' => $datos['duracion_min'] ?? 60,
        ]);

        // Si se proporcionan preguntas, crearlas
        if (isset($datos['preguntas']) && is_array($datos['preguntas'])) {
            foreach ($datos['preguntas'] as $preguntaData) {
                $pregunta = Pregunta::create([
                    'evaluacion_id' => $evaluacion->id,
                    'enunciado' => $preguntaData['enunciado'],
                ]);

                // Crear opciones si se proporcionan
                if (isset($preguntaData['opciones']) && is_array($preguntaData['opciones'])) {
                    foreach ($preguntaData['opciones'] as $opcionData) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'texto' => $opcionData['texto'],
                            'es_correcta' => $opcionData['es_correcta'] ?? false,
                        ]);
                    }
                }
            }
        }

        return $evaluacion->load(['preguntas.opciones']);
    }
}
