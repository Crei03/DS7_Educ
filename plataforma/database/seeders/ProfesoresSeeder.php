<?php

namespace Database\Seeders;

use App\Domain\Curso\Models\Profesor;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProfesoresSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_PA');

        // Crear 10 profesores de prueba
        for ($i = 0; $i < 10; $i++) {
            Profesor::create([
                'nombrePrimario' => $faker->firstName,
                'nombreSecundario' => $faker->optional(0.3)->firstName,
                'apellidoPrimario' => $faker->lastName,
                'apellidoSecundario' => $faker->optional(0.7)->lastName,
                'cedula' => $this->generarCedulaPanameña($faker),
                'correo' => $faker->unique()->email . '.pa',
                'contrasena' => bcrypt('password123'),
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
