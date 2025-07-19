<?php

namespace Database\Seeders;

use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EstudiantesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_PA');

        // Crear 100 estudiantes de prueba
        for ($i = 0; $i < 100; $i++) {
            Estudiante::create([
                'nombrePrimario' => $faker->firstName,
                'nombreSecundario' => $faker->optional(0.4)->firstName,
                'apellidoPrimario' => $faker->lastName,
                'apellidoSecundario' => $faker->optional(0.8)->lastName,
                'cedula' => $this->generarCedulaPanameña($faker),
                'correo' => $faker->unique()->userName . '@estudiante.edu.pa',
                'contrasena' => bcrypt('estudiante123'),
            ]);
        }
    }

    private function generarCedulaPanameña($faker): string
    {
        $provincia = $faker->numberBetween(1, 12);
        $tomo = $faker->numberBetween(1000, 9999);
        $folio = $faker->numberBetween(10000, 999999);

        return "{$provincia}-{$tomo}-{$folio}";
    }
}
