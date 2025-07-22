<?php

namespace Database\Seeders;

use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Pregunta;
use App\Domain\Evaluacion\Models\Opcion;
use Illuminate\Database\Seeder;

class EvaluacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $evaluaciones = [
            ['id' => 1, 'curso_id' => 1, 'titulo' => 'Examen Final de Python', 'duracion_min' => 60],
            ['id' => 2, 'curso_id' => 2, 'titulo' => 'Prueba de Componentes en React', 'duracion_min' => 45],
            ['id' => 3, 'curso_id' => 3, 'titulo' => 'Quiz de Consultas SQL', 'duracion_min' => 30],
            ['id' => 4, 'curso_id' => 4, 'titulo' => 'Evaluación de Estrategias de Marketing', 'duracion_min' => 60],
            ['id' => 5, 'curso_id' => 5, 'titulo' => 'Prueba Práctica de Illustrator', 'duracion_min' => 90],
            ['id' => 6, 'curso_id' => 6, 'titulo' => 'Examen de Fundamentos de Ciencia de Datos', 'duracion_min' => 75],
            ['id' => 7, 'curso_id' => 7, 'titulo' => 'Quiz de Widgets Básicos en Flutter', 'duracion_min' => 30],
            ['id' => 8, 'curso_id' => 8, 'titulo' => 'Evaluación de Conceptos de Scrum', 'duracion_min' => 45],
            ['id' => 9, 'curso_id' => 9, 'titulo' => 'Prueba de Ciberseguridad Básica', 'duracion_min' => 60],
            ['id' => 10, 'curso_id' => 10, 'titulo' => 'Examen de Vocabulario de Negocios', 'duracion_min' => 45],
            ['id' => 11, 'curso_id' => 1, 'titulo' => 'Prueba Corta: Variables y Tipos de Datos', 'duracion_min' => 20],
            ['id' => 12, 'curso_id' => 2, 'titulo' => 'Quiz: JSX y Renderizado', 'duracion_min' => 25],
            ['id' => 13, 'curso_id' => 3, 'titulo' => 'Prueba: JOINs y Subconsultas', 'duracion_min' => 40],
            ['id' => 14, 'curso_id' => 6, 'titulo' => 'Evaluación: Limpieza y Preparación de Datos', 'duracion_min' => 60],
            ['id' => 15, 'curso_id' => 7, 'titulo' => 'Examen Práctico: Creando un Layout Básico', 'duracion_min' => 75],
            ['id' => 16, 'curso_id' => 13, 'titulo' => 'Prueba de API REST con Express', 'duracion_min' => 50],
            ['id' => 17, 'curso_id' => 14, 'titulo' => 'Quiz de Algoritmos de Clasificación', 'duracion_min' => 35],
            ['id' => 18, 'curso_id' => 17, 'titulo' => 'Examen Parcial de Límites y Derivadas', 'duracion_min' => 90],
            ['id' => 19, 'curso_id' => 18, 'titulo' => 'Prueba de Cinemática', 'duracion_min' => 60],
            ['id' => 20, 'curso_id' => 19, 'titulo' => 'Quiz de Estequiometría', 'duracion_min' => 45],
        ];

        $preguntas = [
            ['id' => 1, 'evaluacion_id' => 1, 'enunciado' => '¿Cuál es la palabra clave para definir una función en Python?'],
            ['id' => 2, 'evaluacion_id' => 1, 'enunciado' => '¿Qué tipo de dato es inmutable en Python?'],
            ['id' => 3, 'evaluacion_id' => 2, 'enunciado' => '¿Cómo se pasa datos de un componente padre a un hijo en React?'],
            ['id' => 4, 'evaluacion_id' => 2, 'enunciado' => '¿Qué hook se utiliza para manejar el estado en un componente funcional?'],
            ['id' => 5, 'evaluacion_id' => 3, 'enunciado' => '¿Qué cláusula se usa para filtrar resultados en una consulta SQL?'],
            ['id' => 6, 'evaluacion_id' => 3, 'enunciado' => '¿Cuál es el JOIN que devuelve solo las filas que coinciden en ambas tablas?'],
            ['id' => 7, 'evaluacion_id' => 4, 'enunciado' => '¿Qué significa SEO?'],
            ['id' => 8, 'evaluacion_id' => 5, 'enunciado' => '¿Cuál es la herramienta principal para crear y modificar trazados vectoriales en Illustrator?'],
            ['id' => 9, 'evaluacion_id' => 6, 'enunciado' => '¿Qué librería de Python es fundamental para la manipulación y análisis de datos?'],
            ['id' => 10, 'evaluacion_id' => 7, 'enunciado' => 'En Flutter, ¿todo es un...?'],
            ['id' => 11, 'evaluacion_id' => 11, 'enunciado' => '¿Cómo se declara una variable de tipo entero en Python?'],
            ['id' => 12, 'evaluacion_id' => 11, 'enunciado' => '¿El resultado de 5 / 2 en Python 3 es?'],
            ['id' => 13, 'evaluacion_id' => 12, 'enunciado' => '¿Qué significa JSX?'],
            ['id' => 14, 'evaluacion_id' => 12, 'enunciado' => '¿Es obligatorio usar llaves {} para renderizar variables de JavaScript en JSX?'],
            ['id' => 15, 'evaluacion_id' => 13, 'enunciado' => '¿Qué tipo de JOIN devuelve todos los registros de la tabla izquierda y los registros coincidentes de la tabla derecha?'],
            ['id' => 16, 'evaluacion_id' => 13, 'enunciado' => '¿Se puede anidar una consulta SELECT dentro de otra?'],
            ['id' => 17, 'evaluacion_id' => 18, 'enunciado' => '¿Cuál es la derivada de x^2?'],
            ['id' => 18, 'evaluacion_id' => 18, 'enunciado' => '¿A qué tiende la función 1/x cuando x tiende a infinito?'],
            ['id' => 19, 'evaluacion_id' => 19, 'enunciado' => 'Si un objeto se mueve con velocidad constante, ¿cuál es su aceleración?'],
            ['id' => 20, 'evaluacion_id' => 20, 'enunciado' => '¿Qué ley establece que la materia no se crea ni se destruye?'],
        ];

        $opciones = [
            ['id' => 1, 'pregunta_id' => 1, 'texto' => 'function', 'es_correcta' => 0],
            ['id' => 2, 'pregunta_id' => 1, 'texto' => 'def', 'es_correcta' => 1],
            ['id' => 3, 'pregunta_id' => 1, 'texto' => 'fun', 'es_correcta' => 0],
            ['id' => 4, 'pregunta_id' => 2, 'texto' => 'Lista (list)', 'es_correcta' => 0],
            ['id' => 5, 'pregunta_id' => 2, 'texto' => 'Diccionario (dict)', 'es_correcta' => 0],
            ['id' => 6, 'pregunta_id' => 2, 'texto' => 'Tupla (tuple)', 'es_correcta' => 1],
            ['id' => 7, 'pregunta_id' => 3, 'texto' => 'A través de los "props"', 'es_correcta' => 1],
            ['id' => 8, 'pregunta_id' => 3, 'texto' => 'Usando el "state"', 'es_correcta' => 0],
            ['id' => 9, 'pregunta_id' => 3, 'texto' => 'No se pueden pasar datos', 'es_correcta' => 0],
            ['id' => 10, 'pregunta_id' => 4, 'texto' => 'useEffect', 'es_correcta' => 0],
            ['id' => 11, 'pregunta_id' => 4, 'texto' => 'useState', 'es_correcta' => 1],
            ['id' => 12, 'pregunta_id' => 4, 'texto' => 'useContext', 'es_correcta' => 0],
            ['id' => 13, 'pregunta_id' => 5, 'texto' => 'FILTER', 'es_correcta' => 0],
            ['id' => 14, 'pregunta_id' => 5, 'texto' => 'WHERE', 'es_correcta' => 1],
            ['id' => 15, 'pregunta_id' => 5, 'texto' => 'SORT', 'es_correcta' => 0],
            ['id' => 16, 'pregunta_id' => 6, 'texto' => 'LEFT JOIN', 'es_correcta' => 0],
            ['id' => 17, 'pregunta_id' => 6, 'texto' => 'RIGHT JOIN', 'es_correcta' => 0],
            ['id' => 18, 'pregunta_id' => 6, 'texto' => 'INNER JOIN', 'es_correcta' => 1],
            ['id' => 19, 'pregunta_id' => 7, 'texto' => 'Search Engine Optimization', 'es_correcta' => 1],
            ['id' => 20, 'pregunta_id' => 7, 'texto' => 'Social Engine Optimization', 'es_correcta' => 0],
            ['id' => 21, 'pregunta_id' => 8, 'texto' => 'Pincel', 'es_correcta' => 0],
            ['id' => 22, 'pregunta_id' => 8, 'texto' => 'Pluma', 'es_correcta' => 1],
            ['id' => 23, 'pregunta_id' => 9, 'texto' => 'NumPy', 'es_correcta' => 0],
            ['id' => 24, 'pregunta_id' => 9, 'texto' => 'Pandas', 'es_correcta' => 1],
            ['id' => 25, 'pregunta_id' => 10, 'texto' => 'Widget', 'es_correcta' => 1],
            ['id' => 26, 'pregunta_id' => 10, 'texto' => 'Componente', 'es_correcta' => 0],
            ['id' => 27, 'pregunta_id' => 11, 'texto' => 'int x = 5', 'es_correcta' => 0],
            ['id' => 28, 'pregunta_id' => 11, 'texto' => 'x = 5', 'es_correcta' => 1],
            ['id' => 29, 'pregunta_id' => 12, 'texto' => '2', 'es_correcta' => 0],
            ['id' => 30, 'pregunta_id' => 12, 'texto' => '2.5', 'es_correcta' => 1],
            ['id' => 31, 'pregunta_id' => 13, 'texto' => 'JavaScript XML', 'es_correcta' => 1],
            ['id' => 32, 'pregunta_id' => 13, 'texto' => 'Java Syntax Extension', 'es_correcta' => 0],
            ['id' => 33, 'pregunta_id' => 14, 'texto' => 'Sí', 'es_correcta' => 1],
            ['id' => 34, 'pregunta_id' => 14, 'texto' => 'No', 'es_correcta' => 0],
            ['id' => 35, 'pregunta_id' => 15, 'texto' => 'RIGHT JOIN', 'es_correcta' => 0],
            ['id' => 36, 'pregunta_id' => 15, 'texto' => 'LEFT JOIN', 'es_correcta' => 1],
            ['id' => 37, 'pregunta_id' => 16, 'texto' => 'Sí, se conoce como subconsulta', 'es_correcta' => 1],
            ['id' => 38, 'pregunta_id' => 16, 'texto' => 'No, está prohibido en SQL', 'es_correcta' => 0],
            ['id' => 39, 'pregunta_id' => 17, 'texto' => '2x', 'es_correcta' => 1],
            ['id' => 40, 'pregunta_id' => 17, 'texto' => 'x', 'es_correcta' => 0],
            ['id' => 41, 'pregunta_id' => 18, 'texto' => '1', 'es_correcta' => 0],
            ['id' => 42, 'pregunta_id' => 18, 'texto' => '0', 'es_correcta' => 1],
            ['id' => 43, 'pregunta_id' => 19, 'texto' => 'Cero', 'es_correcta' => 1],
            ['id' => 44, 'pregunta_id' => 19, 'texto' => 'Constante y positiva', 'es_correcta' => 0],
            ['id' => 45, 'pregunta_id' => 20, 'texto' => 'Ley de la Conservación de la Energía', 'es_correcta' => 0],
            ['id' => 46, 'pregunta_id' => 20, 'texto' => 'Ley de la Conservación de la Masa', 'es_correcta' => 1],
        ];

        foreach ($evaluaciones as $evaluacion) {
            Evaluacion::create($evaluacion);
        }

        foreach ($preguntas as $pregunta) {
            Pregunta::create($pregunta);
        }

        foreach ($opciones as $opcion) {
            Opcion::create($opcion);
        }
    }
}
