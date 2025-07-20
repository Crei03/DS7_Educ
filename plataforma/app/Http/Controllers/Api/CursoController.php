<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Curso\Models\Curso;
use App\Domain\Curso\Actions\CrearCursoAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CursoController extends Controller
{
    public function __construct(
        private CrearCursoAction $crearCursoAction
    ) {
    }

    public function index(): JsonResponse
    {
        $cursos = Curso::with(['profesor', 'estudiantes'])->paginate(15);
        return response()->json($cursos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'profesor_id' => 'required|exists:profesores,id',
        ]);

        $curso = $this->crearCursoAction->execute($datos);

        return response()->json($curso, 201);
    }

    public function show(Curso $curso): JsonResponse
    {
        $curso->load(['profesor', 'estudiantes', 'modulos']);
        return response()->json($curso);
    }

    public function update(Request $request, Curso $curso): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
            'profesor_id' => 'sometimes|required|exists:profesores,id',
        ]);

        $curso->update($datos);

        return response()->json($curso);
    }

    public function destroy(Curso $curso): JsonResponse
    {
        $curso->delete();
        return response()->json(['message' => 'Curso eliminado correctamente']);
    }

    public function estudiantes(Curso $curso): JsonResponse
    {
        $estudiantes = $curso->estudiantes()->withPivot(['fecha', 'progreso'])->get();
        return response()->json($estudiantes);
    }

    public function modulos(Curso $curso): JsonResponse
    {
        $modulos = $curso->modulos()->with('materiales')->get();
        return response()->json($modulos);
    }

    /**
     * Obtener cursos disponibles para matrícula
     * Retorna cursos que no están completamente llenos y están activos
     */
    public function disponibles(): JsonResponse
    {
        try {
            $cursos = Curso::with(['profesor'])
                ->whereHas('profesor') // Solo cursos que tengan profesor asignado
                ->get()
                ->map(function ($curso) {
                    return [
                        'id' => $curso->id,
                        'titulo' => $curso->titulo,
                        'descripcion' => $curso->descripcion,
                        'profesor' => [
                            'id' => $curso->profesor->id,
                            'nombre_completo' => trim($curso->profesor->nombrePrimario . ' ' . ($curso->profesor->nombreSecundario ?? '') . ' ' . $curso->profesor->apellidoPrimario . ' ' . ($curso->profesor->apellidoSecundario ?? ''))
                        ],
                        'estudiantes_count' => $curso->estudiantes()->count(),
                        'modulos_count' => $curso->modulos()->count(),
                        'creado_en' => $curso->creado_en,
                    ];
                });

            return response()->json([
                'success' => true,
                'cursos' => $cursos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cursos disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Matricular al estudiante autenticado en un curso
     */
    public function matricular(Request $request, Curso $curso): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Cargar la relación profesor
            $curso->load('profesor');

            // Buscar el estudiante asociado al usuario
            $estudiante = \App\Domain\Estudiante\Models\Estudiante::where('correo', $user->email ?? $user->correo)->first();

            // Si no existe estudiante pero el usuario es de tipo estudiante, crearlo
            if (!$estudiante && (isset($user->tipo) || $user instanceof \App\Domain\Estudiante\Models\Estudiante)) {
                if ($user instanceof \App\Models\User) {
                    // Crear estudiante a partir del usuario
                    $nombres = explode(' ', trim($user->name));
                    $estudiante = \App\Domain\Estudiante\Models\Estudiante::create([
                        'nombrePrimario' => $nombres[0] ?? '',
                        'nombreSecundario' => $nombres[1] ?? null,
                        'apellidoPrimario' => $nombres[2] ?? '',
                        'apellidoSecundario' => $nombres[3] ?? null,
                        'correo' => $user->email,
                        'contrasena' => $user->password, // Ya está hasheada
                        'fechaNacimiento' => now()->subYears(25),
                        'genero' => 'No especificado',
                        'telefono' => null,
                    ]);
                } else {
                    $estudiante = $user; // El usuario ya es un estudiante
                }
            }

            if (!$estudiante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo encontrar o crear el perfil de estudiante'
                ], 404);
            }

            // Verificar si ya está matriculado
            $yaMatriculado = $curso->estudiantes()->where('estudiante_id', $estudiante->id)->exists();

            if ($yaMatriculado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya estás matriculado en este curso'
                ], 409);
            }

            // Matricular al estudiante
            $curso->estudiantes()->attach($estudiante->id, [
                'fecha' => now(),
                'progreso' => 0
            ]);

            // Retornar información del curso matriculado
            $cursoMatriculado = [
                'id' => $curso->id,
                'titulo' => $curso->titulo,
                'descripcion' => $curso->descripcion,
                'profesor' => [
                    'nombre_completo' => trim($curso->profesor->nombrePrimario . ' ' . ($curso->profesor->nombreSecundario ?? '') . ' ' . $curso->profesor->apellidoPrimario . ' ' . ($curso->profesor->apellidoSecundario ?? ''))
                ],
                'progreso' => 0,
                'fecha_matricula' => now(),
                'modulos_count' => $curso->modulos()->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Matrícula realizada exitosamente',
                'curso' => $cursoMatriculado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la matrícula',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
