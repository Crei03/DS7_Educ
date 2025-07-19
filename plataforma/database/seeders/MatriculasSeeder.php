<?php

namespace Database\Seeders;

use App\Domain\Curso\Models\Curso;
use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatriculasSeeder extends Seeder
{
    public function run(): void
    {
        $estudiantes = Estudiante::all();
        $cursos = Curso::all();

        // Cada estudiante se matricula en 2-5 cursos aleatorios
        foreach ($estudiantes as $estudiante) {
            $cursosAleatorios = $cursos->random(rand(2, 5));

            foreach ($cursosAleatorios as $curso) {
                // Evitar duplicados
                if (
                    !DB::table('matriculas')
                        ->where('estudiante_id', $estudiante->id)
                        ->where('curso_id', $curso->id)
                        ->exists()
                ) {

                    DB::table('matriculas')->insert([
                        'estudiante_id' => $estudiante->id,
                        'curso_id' => $curso->id,
                        'fecha' => now()->subDays(rand(1, 90)),
                        'progreso' => rand(0, 100),
                    ]);
                }
            }
        }
    }
}
