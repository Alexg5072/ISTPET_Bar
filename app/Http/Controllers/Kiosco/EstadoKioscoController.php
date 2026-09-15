<?php namespace App\Http\Controllers\Kiosco;
use App\Http\Controllers\Controller;
use App\Models\{Categoria, Combo, Configuracion, Producto, Promocion};
use Illuminate\Http\Request;

/**
 * Endpoint muy liviano que el kiosco y el admin consultan cada pocos segundos
 * (polling) para saber si algo relevante cambió, SIN tener que descargar
 * todo el catálogo cada vez. Solo cuando la "huella" cambia, el frontend
 * dispara la recarga real de productos/promos/config.
 *
 * Cada llamada aquí es solo un MAX(updated_at) por tabla (5 consultas muy
 * baratas, todas sobre columnas indexadas), así que no hace falta cachear
 * el resultado: cachear introducía un riesgo de "ceguera" de unos segundos
 * justo cuando alguien hacía dos cambios seguidos muy rápido.
 */
class EstadoKioscoController extends Controller
{
    public function huella(Request $request)
    {
        $sedeId = $request->input('sede_id');

        $partes = [
            'productos'     => $this->maxUpdated(Producto::query(), $sedeId),
            'categorias'    => $this->maxUpdated(Categoria::query(), $sedeId),
            'combos'        => $this->maxUpdated(Combo::query(), $sedeId),
            'promociones'   => $this->maxUpdated(Promocion::query(), $sedeId),
            'configuracion' => Configuracion::max('updated_at'),
        ];

        // Un solo string que cambia si CUALQUIER cosa relevante cambió.
        $huella = md5(json_encode($partes));

        return response()->json([
            'huella' => $huella,
            'ts'     => now()->timestamp,
        ]);
    }

    private function maxUpdated($query, ?int $sedeId): ?string
    {
        if ($sedeId && $this->tieneSedeId($query)) {
            $query->where(function ($q) use ($sedeId) {
                $q->where('sede_id', $sedeId)->orWhereNull('sede_id');
            });
        }

        return $query->max('updated_at');
    }

    private function tieneSedeId($query): bool
    {
        return in_array('sede_id', $query->getModel()->getFillable());
    }
}