<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\ContenidoDigital\Models\Material;
use App\Domain\ContenidoDigital\Actions\CrearMaterialAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaterialController extends Controller
{
    public function __construct(
        private CrearMaterialAction $crearMaterialAction
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Material::with(['modulo.curso']);

        // Filtrar por módulo si se proporciona
        if ($request->has('modulo_id')) {
            $query->where('modulo_id', $request->modulo_id);
        }

        // Filtrar por tipo si se proporciona
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $materiales = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($materiales);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'modulo_id' => 'nullable|exists:modulos,id',
            'titulo' => 'required|string|max:120',
            'tipo' => 'required|in:pdf,link,zip,video,documento,enlace,presentacion,audio',
            'url' => 'required|string|max:500',
        ]);

        try {
            $material = $this->crearMaterialAction->execute($datos);
            $material->load(['modulo.curso']);

            return response()->json($material, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($id): JsonResponse
    {
        $material = Material::findOrFail($id);
        $material->load(['modulo.curso']);
        return response()->json($material);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $material = Material::findOrFail($id);

        $datos = $request->validate([
            'titulo' => 'sometimes|required|string|max:120',
            'tipo' => 'sometimes|required|in:pdf,link,zip,video,documento,enlace,presentacion,audio',
            'url' => 'sometimes|required|string|max:500',
            'modulo_id' => 'sometimes|nullable|exists:modulos,id',
        ]);

        $material->update($datos);
        $material->load(['modulo.curso']);

        return response()->json($material);
    }

    public function destroy($id): JsonResponse
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return response()->json(['message' => 'Material eliminado correctamente']);
    }

    /**
     * Marcar un material como visto por el estudiante autenticado
     */
    public function marcarVisto(Request $request, $materialId): JsonResponse
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
            $estudiante = \App\Domain\Estudiante\Models\Estudiante::where('correo', $user->email)->first();

            if (!$estudiante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estudiante no encontrado'
                ], 404);
            }

            // Verificar que el material existe
            $material = Material::findOrFail($materialId);

            // Verificar si ya está marcado como visto
            $yaVisto = \Illuminate\Support\Facades\DB::table('material_visto')
                ->where('estudiante_id', $estudiante->id)
                ->where('material_id', $materialId)
                ->exists();

            if (!$yaVisto) {
                // Marcar como visto
                \Illuminate\Support\Facades\DB::table('material_visto')->insert([
                    'estudiante_id' => $estudiante->id,
                    'material_id' => $materialId,
                    'visto_en' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Material marcado como visto',
                'visto_en' => now()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar material como visto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
