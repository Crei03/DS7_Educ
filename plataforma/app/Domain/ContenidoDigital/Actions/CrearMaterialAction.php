<?php

namespace App\Domain\ContenidoDigital\Actions;

use App\Domain\ContenidoDigital\Models\Material;

class CrearMaterialAction
{
    public function execute(array $datos): Material
    {
        // Validar tipo de material
        $this->validarTipoMaterial($datos['tipo']);

        return Material::create([
            'modulo_id' => $datos['modulo_id'],
            'tipo' => $datos['tipo'],
            'titulo' => $datos['titulo'],
            'url' => $datos['url'],
        ]);
    }

    private function validarTipoMaterial(string $tipo): void
    {
        $tiposValidos = ['pdf', 'link', 'zip'];

        if (!in_array($tipo, $tiposValidos)) {
            throw new \InvalidArgumentException("Tipo de material no válido: {$tipo}");
        }
    }
}
