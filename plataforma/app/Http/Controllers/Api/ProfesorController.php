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
                'regex:/^(1[0-3]|[1-9])-\d{1,6}-\d{1,7}$/'
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
                'regex:/^(1[0-3]|[1-9])-\d{1,6}-\d{1,7}$/'
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

    /**
     * Obtener todos los materiales de los cursos del profesor
     */
    public function materiales(Profesor $profesor): JsonResponse
    {
        $materiales = $profesor->materiales()->with(['modulo.curso'])->get();
        return response()->json([
            'data' => [
                'profesor' => $profesor,
                'materiales' => $materiales
            ]
        ]);
    }

    /**
     * Obtener todas las evaluaciones de los cursos del profesor
     */
    public function evaluaciones(Profesor $profesor): JsonResponse
    {
        $evaluaciones = $profesor->evaluaciones()->with(['curso'])->get();
        return response()->json([
            'data' => [
                'profesor' => $profesor,
                'evaluaciones' => $evaluaciones
            ]
        ]);
    }

    /**
     * Obtener el progreso de los estudiantes matriculados en los cursos del profesor
     */
    public function progresoEstudiantes(Profesor $profesor): JsonResponse
    {
        $cursos = $profesor->cursos()->with(['estudiantes'])->get();
        $resultado = [];
        foreach ($cursos as $curso) {
            $estudiantes = $curso->estudiantes->map(function ($estudiante) use ($curso) {
                return [
                    'id' => $estudiante->id,
                    'nombre' => $estudiante->fullName(),
                    'correo' => $estudiante->correo,
                    'progreso' => $estudiante->pivot->progreso,
                    'fecha_matricula' => $estudiante->pivot->fecha,
                ];
            });
            $resultado[] = [
                'curso_id' => $curso->id,
                'curso_titulo' => $curso->titulo,
                'estudiantes' => $estudiantes,
            ];
        }
        return response()->json([
            'data' => [
                'profesor' => $profesor,
                'cursos' => $resultado
            ]
        ]);
    }

    /**
     * Obtener todos los módulos de los cursos del profesor
     */
    public function modulos(Profesor $profesor): JsonResponse
    {
        $modulos = \App\Domain\ContenidoDigital\Models\Modulo::whereHas('curso', function ($query) use ($profesor) {
            $query->where('profesor_id', $profesor->getKey());
        })->with(['curso:id,titulo'])->get();

        return response()->json([
            'data' => [
                'profesor' => $profesor,
                'modulos' => $modulos
            ]
        ]);
    }
}
