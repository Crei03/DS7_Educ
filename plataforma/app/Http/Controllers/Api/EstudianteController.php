<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Estudiante\Models\Estudiante;
use App\Domain\Estudiante\Actions\CrearEstudianteAction;
use App\Domain\Estudiante\Actions\MatricularEstudianteAction;
use App\Http\Requests\CrearEstudianteRequest;
use App\Http\Requests\ActualizarEstudianteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstudianteController extends Controller
{
    public function __construct(
        private CrearEstudianteAction $crearEstudianteAction,
        private MatricularEstudianteAction $matricularEstudianteAction
    ) {
    }

    public function index(): JsonResponse
    {
        $estudiantes = Estudiante::with('cursos')->paginate(15);
        return response()->json($estudiantes);
    }

    public function store(CrearEstudianteRequest $request): JsonResponse
    {
        $estudiante = $this->crearEstudianteAction->execute($request->validated());

        return response()->json($estudiante, 201);
    }

    public function show(Estudiante $estudiante): JsonResponse
    {
        $estudiante->load('cursos');
        return response()->json($estudiante);
    }

    public function update(ActualizarEstudianteRequest $request, Estudiante $estudiante): JsonResponse
    {
        $estudiante->update($request->validated());

        return response()->json($estudiante);
    }

    public function destroy(Estudiante $estudiante): JsonResponse
    {
        $estudiante->delete();
        return response()->json(['message' => 'Estudiante eliminado correctamente']);
    }

    public function matricular(Request $request, Estudiante $estudiante): JsonResponse
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ]);

        $this->matricularEstudianteAction->execute($estudiante->id, $request->curso_id);

        return response()->json(['message' => 'Estudiante matriculado correctamente']);
    }

    public function cursosMatriculados(Estudiante $estudiante): JsonResponse
    {
        // Debug info
        \Log::info('Cargando cursos para estudiante:', [
            'estudiante_id' => $estudiante->id,
            'nombre' => $estudiante->nombrePrimario . ' ' . $estudiante->apellidoPrimario
        ]);

        $cursos = $estudiante->cursos()
            ->withPivot(['fecha', 'progreso'])
            ->with([
                'profesor:id,nombrePrimario,nombreSecundario,apellidoPrimario,apellidoSecundario',
                'modulos' => function ($query) {
                    $query->select('id', 'curso_id', 'titulo');
                },
                'evaluaciones' => function ($query) {
                    $query->select('id', 'curso_id', 'titulo', 'duracion_min');
                }
            ])
            ->get()
            ->map(function ($curso) {
                // Agregar nombre completo del profesor
                if ($curso->profesor) {
                    $curso->profesor->nombreCompleto = trim(
                        $curso->profesor->nombrePrimario . ' ' .
                        ($curso->profesor->nombreSecundario ?? '') . ' ' .
                        $curso->profesor->apellidoPrimario . ' ' .
                        ($curso->profesor->apellidoSecundario ?? '')
                    );
                }

                // Agregar contadores
                $curso->modulos_count = $curso->modulos->count();
                $curso->evaluaciones_count = $curso->evaluaciones->count();

                return $curso;
            });

        \Log::info('Cursos encontrados:', ['count' => $cursos->count()]);

        return response()->json(['cursos' => $cursos]);
    }

    /**
     * Obtener progreso detallado de un curso para el estudiante autenticado
     */
    public function progresoDetallado(Request $request, $cursoId): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Buscar el estudiante asociado al usuario
            $estudiante = Estudiante::where('correo', $user->email)->first();

            if (!$estudiante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estudiante no encontrado'
                ], 404);
            }

            // Verificar que el estudiante está matriculado en el curso
            $curso = \App\Domain\Curso\Models\Curso::with(['profesor', 'modulos.materiales'])
                ->whereHas('estudiantes', function ($query) use ($estudiante) {
                    $query->where('estudiante_id', $estudiante->id);
                })
                ->findOrFail($cursoId);

            // Obtener el progreso de la matrícula
            $matricula = $curso->estudiantes()->where('estudiante_id', $estudiante->id)->first();
            $progresoGeneral = $matricula ? $matricula->pivot->progreso : 0;

            // Obtener materiales vistos por el estudiante
            $materialesVistos = \Illuminate\Support\Facades\DB::table('material_visto')
                ->where('estudiante_id', $estudiante->id)
                ->pluck('material_id')
                ->toArray();

            // Mapear módulos con progreso detallado
            $modulos = $curso->modulos->map(function ($modulo) use ($materialesVistos) {
                $materiales = $modulo->materiales->map(function ($material) use ($materialesVistos) {
                    return [
                        'id' => $material->id,
                        'titulo' => $material->titulo,
                        'tipo' => $material->tipo,
                        'url' => $material->url,
                        'visto' => in_array($material->id, $materialesVistos)
                    ];
                });

                // Calcular progreso del módulo
                $totalMateriales = $materiales->count();
                $materialesCompletados = $materiales->filter(function ($material) {
                    return $material['visto'];
                })->count();

                $progresoModulo = $totalMateriales > 0
                    ? round(($materialesCompletados / $totalMateriales) * 100)
                    : 0;

                return [
                    'id' => $modulo->id,
                    'titulo' => $modulo->titulo,
                    'orden' => $modulo->orden,
                    'progreso' => $progresoModulo,
                    'materiales' => $materiales
                ];
            });

            // Construir respuesta
            $respuesta = [
                'id' => $curso->id,
                'titulo' => $curso->titulo,
                'descripcion' => $curso->descripcion,
                'progreso' => $progresoGeneral,
                'profesor' => [
                    'nombre_completo' => trim($curso->profesor->nombrePrimario . ' ' . ($curso->profesor->nombreSecundario ?? '') . ' ' . $curso->profesor->apellidoPrimario . ' ' . ($curso->profesor->apellidoSecundario ?? ''))
                ],
                'modulos' => $modulos,
                'ultima_actividad' => $matricula ? $matricula->pivot->fecha : null
            ];

            return response()->json($respuesta);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Curso no encontrado o no estás matriculado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener progreso detallado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
