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
}
