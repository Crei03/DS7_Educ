<?php

namespace Database\Seeders;

use App\Domain\Curso\Models\Curso;
use Illuminate\Database\Seeder;

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cursos = [
            [
                'id' => 1,
                'titulo' => 'Introducción a la Programación con Python',
                'descripcion' => 'Aprende los fundamentos de la programación utilizando Python.',
                'profesor_id' => 1,
            ],
            [
                'id' => 2,
                'titulo' => 'Desarrollo Web Frontend con React',
                'descripcion' => 'Crea interfaces de usuario interactivas y modernas con React.',
                'profesor_id' => 2,
            ],
            [
                'id' => 3,
                'titulo' => 'Bases de Datos SQL desde Cero',
                'descripcion' => 'Domina el lenguaje SQL y el diseño de bases de datos relacionales.',
                'profesor_id' => 3,
            ],
            [
                'id' => 4,
                'titulo' => 'Marketing Digital para Emprendedores',
                'descripcion' => 'Estrategias y herramientas para potenciar tu negocio en línea.',
                'profesor_id' => 4,
            ],
            [
                'id' => 5,
                'titulo' => 'Diseño Gráfico con Adobe Illustrator',
                'descripcion' => 'Aprende a crear ilustraciones y gráficos vectoriales profesionales.',
                'profesor_id' => 5,
            ],
            [
                'id' => 6,
                'titulo' => 'Introducción a la Ciencia de Datos',
                'descripcion' => 'Conceptos fundamentales y herramientas para el análisis de datos.',
                'profesor_id' => 6,
            ],
            [
                'id' => 7,
                'titulo' => 'Desarrollo de Aplicaciones Móviles con Flutter',
                'descripcion' => 'Crea aplicaciones nativas para iOS y Android desde una sola base de código.',
                'profesor_id' => 7,
            ],
            [
                'id' => 8,
                'titulo' => 'Gestión de Proyectos con Metodologías Ágiles',
                'descripcion' => 'Aprende a gestionar proyectos de forma eficiente con Scrum y Kanban.',
                'profesor_id' => 8,
            ],
            [
                'id' => 9,
                'titulo' => 'Fundamentos de Ciberseguridad',
                'descripcion' => 'Protege sistemas y redes de amenazas informáticas.',
                'profesor_id' => 9,
            ],
            [
                'id' => 10,
                'titulo' => 'Inglés de Negocios',
                'descripcion' => 'Mejora tus habilidades de comunicación en inglés en un entorno profesional.',
                'profesor_id' => 10,
            ],
            [
                'id' => 11,
                'titulo' => 'Contabilidad para No Contadores',
                'descripcion' => 'Entiende los principios básicos de la contabilidad y las finanzas.',
                'profesor_id' => 11,
            ],
            [
                'id' => 12,
                'titulo' => 'Fotografía Digital para Principiantes',
                'descripcion' => 'Aprende a manejar tu cámara y a componer fotografías impactantes.',
                'profesor_id' => 12,
            ],
            [
                'id' => 13,
                'titulo' => 'Desarrollo Backend con Node.js y Express',
                'descripcion' => 'Construye APIs RESTful robustas y escalables.',
                'profesor_id' => 1,
            ],
            [
                'id' => 14,
                'titulo' => 'Machine Learning Aplicado',
                'descripcion' => 'Implementa modelos de aprendizaje automático para resolver problemas reales.',
                'profesor_id' => 6,
            ],
            [
                'id' => 15,
                'titulo' => 'Animación 2D con Toon Boom Harmony',
                'descripcion' => 'Crea animaciones fluidas y profesionales.',
                'profesor_id' => 5,
            ],
            [
                'id' => 16,
                'titulo' => 'Oratoria y Hablar en Público',
                'descripcion' => 'Desarrolla tu confianza y habilidades para comunicarte efectivamente.',
                'profesor_id' => 4,
            ],
            [
                'id' => 17,
                'titulo' => 'Cálculo para Ingenieros',
                'descripcion' => 'Repaso de los conceptos fundamentales del cálculo diferencial e integral.',
                'profesor_id' => 17,
            ],
            [
                'id' => 18,
                'titulo' => 'Física Mecánica',
                'descripcion' => 'Estudio del movimiento, las fuerzas y la energía.',
                'profesor_id' => 18,
            ],
            [
                'id' => 19,
                'titulo' => 'Química General',
                'descripcion' => 'Principios básicos de la química, la materia y sus transformaciones.',
                'profesor_id' => 19,
            ],
            [
                'id' => 20,
                'titulo' => 'Historia del Arte Moderno',
                'descripcion' => 'Un recorrido por los movimientos artísticos desde el siglo XIX hasta la actualidad.',
                'profesor_id' => 20,
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::create($curso);
        }
    }
}
