<?php

namespace Database\Seeders;

use App\Domain\Evaluacion\Models\Resultado;
use Illuminate\Database\Seeder;

class ResultadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resultados = [
            ['id' => 1, 'evaluacion_id' => 1, 'estudiante_id' => 1, 'puntaje' => 85.50, 'fecha' => '2023-12-01 10:00:00'],
            ['id' => 2, 'evaluacion_id' => 2, 'estudiante_id' => 2, 'puntaje' => 92.00, 'fecha' => '2023-12-02 11:30:00'],
            ['id' => 3, 'evaluacion_id' => 3, 'estudiante_id' => 3, 'puntaje' => 76.00, 'fecha' => '2023-12-03 09:45:00'],
            ['id' => 4, 'evaluacion_id' => 4, 'estudiante_id' => 4, 'puntaje' => 98.75, 'fecha' => '2023-12-04 14:00:00'],
            ['id' => 5, 'evaluacion_id' => 5, 'estudiante_id' => 5, 'puntaje' => 65.00, 'fecha' => '2023-12-05 16:00:00'],
            ['id' => 6, 'evaluacion_id' => 1, 'estudiante_id' => 2, 'puntaje' => 95.00, 'fecha' => '2023-12-01 10:05:00'],
            ['id' => 7, 'evaluacion_id' => 2, 'estudiante_id' => 18, 'puntaje' => 100.00, 'fecha' => '2023-12-02 11:35:00'],
            ['id' => 8, 'evaluacion_id' => 3, 'estudiante_id' => 1, 'puntaje' => 88.50, 'fecha' => '2023-12-03 09:50:00'],
            ['id' => 9, 'evaluacion_id' => 6, 'estudiante_id' => 6, 'puntaje' => 91.20, 'fecha' => '2023-12-06 13:00:00'],
            ['id' => 10, 'evaluacion_id' => 7, 'estudiante_id' => 7, 'puntaje' => 80.00, 'fecha' => '2023-12-07 15:20:00'],
            ['id' => 11, 'evaluacion_id' => 8, 'estudiante_id' => 8, 'puntaje' => 79.50, 'fecha' => '2023-12-08 11:00:00'],
            ['id' => 12, 'evaluacion_id' => 9, 'estudiante_id' => 9, 'puntaje' => 94.00, 'fecha' => '2023-12-09 10:30:00'],
            ['id' => 13, 'evaluacion_id' => 10, 'estudiante_id' => 10, 'puntaje' => 72.80, 'fecha' => '2023-12-10 12:00:00'],
            ['id' => 14, 'evaluacion_id' => 11, 'estudiante_id' => 1, 'puntaje' => 90.00, 'fecha' => '2023-12-11 14:10:00'],
            ['id' => 15, 'evaluacion_id' => 12, 'estudiante_id' => 2, 'puntaje' => 88.00, 'fecha' => '2023-12-12 13:45:00'],
            ['id' => 16, 'evaluacion_id' => 13, 'estudiante_id' => 3, 'puntaje' => 75.50, 'fecha' => '2023-12-13 10:00:00'],
            ['id' => 17, 'evaluacion_id' => 14, 'estudiante_id' => 6, 'puntaje' => 93.00, 'fecha' => '2023-12-14 11:00:00'],
            ['id' => 18, 'evaluacion_id' => 18, 'estudiante_id' => 17, 'puntaje' => 89.00, 'fecha' => '2023-12-15 09:00:00'],
            ['id' => 19, 'evaluacion_id' => 19, 'estudiante_id' => 17, 'puntaje' => 96.50, 'fecha' => '2023-12-16 10:30:00'],
            ['id' => 20, 'evaluacion_id' => 20, 'estudiante_id' => 17, 'puntaje' => 91.00, 'fecha' => '2023-12-17 11:45:00'],
        ];

        foreach ($resultados as $resultado) {
            Resultado::create($resultado);
        }
    }
}
