<?php

namespace Database\Seeders;

use App\Domain\ContenidoDigital\Models\Material;
use App\Domain\ContenidoDigital\Models\Modulo;
use Illuminate\Database\Seeder;

class MaterialesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener módulos existentes
        $modulos = Modulo::with('curso')->get();

        if ($modulos->isEmpty()) {
            $this->command->info('No hay módulos disponibles. Ejecuta ModulosSeeder primero.');
            return;
        }

        $tiposMateriales = ['pdf', 'link', 'zip'];
        $urlsEjemplo = [
            'pdf' => [
                'https://academia.edu.pa/materiales/guia1.pdf',
                'https://academia.edu.pa/materiales/guia2.pdf',
                'https://academia.edu.pa/materiales/manual.pdf',
            ],
            'link' => [
                'https://laravel.com/docs',
                'https://developer.mozilla.org/es/docs/Web',
                'https://academia.edu.pa/recursos/enlace1',
            ],
            'zip' => [
                'https://academia.edu.pa/materiales/ejercicios.zip',
                'https://academia.edu.pa/materiales/proyecto.zip',
                'https://academia.edu.pa/materiales/archivos.zip',
            ],
        ];

        foreach ($modulos as $modulo) {
            // Crear entre 3-6 materiales por módulo
            $cantidadMateriales = rand(3, 6);

            for ($i = 0; $i < $cantidadMateriales; $i++) {
                $tipo = $tiposMateriales[array_rand($tiposMateriales)];

                $urlIndex = $i % count($urlsEjemplo[$tipo]);

                Material::create([
                    'modulo_id' => $modulo->id,
                    'tipo' => $tipo,
                    'titulo' => ucfirst($tipo) . ' de ' . $modulo->titulo . ' #' . ($i + 1),
                    'url' => $urlsEjemplo[$tipo][$urlIndex],
                ]);
            }

            $this->command->info("✅ Creados {$cantidadMateriales} materiales para el módulo: {$modulo->titulo}");
        }

        $totalMateriales = Material::count();
        $this->command->info("🎯 Total de materiales creados: {$totalMateriales}");
    }
}
