<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Curso\Models\Profesor;
use App\Domain\Curso\Actions\CrearProfesorAction;
use App\Domain\Curso\Actions\ActualizarProfesorAction;
use App\Domain\Curso\Actions\EliminarProfesorAction;
use App\Domain\Curso\Actions\ObtenerProfesoresAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;

class ProfesorController extends Controller
{
    public function __construct(
        private CrearProfesorAction $crearProfesorAction,
        private ActualizarProfesorAction $actualizarProfesorAction,
        private EliminarProfesorAction $eliminarProfesorAction,
        private ObtenerProfesoresAction $obtenerProfesoresAction
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['buscar']);
        $profesores = $this->obtenerProfesoresAction->execute($filtros);

        return response()->json($profesores);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombrePrimario' => 'required|string|max:60',
            'nombreSecundario' => 'nullable|string|max:60',
            'apellidoPrimario' => 'required|string|max:60',
            'apellidoSecundario' => 'nullable|string|max:60',
            'cedula' => [
                'required',
                'string',
                'max:20',
                'unique:profesores,cedula',
                'regex:/^\d{1,2}-\d{4,5}-\d{5,6}$/'
            ],
            'correo' => 'required|email|max:120|unique:profesores,correo',
            'contrasena' => ['required', Password::min(8)->mixedCase()->numbers()],
        ]);

        try {
            $profesor = $this->crearProfesorAction->execute($datos);

            return response()->json([
                'message' => 'Profesor creado exitosamente',
                'data' => $profesor
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el profesor',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Profesor $profesor): JsonResponse
    {
        $profesor->load('cursos');
        $profesor->loadCount('cursos');

        return response()->json([
            'data' => $profesor
        ]);
    }

    public function update(Request $request, Profesor $profesor): JsonResponse
    {
        $datos = $request->validate([
            'nombrePrimario' => 'required|string|max:60',
            'nombreSecundario' => 'nullable|string|max:60',
            'apellidoPrimario' => 'required|string|max:60',
            'apellidoSecundario' => 'nullable|string|max:60',
            'cedula' => [
                'required',
                'string',
                'max:20',
                'unique:profesores,cedula,' . $profesor->id,
                'regex:/^\d{1,2}-\d{4,5}-\d{5,6}$/'
            ],
            'correo' => 'required|email|max:120|unique:profesores,correo,' . $profesor->id,
            'contrasena' => ['nullable', Password::min(8)->mixedCase()->numbers()],
        ]);

        try {
            $profesorActualizado = $this->actualizarProfesorAction->execute($profesor, $datos);

            return response()->json([
                'message' => 'Profesor actualizado exitosamente',
                'data' => $profesorActualizado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el profesor',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(Profesor $profesor): JsonResponse
    {
        try {
            $this->eliminarProfesorAction->execute($profesor);

            return response()->json([
                'message' => 'Profesor eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el profesor',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function cursos(Profesor $profesor): JsonResponse
    {
        $cursos = $profesor->cursos()
            ->with(['estudiantes'])
            ->withCount(['estudiantes', 'modulos', 'evaluaciones'])
            ->get();

        return response()->json([
            'data' => [
                'profesor' => $profesor,
                'cursos' => $cursos
            ]
        ]);
    }
}
