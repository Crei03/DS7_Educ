<?php

namespace Database\Seeders;

use App\Domain\ContenidoDigital\Models\Modulo;
use Illuminate\Database\Seeder;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modulos = [
            ['id' => 1, 'curso_id' => 1, 'titulo' => 'Introducción a Python', 'orden' => 1],
            ['id' => 2, 'curso_id' => 1, 'titulo' => 'Estructuras de Datos', 'orden' => 2],
            ['id' => 3, 'curso_id' => 2, 'titulo' => 'Fundamentos de React', 'orden' => 1],
            ['id' => 4, 'curso_id' => 2, 'titulo' => 'Hooks y State Management', 'orden' => 2],
            ['id' => 5, 'curso_id' => 3, 'titulo' => 'Consultas SQL Básicas', 'orden' => 1],
            ['id' => 6, 'curso_id' => 3, 'titulo' => 'Modelado de Datos', 'orden' => 2],
            ['id' => 7, 'curso_id' => 4, 'titulo' => 'SEO y SEM', 'orden' => 1],
            ['id' => 8, 'curso_id' => 4, 'titulo' => 'Redes Sociales y Content Marketing', 'orden' => 2],
            ['id' => 9, 'curso_id' => 5, 'titulo' => 'Herramientas y Espacio de Trabajo', 'orden' => 1],
            ['id' => 10, 'curso_id' => 5, 'titulo' => 'Creación de Vectores y Logos', 'orden' => 2],
            ['id' => 11, 'curso_id' => 6, 'titulo' => 'Introducción a Pandas y NumPy', 'orden' => 1],
            ['id' => 12, 'curso_id' => 6, 'titulo' => 'Visualización de Datos con Matplotlib', 'orden' => 2],
            ['id' => 13, 'curso_id' => 7, 'titulo' => 'Configuración del Entorno de Desarrollo', 'orden' => 1],
            ['id' => 14, 'curso_id' => 7, 'titulo' => 'Widgets y Layouts en Flutter', 'orden' => 2],
            ['id' => 15, 'curso_id' => 8, 'titulo' => 'Principios de Scrum', 'orden' => 1],
            ['id' => 16, 'curso_id' => 8, 'titulo' => 'Kanban y Lean', 'orden' => 2],
            ['id' => 17, 'curso_id' => 9, 'titulo' => 'Tipos de Ataques y Vulnerabilidades', 'orden' => 1],
            ['id' => 18, 'curso_id' => 9, 'titulo' => 'Criptografía y Seguridad en Redes', 'orden' => 2],
            ['id' => 19, 'curso_id' => 10, 'titulo' => 'Presentaciones y Reuniones', 'orden' => 1],
            ['id' => 20, 'curso_id' => 10, 'titulo' => 'Negociación y Networking', 'orden' => 2],
        ];

        foreach ($modulos as $modulo) {
            Modulo::create($modulo);
        }
    }
}
