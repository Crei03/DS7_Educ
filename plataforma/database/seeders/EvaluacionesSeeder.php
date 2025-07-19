<?php

namespace Database\Seeders;

use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Pregunta;
use App\Domain\Evaluacion\Models\Opcion;
use App\Domain\Curso\Models\Curso;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EvaluacionesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_PA');
        $cursos = Curso::all();

        // Crear evaluaciones para cada curso
        foreach ($cursos as $curso) {
            // Crear 1-2 evaluaciones por curso
            for ($e = 0; $e < rand(1, 2); $e++) {
                $evaluacion = Evaluacion::create([
                    'curso_id' => $curso->id,
                    'titulo' => $faker->randomElement([
                        'Examen Parcial',
                        'Examen Final',
                        'Quiz Semanal',
                        'Evaluación Práctica',
                        'Prueba de Conocimientos'
                    ]) . ' - ' . $curso->titulo,
                    'duracion_min' => $faker->randomElement([30, 45, 60, 90, 120]),
                ]);

                // Crear 5-10 preguntas por evaluación
                for ($p = 0; $p < rand(5, 10); $p++) {
                    $pregunta = Pregunta::create([
                        'evaluacion_id' => $evaluacion->id,
                        'enunciado' => $this->generarPreguntaContextual($curso->titulo, $faker),
                    ]);

                    // Crear 4 opciones por pregunta (1 correcta, 3 incorrectas)
                    $opciones = $this->generarOpciones($curso->titulo, $faker);

                    foreach ($opciones as $index => $textoOpcion) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'texto' => $textoOpcion,
                            'es_correcta' => $index === 0, // La primera opción es la correcta
                        ]);
                    }
                }
            }
        }
    }

    private function generarPreguntaContextual(string $nombreCurso, $faker): string
    {
        $preguntas = [
            'Matemáticas' => [
                '¿Cuál es el resultado de la ecuación 2x + 5 = 15?',
                '¿Qué tipo de función representa y = x²?',
                '¿Cuál es la derivada de x³?',
                '¿Qué es un número primo?',
                '¿Cuál es el área de un círculo con radio 5?'
            ],
            'Laravel' => [
                '¿Qué comando se usa para crear un modelo en Laravel?',
                '¿Cuál es el propósito de las migraciones?',
                '¿Qué es Eloquent en Laravel?',
                '¿Cómo se definen las rutas en Laravel?',
                '¿Qué es middleware en Laravel?'
            ],
            'Historia' => [
                '¿En qué año se independizó Panamá?',
                '¿Quién fue el primer presidente de Panamá?',
                '¿Cuándo se construyó el Canal de Panamá?',
                '¿Qué evento histórico marcó el siglo XX en Panamá?',
                '¿Cuál es el origen del nombre Panamá?'
            ],
            'default' => [
                '¿Cuál de las siguientes opciones es correcta?',
                '¿Qué concepto es fundamental en esta materia?',
                '¿Cuál es la definición más apropiada?',
                '¿Qué principio aplica en este caso?',
                '¿Cuál es la respuesta correcta?'
            ]
        ];

        // Buscar preguntas específicas por palabras clave
        foreach ($preguntas as $materia => $preguntasMateria) {
            if ($materia !== 'default' && stripos($nombreCurso, $materia) !== false) {
                return $faker->randomElement($preguntasMateria);
            }
        }

        return $faker->randomElement($preguntas['default']);
    }

    private function generarOpciones(string $nombreCurso, $faker): array
    {
        $opciones = [
            'Matemáticas' => [
                ['x = 5', 'x = 3', 'x = 7', 'x = 10'],
                ['Función cuadrática', 'Función lineal', 'Función cúbica', 'Función logarítmica'],
                ['3x²', '3x', 'x²', '2x²'],
            ],
            'Laravel' => [
                ['php artisan make:model', 'php artisan create:model', 'composer make:model', 'artisan new:model'],
                ['Versionar la base de datos', 'Crear vistas', 'Generar rutas', 'Configurar middleware'],
                ['ORM de Laravel', 'Framework CSS', 'Base de datos', 'Servidor web'],
            ],
            'Historia' => [
                ['1903', '1901', '1905', '1910'],
                ['Manuel Amador Guerrero', 'Belisario Porras', 'José Domingo de Obaldía', 'Carlos Antonio Mendoza'],
                ['1914', '1920', '1904', '1925'],
            ]
        ];

        // Generar opciones basadas en el curso
        foreach ($opciones as $materia => $opcionesMateria) {
            if (stripos($nombreCurso, $materia) !== false) {
                return $faker->randomElement($opcionesMateria);
            }
        }

        // Opciones genéricas
        return [
            'Opción A - Correcta',
            'Opción B - Incorrecta',
            'Opción C - Incorrecta',
            'Opción D - Incorrecta'
        ];
    }
}
