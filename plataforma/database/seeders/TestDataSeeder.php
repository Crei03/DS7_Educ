<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Estudiante\Models\Estudiante;
use App\Domain\Curso\Models\Profesor;
use App\Domain\Curso\Models\Curso;
use App\Domain\ContenidoDigital\Models\Modulo;
use App\Domain\ContenidoDigital\Models\Material;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear profesor de prueba
        $profesor = Profesor::create([
            'nombrePrimario' => 'Juan',
            'nombreSecundario' => 'Carlos',
            'apellidoPrimario' => 'González',
            'apellidoSecundario' => 'Pérez',
            'cedula' => '8-123-4567',
            'correo' => 'profesor@test.com',
            'contrasena' => Hash::make('password123')
        ]);

        // Crear estudiante de prueba
        $estudiante = Estudiante::create([
            'nombrePrimario' => 'María',
            'nombreSecundario' => 'Elena',
            'apellidoPrimario' => 'Rodríguez',
            'apellidoSecundario' => 'López',
            'cedula' => '9-876-5432',
            'correo' => 'estudiante@test.com',
            'contrasena' => Hash::make('password123')
        ]);

        // Crear usuario para autenticación
        $user = User::create([
            'name' => 'María Elena Rodríguez',
            'email' => 'estudiante@test.com',
            'password' => Hash::make('password123')
        ]);

        // Crear curso de prueba
        $curso = Curso::create([
            'titulo' => 'PHP Básico para Principiantes',
            'descripcion' => 'Curso completo de PHP desde los fundamentos hasta conceptos avanzados.',
            'profesor_id' => $profesor->id
        ]);

        // Crear módulos
        $modulo1 = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Introducción a PHP',
            'orden' => 1
        ]);

        $modulo2 = Modulo::create([
            'curso_id' => $curso->id,
            'titulo' => 'Variables y Tipos de Datos',
            'orden' => 2
        ]);

        // Crear materiales para módulo 1
        Material::create([
            'modulo_id' => $modulo1->id,
            'titulo' => '¿Qué es PHP?',
            'tipo' => 'pdf',
            'url' => 'https://example.com/php-introduccion.pdf'
        ]);

        Material::create([
            'modulo_id' => $modulo1->id,
            'titulo' => 'Instalación de PHP',
            'tipo' => 'link',
            'url' => 'https://php.net/downloads.php'
        ]);

        // Crear materiales para módulo 2
        Material::create([
            'modulo_id' => $modulo2->id,
            'titulo' => 'Variables en PHP',
            'tipo' => 'pdf',
            'url' => 'https://example.com/variables-php.pdf'
        ]);

        $this->command->info('Datos de prueba creados:');
        $this->command->info("Profesor: {$profesor->correo} / password123");
        $this->command->info("Estudiante: {$estudiante->correo} / password123");
        $this->command->info("Curso: {$curso->titulo}");
    }
}
