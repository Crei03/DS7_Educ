<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DataCoherenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_PA');

        // Limpiar todas las tablas primero
        $this->command->info('Limpiando base de datos...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('material_visto')->truncate();
        DB::table('resultados')->truncate();
        DB::table('opciones')->truncate();
        DB::table('preguntas')->truncate();
        DB::table('evaluaciones')->truncate();
        DB::table('matriculas')->truncate();
        DB::table('materiales')->truncate();
        DB::table('modulos')->truncate();
        DB::table('cursos')->truncate();
        DB::table('estudiantes')->truncate();
        DB::table('profesores')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Crear 40 profesores con datos coherentes
        $this->command->info('Creando profesores...');
        $profesores = [];
        $nombresProfesor = [
            ['Carlos', 'Eduardo', 'García', 'Martínez'],
            ['María', 'Elena', 'Rodríguez', 'López'],
            ['José', 'Antonio', 'Fernández', 'Sánchez'],
            ['Ana', 'Sofía', 'González', 'Pérez'],
            ['Luis', 'Fernando', 'Martín', 'Díaz'],
            ['Carmen', 'Isabel', 'Jiménez', 'Torres'],
            ['Francisco', 'Javier', 'Ruiz', 'Flores'],
            ['Patricia', 'Alejandra', 'Moreno', 'Rivera'],
            ['Manuel', 'Enrique', 'Álvarez', 'Gómez'],
            ['Rosa', 'Victoria', 'Romero', 'Herrera'],
        ];

        for ($i = 0; $i < 40; $i++) {
            $nombre = $nombresProfesor[$i % 10];
            $cedula = $faker->numerify('#-####-#####');
            $correo = strtolower($nombre[0] . '.' . $nombre[2]) . $i . '@academia.pa';

            $profesor = [
                'nombrePrimario' => $nombre[0],
                'nombreSecundario' => $nombre[1],
                'apellidoPrimario' => $nombre[2],
                'apellidoSecundario' => $nombre[3],
                'cedula' => $cedula,
                'correo' => $correo,
                'contrasena' => Hash::make('password123'),
                'creado_en' => now()->subDays(rand(30, 365)),
            ];

            $profesores[] = $profesor;
        }
        DB::table('profesores')->insert($profesores);

        // 2. Crear 40 estudiantes con datos coherentes
        $this->command->info('Creando estudiantes...');
        $estudiantes = [];
        $nombresEstudiante = [
            ['Diego', 'Andrés', 'Castro', 'Morales'],
            ['Valeria', 'Cristina', 'Vargas', 'Reyes'],
            ['Santiago', 'Alejandro', 'Mendoza', 'Cruz'],
            ['Camila', 'Fernanda', 'Guerrero', 'Navarro'],
            ['Sebastián', 'Daniel', 'Herrera', 'Medina'],
            ['Isabella', 'Gabriela', 'Peña', 'Ortiz'],
            ['Mateo', 'Nicolás', 'Ramos', 'Silva'],
            ['Sofía', 'Valentina', 'Aguilar', 'Vega'],
            ['Emilio', 'Gabriel', 'Delgado', 'Campos'],
            ['Lucía', 'Antonella', 'Rojas', 'Cortés'],
        ];

        for ($i = 0; $i < 40; $i++) {
            $nombre = $nombresEstudiante[$i % 10];
            $cedula = $faker->numerify('##-####-######');
            $correo = strtolower($nombre[0] . '.' . $nombre[2]) . ($i + 100) . '@estudiante.pa';

            $estudiante = [
                'nombrePrimario' => $nombre[0],
                'nombreSecundario' => $nombre[1],
                'apellidoPrimario' => $nombre[2],
                'apellidoSecundario' => $nombre[3],
                'cedula' => $cedula,
                'correo' => $correo,
                'contrasena' => Hash::make('student123'),
                'creado_en' => now()->subDays(rand(10, 200)),
            ];

            $estudiantes[] = $estudiante;
        }
        DB::table('estudiantes')->insert($estudiantes);

        // 3. Crear 40 cursos con datos coherentes
        $this->command->info('Creando cursos...');
        $cursos = [];
        $titulosCursos = [
            'PHP Básico para Principiantes',
            'Desarrollo Web con Laravel',
            'JavaScript Moderno y ES6+',
            'Bases de Datos MySQL Avanzado',
            'React.js desde Cero',
            'Python para Data Science',
            'Finanzas Personales Básicas',
            'Marketing Digital Estratégico',
            'Diseño UX/UI con Figma',
            'Contabilidad para Empresas',
            'Inglés Conversacional Intermedio',
            'Excel Avanzado para Negocios',
            'Fotografía Digital Profesional',
            'Gestión de Proyectos Ágiles',
            'Ventas y Atención al Cliente',
            'Adobe Photoshop Completo',
            'Redes Sociales y Community Management',
            'Programación en Java',
            'Análisis de Datos con Power BI',
            'Emprendimiento Digital',
        ];

        for ($i = 0; $i < 40; $i++) {
            $titulo = $titulosCursos[$i % 20] . ($i >= 20 ? ' - Nivel Avanzado' : '');
            $profesorId = ($i % 40) + 1;

            $curso = [
                'titulo' => $titulo,
                'descripcion' => 'Curso completo de ' . strtolower($titulo) . '. Aprenderás desde los conceptos básicos hasta técnicas avanzadas con ejercicios prácticos y proyectos reales.',
                'profesor_id' => $profesorId,
                'creado_en' => now()->subDays(rand(5, 100)),
            ];

            $cursos[] = $curso;
        }
        DB::table('cursos')->insert($cursos);

        // 4. Crear módulos para cada curso (3-5 módulos por curso)
        $this->command->info('Creando módulos...');
        $modulos = [];
        $titulosModulos = [
            'Introducción y Conceptos Básicos',
            'Fundamentos Teóricos',
            'Práctica Dirigida',
            'Proyecto Intermedio',
            'Técnicas Avanzadas',
            'Casos de Estudio',
            'Proyecto Final',
            'Evaluación y Certificación'
        ];

        $moduloId = 1;
        for ($cursoId = 1; $cursoId <= 40; $cursoId++) {
            $numModulos = rand(3, 5);
            for ($j = 1; $j <= $numModulos; $j++) {
                $modulo = [
                    'id' => $moduloId,
                    'curso_id' => $cursoId,
                    'titulo' => 'Módulo ' . $j . ': ' . $titulosModulos[$j - 1],
                    'orden' => $j,
                ];
                $modulos[] = $modulo;
                $moduloId++;
            }
        }
        DB::table('modulos')->insert($modulos);

        // 5. Crear materiales para cada módulo (2-4 materiales por módulo)
        $this->command->info('Creando materiales...');
        $materiales = [];
        $tiposMaterial = ['pdf', 'link', 'zip'];
        $titulosMateriales = [
            'Guía de Introducción',
            'Manual de Usuario',
            'Video Tutorial',
            'Ejercicios Prácticos',
            'Presentación del Tema',
            'Código Fuente de Ejemplo',
            'Documentación Técnica',
            'Recursos Adicionales'
        ];

        $modulos = DB::table('modulos')->get();
        foreach ($modulos as $modulo) {
            $numMateriales = rand(2, 4);
            for ($k = 0; $k < $numMateriales; $k++) {
                $tipo = $tiposMaterial[array_rand($tiposMaterial)];
                $titulo = $titulosMateriales[$k % 8];

                $url = match ($tipo) {
                    'pdf' => 'https://academia.pa/materiales/curso-' . $modulo->curso_id . '-modulo-' . $modulo->id . '-' . ($k + 1) . '.pdf',
                    'link' => 'https://youtube.com/watch?v=ejemplo-' . $modulo->id . '-' . ($k + 1),
                    'zip' => 'https://academia.pa/descargas/recursos-modulo-' . $modulo->id . '-' . ($k + 1) . '.zip',
                };

                $material = [
                    'modulo_id' => $modulo->id,
                    'tipo' => $tipo,
                    'url' => $url,
                    'titulo' => $titulo . ' - Módulo ' . $modulo->orden,
                ];

                $materiales[] = $material;
            }
        }
        DB::table('materiales')->insert($materiales);

        // 6. Crear matrículas (cada estudiante en 2-4 cursos)
        $this->command->info('Creando matrículas...');
        $matriculas = [];
        for ($estudianteId = 1; $estudianteId <= 40; $estudianteId++) {
            $numCursos = rand(2, 4);
            $cursosAsignados = [];

            for ($i = 0; $i < $numCursos; $i++) {
                do {
                    $cursoId = rand(1, 40);
                } while (in_array($cursoId, $cursosAsignados));

                $cursosAsignados[] = $cursoId;

                $matricula = [
                    'estudiante_id' => $estudianteId,
                    'curso_id' => $cursoId,
                    'fecha' => $faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
                    'progreso' => rand(0, 10000) / 100, // 0.00 a 100.00
                ];

                $matriculas[] = $matricula;
            }
        }
        DB::table('matriculas')->insert($matriculas);

        // 7. Crear evaluaciones (1-2 por curso)
        $this->command->info('Creando evaluaciones...');
        $evaluaciones = [];
        for ($cursoId = 1; $cursoId <= 40; $cursoId++) {
            $numEvaluaciones = rand(1, 2);
            for ($i = 1; $i <= $numEvaluaciones; $i++) {
                $evaluacion = [
                    'curso_id' => $cursoId,
                    'titulo' => $i == 1 ? 'Examen Parcial' : 'Examen Final',
                    'duracion_min' => rand(30, 120),
                ];
                $evaluaciones[] = $evaluacion;
            }
        }
        DB::table('evaluaciones')->insert($evaluaciones);

        // 8. Crear preguntas para cada evaluación (5-10 preguntas)
        $this->command->info('Creando preguntas...');
        $preguntas = [];
        $enunciadosEjemplo = [
            '¿Cuál es la sintaxis correcta para declarar una variable en PHP?',
            '¿Qué significa el acrónimo HTML?',
            'Selecciona la definición correcta de base de datos:',
            '¿Cuál es la principal ventaja de usar un framework?',
            'En programación orientada a objetos, ¿qué es una clase?',
            '¿Qué protocolo se utiliza para la transferencia de páginas web?',
            'Selecciona el tipo de dato correcto en JavaScript:',
            '¿Cuál es la función de CSS en el desarrollo web?',
            '¿Qué es un bucle en programación?',
            'Define el concepto de algoritmo:'
        ];

        $evaluaciones = DB::table('evaluaciones')->get();
        foreach ($evaluaciones as $evaluacion) {
            $numPreguntas = rand(5, 10);
            for ($i = 0; $i < $numPreguntas; $i++) {
                $pregunta = [
                    'evaluacion_id' => $evaluacion->id,
                    'enunciado' => $enunciadosEjemplo[$i % 10] . ' (Evaluación ' . $evaluacion->id . ')',
                ];
                $preguntas[] = $pregunta;
            }
        }
        DB::table('preguntas')->insert($preguntas);

        // 9. Crear opciones para cada pregunta (4 opciones c/u)
        $this->command->info('Creando opciones...');
        $opciones = [];
        $opcionesEjemplo = [
            ['Opción A: Respuesta incorrecta', 'Opción B: Respuesta correcta', 'Opción C: Respuesta incorrecta', 'Opción D: Respuesta incorrecta'],
            ['Opción A: Respuesta incorrecta', 'Opción B: Respuesta incorrecta', 'Opción C: Respuesta correcta', 'Opción D: Respuesta incorrecta'],
            ['Opción A: Respuesta correcta', 'Opción B: Respuesta incorrecta', 'Opción C: Respuesta incorrecta', 'Opción D: Respuesta incorrecta'],
            ['Opción A: Respuesta incorrecta', 'Opción B: Respuesta incorrecta', 'Opción C: Respuesta incorrecta', 'Opción D: Respuesta correcta'],
        ];

        $preguntas = DB::table('preguntas')->get();
        foreach ($preguntas as $pregunta) {
            $setOpciones = $opcionesEjemplo[($pregunta->id - 1) % 4];
            $correctaIndex = [1, 2, 0, 3][($pregunta->id - 1) % 4]; // B, C, A, D

            for ($i = 0; $i < 4; $i++) {
                $opcion = [
                    'pregunta_id' => $pregunta->id,
                    'texto' => $setOpciones[$i],
                    'es_correcta' => $i === $correctaIndex,
                ];
                $opciones[] = $opcion;
            }
        }
        DB::table('opciones')->insert($opciones);

        // 10. Crear resultados (algunos estudiantes han tomado evaluaciones)
        $this->command->info('Creando resultados...');
        $resultados = [];
        $matriculas = DB::table('matriculas')->get();

        foreach ($matriculas as $matricula) {
            // Solo algunos estudiantes han tomado evaluaciones (70% de probabilidad)
            if (rand(1, 100) <= 70) {
                $evaluacionesCurso = DB::table('evaluaciones')->where('curso_id', $matricula->curso_id)->get();

                foreach ($evaluacionesCurso as $evaluacion) {
                    // 80% de probabilidad de haber tomado cada evaluación
                    if (rand(1, 100) <= 80) {
                        $resultado = [
                            'evaluacion_id' => $evaluacion->id,
                            'estudiante_id' => $matricula->estudiante_id,
                            'puntaje' => rand(6000, 10000) / 100, // 60.00 a 100.00
                            'fecha' => $faker->dateTimeBetween('-30 days', 'now'),
                        ];
                        $resultados[] = $resultado;
                    }
                }
            }
        }
        DB::table('resultados')->insert($resultados);

        // 11. Crear registros de material_visto
        $this->command->info('Creando registros de material visto...');
        $materialVisto = [];
        $matriculas = DB::table('matriculas')->get();

        foreach ($matriculas as $matricula) {
            // Obtener módulos del curso
            $modulos = DB::table('modulos')->where('curso_id', $matricula->curso_id)->get();

            foreach ($modulos as $modulo) {
                $materiales = DB::table('materiales')->where('modulo_id', $modulo->id)->get();

                foreach ($materiales as $material) {
                    // 60% de probabilidad de haber visto cada material
                    if (rand(1, 100) <= 60) {
                        $visto = [
                            'estudiante_id' => $matricula->estudiante_id,
                            'material_id' => $material->id,
                            'visto_en' => $faker->dateTimeBetween('-30 days', 'now'),
                        ];
                        $materialVisto[] = $visto;
                    }
                }
            }
        }

        if (!empty($materialVisto)) {
            DB::table('material_visto')->insert($materialVisto);
        }

        $this->command->info('✅ Seeder ejecutado exitosamente!');
        $this->command->info('📊 Resumen de datos creados:');
        $this->command->info('- 40 Profesores');
        $this->command->info('- 40 Estudiantes');
        $this->command->info('- 40 Cursos');
        $this->command->info('- ' . DB::table('modulos')->count() . ' Módulos');
        $this->command->info('- ' . DB::table('materiales')->count() . ' Materiales');
        $this->command->info('- ' . DB::table('matriculas')->count() . ' Matrículas');
        $this->command->info('- ' . DB::table('evaluaciones')->count() . ' Evaluaciones');
        $this->command->info('- ' . DB::table('preguntas')->count() . ' Preguntas');
        $this->command->info('- ' . DB::table('opciones')->count() . ' Opciones');
        $this->command->info('- ' . DB::table('resultados')->count() . ' Resultados');
        $this->command->info('- ' . DB::table('material_visto')->count() . ' Registros de Material Visto');
    }
}
