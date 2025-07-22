<?php

namespace Database\Seeders;

use App\Domain\Estudiante\Models\Matricula;
use Illuminate\Database\Seeder;

class MatriculasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $matriculas = [
            ['estudiante_id' => 1, 'curso_id' => 1, 'fecha' => '2023-11-01', 'progreso' => 0.50],
            ['estudiante_id' => 2, 'curso_id' => 2, 'fecha' => '2023-11-02', 'progreso' => 0.75],
            ['estudiante_id' => 3, 'curso_id' => 3, 'fecha' => '2023-11-03', 'progreso' => 0.25],
            ['estudiante_id' => 4, 'curso_id' => 4, 'fecha' => '2023-11-04', 'progreso' => 1.00],
            ['estudiante_id' => 5, 'curso_id' => 5, 'fecha' => '2023-11-05', 'progreso' => 0.10],
            ['estudiante_id' => 6, 'curso_id' => 6, 'fecha' => '2023-11-06', 'progreso' => 0.90],
            ['estudiante_id' => 7, 'curso_id' => 7, 'fecha' => '2023-11-07', 'progreso' => 0.60],
            ['estudiante_id' => 8, 'curso_id' => 8, 'fecha' => '2023-11-08', 'progreso' => 0.45],
            ['estudiante_id' => 9, 'curso_id' => 9, 'fecha' => '2023-11-09', 'progreso' => 0.80],
            ['estudiante_id' => 10, 'curso_id' => 10, 'fecha' => '2023-11-10', 'progreso' => 0.30],
            ['estudiante_id' => 1, 'curso_id' => 3, 'fecha' => '2023-11-11', 'progreso' => 0.15],
            ['estudiante_id' => 2, 'curso_id' => 1, 'fecha' => '2023-11-12', 'progreso' => 0.95],
            ['estudiante_id' => 11, 'curso_id' => 11, 'fecha' => '2023-11-13', 'progreso' => 0.55],
            ['estudiante_id' => 12, 'curso_id' => 12, 'fecha' => '2023-11-14', 'progreso' => 0.70],
            ['estudiante_id' => 13, 'curso_id' => 13, 'fecha' => '2023-11-15', 'progreso' => 0.20],
            ['estudiante_id' => 14, 'curso_id' => 14, 'fecha' => '2023-11-16', 'progreso' => 0.85],
            ['estudiante_id' => 15, 'curso_id' => 15, 'fecha' => '2023-11-17', 'progreso' => 0.40],
            ['estudiante_id' => 16, 'curso_id' => 16, 'fecha' => '2023-11-18', 'progreso' => 0.65],
            ['estudiante_id' => 17, 'curso_id' => 1, 'fecha' => '2023-11-19', 'progreso' => 0.05],
            ['estudiante_id' => 18, 'curso_id' => 2, 'fecha' => '2023-11-20', 'progreso' => 1.00],
        ];

        foreach ($matriculas as $matricula) {
            Matricula::create($matricula);
        }
    }
}
