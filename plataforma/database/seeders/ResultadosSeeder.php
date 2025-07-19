<?php

namespace Database\Seeders;

use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Resultado;
use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ResultadosSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_PA');
        $evaluaciones = Evaluacion::with('preguntas')->get();
        $estudiantes = Estudiante::all();

        foreach ($evaluaciones as $evaluacion) {
            // El 60-80% de los estudiantes resuelven cada evaluación
            $participantes = $estudiantes->random($faker->numberBetween(
                (int) ($estudiantes->count() * 0.6),
                (int) ($estudiantes->count() * 0.8)
            ));

            foreach ($participantes as $estudiante) {
                // Simular puntaje basado en distribución realista
                $totalPreguntas = $evaluacion->preguntas->count();

                if ($totalPreguntas > 0) {
                    // Generar respuestas correctas con distribución normal
                    $respuestasCorrectas = $this->generarRespuestasConDistribucionNormal($totalPreguntas);
                    $puntaje = ($respuestasCorrectas / $totalPreguntas) * 100;
                    $porcentaje = round($puntaje, 1);

                    // Simular respuestas JSON
                    $respuestas = [];
                    foreach ($evaluacion->preguntas as $index => $pregunta) {
                        $opciones = $pregunta->opciones;
                        if ($opciones->count() > 0) {
                            // Decidir si responde correcta o incorrectamente
                            if ($index < $respuestasCorrectas) {
                                $respuestas[$pregunta->id] = $opciones->where('es_correcta', true)->first()->id ?? $opciones->first()->id;
                            } else {
                                $respuestas[$pregunta->id] = $opciones->where('es_correcta', false)->random()->id ?? $opciones->first()->id;
                            }
                        }
                    }

                    Resultado::create([
                        'evaluacion_id' => $evaluacion->id,
                        'estudiante_id' => $estudiante->id,
                        'puntaje' => round($puntaje, 2),
                    ]);
                }
            }
        }
    }

    /**
     * Genera número de respuestas correctas con distribución que simula
     * resultados académicos reales (campana de Gauss)
     */
    private function generarRespuestasConDistribucionNormal(int $totalPreguntas): int
    {
        // Media del 70% de respuestas correctas
        $media = $totalPreguntas * 0.7;
        $desviacion = $totalPreguntas * 0.15;

        // Generar valor con distribución normal aproximada
        $respuestasCorrectas = round($this->normalRandom($media, $desviacion));

        // Asegurar que esté dentro del rango válido
        return max(0, min($totalPreguntas, $respuestasCorrectas));
    }

    /**
     * Genera un número aleatorio con distribución normal aproximada
     */
    private function normalRandom(float $media, float $desviacion): float
    {
        // Método Box-Muller simplificado
        $u1 = mt_rand(1, 9999) / 10000;
        $u2 = mt_rand(1, 9999) / 10000;

        $z0 = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);

        return $z0 * $desviacion + $media;
    }
}
