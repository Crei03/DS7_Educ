<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupAcademiaCommand extends Command
{
    protected $signature = 'academia:setup 
                          {--fresh : Ejecutar migraciones desde cero}
                          {--seed : Ejecutar seeders}';

    protected $description = 'Configurar la aplicación Academia con migraciones y datos de prueba';

    public function handle(): int
    {
        $this->info('🎓 Configurando Academia Digital...');

        try {
            // Ejecutar migraciones
            if ($this->option('fresh')) {
                $this->warn('⚠️  Ejecutando migraciones desde cero (se perderán los datos existentes)');
                if ($this->confirm('¿Está seguro de continuar?')) {
                    Artisan::call('migrate:fresh');
                    $this->info('✅ Migraciones ejecutadas desde cero');
                } else {
                    $this->info('Operación cancelada');
                    return 0;
                }
            } else {
                Artisan::call('migrate');
                $this->info('✅ Migraciones ejecutadas');
            }

            // Ejecutar seeders si se solicita
            if ($this->option('seed')) {
                $this->info('📊 Ejecutando seeders...');

                $seeders = [
                    'ProfesoresSeeder',
                    'EstudiantesSeeder',
                    'CursosSeeder',
                    'MatriculasSeeder'
                ];

                foreach ($seeders as $seeder) {
                    $this->line("   Ejecutando {$seeder}...");
                    Artisan::call('db:seed', ['--class' => $seeder]);
                }

                $this->info('✅ Seeders ejecutados correctamente');
            }

            // Crear token de Sanctum
            Artisan::call('vendor:publish', ['--provider' => 'Laravel\Sanctum\SanctumServiceProvider']);

            $this->info('🚀 Academia Digital configurada correctamente');
            $this->newLine();
            $this->line('Accesos de prueba:');
            $this->line('- Admin: admin@academia.edu.pa / password');
            $this->line('- Estudiante: cualquier correo del seeder / estudiante123');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la configuración: ' . $e->getMessage());
            return 1;
        }
    }
}
