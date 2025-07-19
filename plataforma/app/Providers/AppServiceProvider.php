<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bindings para Actions de Evaluaciones
        $this->app->bind(\App\Domain\Evaluacion\Actions\CrearEvaluacionAction::class);
        $this->app->bind(\App\Domain\Evaluacion\Actions\ProcesarEvaluacionAction::class);
        $this->app->bind(\App\Domain\Evaluacion\Actions\ObtenerResultadosEvaluacionAction::class);

        // Bindings para Actions de Módulos y Materiales
        $this->app->bind(\App\Domain\ContenidoDigital\Actions\CrearModuloAction::class);
        $this->app->bind(\App\Domain\ContenidoDigital\Actions\ReordenarModulosAction::class);
        $this->app->bind(\App\Domain\ContenidoDigital\Actions\ObtenerContenidoModuloAction::class);
        $this->app->bind(\App\Domain\ContenidoDigital\Actions\CrearMaterialAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
