<?php

namespace Database\Seeders;

use App\Domain\ContenidoDigital\Models\Material;
use Illuminate\Database\Seeder;

class MaterialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materiales = [
            ['id' => 1, 'modulo_id' => 1, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/python_intro.mp4', 'titulo' => 'Video: ¿Qué es Python?'],
            ['id' => 2, 'modulo_id' => 1, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/python_sintaxis.pdf', 'titulo' => 'PDF: Sintaxis Básica de Python'],
            ['id' => 3, 'modulo_id' => 2, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/python_listas.mp4', 'titulo' => 'Video: Listas y Tuplas'],
            ['id' => 4, 'modulo_id' => 2, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/python_diccionarios.pdf', 'titulo' => 'PDF: Diccionarios y Conjuntos'],
            ['id' => 5, 'modulo_id' => 3, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/react_componentes.mp4', 'titulo' => 'Video: Componentes y Props'],
            ['id' => 6, 'modulo_id' => 3, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/react_jsx.pdf', 'titulo' => 'PDF: Introducción a JSX'],
            ['id' => 7, 'modulo_id' => 4, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/react_useState.mp4', 'titulo' => 'Video: El Hook useState'],
            ['id' => 8, 'modulo_id' => 4, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/react_useEffect.pdf', 'titulo' => 'PDF: El Hook useEffect'],
            ['id' => 9, 'modulo_id' => 5, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/sql_select.mp4', 'titulo' => 'Video: La Cláusula SELECT'],
            ['id' => 10, 'modulo_id' => 5, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/sql_where.pdf', 'titulo' => 'PDF: Filtrando con WHERE'],
            ['id' => 11, 'modulo_id' => 6, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/sql_joins.mp4', 'titulo' => 'Video: Uniendo Tablas con JOIN'],
            ['id' => 12, 'modulo_id' => 6, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/sql_normalizacion.pdf', 'titulo' => 'PDF: Normalización de Bases de Datos'],
            ['id' => 13, 'modulo_id' => 7, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/seo_onpage.mp4', 'titulo' => 'Video: SEO On-Page'],
            ['id' => 14, 'modulo_id' => 7, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/seo_keywords.pdf', 'titulo' => 'PDF: Investigación de Palabras Clave'],
            ['id' => 15, 'modulo_id' => 8, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/marketing_contenidos.mp4', 'titulo' => 'Video: Creación de Contenido Atractivo'],
            ['id' => 16, 'modulo_id' => 8, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/marketing_facebookads.pdf', 'titulo' => 'PDF: Guía de Facebook Ads'],
            ['id' => 17, 'modulo_id' => 9, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/illustrator_interfaz.mp4', 'titulo' => 'Video: Conociendo la Interfaz'],
            ['id' => 18, 'modulo_id' => 9, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/illustrator_atajos.pdf', 'titulo' => 'PDF: Atajos de Teclado Esenciales'],
            ['id' => 19, 'modulo_id' => 10, 'tipo' => 'LINK', 'url' => 'https://academia.pa/videos/illustrator_pluma.mp4', 'titulo' => 'Video: La Herramienta Pluma'],
            ['id' => 20, 'modulo_id' => 10, 'tipo' => 'PDF', 'url' => 'https://academia.pa/docs/illustrator_logotipos.pdf', 'titulo' => 'PDF: Diseño de Logotipos Vectoriales'],
        ];

        foreach ($materiales as $material) {
            Material::create($material);
        }
    }
}
