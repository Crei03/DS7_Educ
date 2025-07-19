<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden lógico
        $this->call([
            ProfesoresSeeder::class,
            EstudiantesSeeder::class,
            CursosSeeder::class,
            MatriculasSeeder::class,
            EvaluacionesSeeder::class,
            ResultadosSeeder::class,
            ModulosSeeder::class,
            MaterialesSeeder::class,
        ]);

        // Usuario administrador de prueba
        User::factory()->create([
            'name' => 'Admin Academia',
            'email' => 'admin@academia.edu.pa',
        ]);
    }
}
