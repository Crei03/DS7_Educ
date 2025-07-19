<?php

namespace Database\Seeders;

use App\Domain\ContenidoDigital\Models\Modulo;
use App\Domain\Curso\Models\Curso;
use Illuminate\Database\Seeder;

class ModulosSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener cursos existentes
        $cursos = Curso::all();

        if ($cursos->isEmpty()) {
            $this->command->info('No hay cursos disponibles. Ejecuta CursosSeeder primero.');
            return;
        }

        $modulosPorCurso = [
            'Introducción a Laravel' => [
                'Fundamentos del Framework',
                'Instalación y Configuración',
                'Arquitectura MVC',
                'Routing y Controllers',
                'Views y Blade',
            ],
            'JavaScript Avanzado' => [
                'Conceptos Avanzados',
                'Promises y Async/Await',
                'Manipulación del DOM',
                'APIs y Fetch',
                'Proyecto Final',
            ],
            'Diseño UI/UX' => [
                'Principios de Diseño',
                'Research y User Personas',
                'Wireframes y Prototyping',
                'Design Systems',
                'Testing de Usabilidad',
            ],
            'Marketing Digital' => [
                'Fundamentos del Marketing Digital',
                'SEO y SEM',
                'Redes Sociales',
                'Email Marketing',
                'Analytics y Métricas',
            ],
        ];

        $modulosGenericos = [
            'Módulo 1',
            'Módulo 2',
            'Módulo 3',
            'Módulo 4',
            'Módulo 5'
        ];

        foreach ($cursos as $curso) {
            $modulos = $modulosPorCurso[$curso->nombre] ?? $modulosGenericos;
            foreach ($modulos as $orden => $tituloModulo) {
                Modulo::create([
                    'curso_id' => $curso->id,
                    'titulo' => $tituloModulo,
                    'orden' => $orden + 1,
                ]);
            }
            $this->command->info("✅ Creados " . count($modulos) . " módulos para el curso: {$curso->nombre}");
        }
        $totalModulos = Modulo::count();
        $this->command->info("🎯 Total de módulos creados: {$totalModulos}");
    }
}
