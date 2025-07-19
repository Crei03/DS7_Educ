<?php

namespace Database\Seeders;

use App\Domain\Curso\Models\Curso;
use App\Domain\Curso\Models\Profesor;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CursosSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_PA');
        $profesores = Profesor::all();

        $cursosTitulos = [
            'Matemáticas Básicas',
            'Álgebra Intermedia',
            'Cálculo Diferencial',
            'Estadística Aplicada',
            'Química General',
            'Física Moderna',
            'Biología Molecular',
            'Historia de Panamá',
            'Literatura Hispanoamericana',
            'Inglés Conversacional',
            'Inglés Técnico',
            'Informática Básica',
            'Programación Web',
            'Bases de Datos',
            'Administración de Empresas',
            'Contabilidad General',
            'Economía Panameña',
        ];

        foreach ($cursosTitulos as $titulo) {
            Curso::create([
                'titulo' => $titulo,
                'descripcion' => $faker->paragraph(3),
                'profesor_id' => $profesores->random()->id,
            ]);
        }
    }
}
