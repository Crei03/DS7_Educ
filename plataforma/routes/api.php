<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EstudianteController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\ProfesorController;
use App\Http\Controllers\Api\EvaluacionController;
use App\Http\Controllers\Api\ModuloController;
use App\Http\Controllers\Api\MaterialController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas de la API para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro del grupo que
| contiene el middleware "api". ¡Disfruta construyendo tu API!
|
*/

// Rutas públicas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Perfil del usuario autenticado
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    // Cerrar sesión
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Rutas de Estudiantes
    Route::apiResource('estudiantes', EstudianteController::class);
    Route::post('estudiantes/{estudiante}/matricular', [EstudianteController::class, 'matricular']);
    Route::get('estudiantes/{estudiante}/cursos', [EstudianteController::class, 'cursosMatriculados']);

    // Rutas de Cursos
    Route::apiResource('cursos', CursoController::class);
    Route::get('cursos/{curso}/estudiantes', [CursoController::class, 'estudiantes']);
    Route::get('cursos/{curso}/modulos', [CursoController::class, 'modulos']);

    // Rutas de Profesores
    Route::apiResource('profesores', ProfesorController::class);
    Route::get('profesores/{profesor}/cursos', [ProfesorController::class, 'cursos']);
    Route::get('profesores/{profesor}/materiales', [ProfesorController::class, 'materiales']);
    Route::get('profesores/{profesor}/evaluaciones', [ProfesorController::class, 'evaluaciones']);
    Route::get('profesores/{profesor}/progreso-estudiantes', [ProfesorController::class, 'progresoEstudiantes']);
    Route::middleware('auth:sanctum')->put('/profesor/{id}', [ProfesorController::class, 'update']);

    // Rutas de Evaluaciones
    Route::apiResource('evaluaciones', EvaluacionController::class)->parameters([
        'evaluaciones' => 'evaluacion'
    ]);
    Route::post('evaluaciones/{evaluacion}/resolver', [EvaluacionController::class, 'resolverEvaluacion']);
    Route::get('evaluaciones/{evaluacion}/resultados', [EvaluacionController::class, 'resultados']);

    // Rutas de Módulos
    Route::apiResource('modulos', ModuloController::class);
    Route::post('modulos/reordenar', [ModuloController::class, 'reordenar']);

    // Rutas de Materiales
    Route::apiResource('materiales', MaterialController::class);
});
