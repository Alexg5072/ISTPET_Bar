<?php namespace App\Http\Controllers\Kiosco;
use App\Http\Controllers\Controller;
use App\Models\{Categoria, Combo, Producto, Promocion, Sede};
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function inicio()
    {
        // Limpiar carrito y sede al volver al inicio
        session()->forget(['kiosco_carrito', 'kiosco_sede', 'kiosco_sede_id']);
        $sedes = Sede::activos()->get();
        return view('kiosco.inicio', compact('sedes'));
    }

    public function menu(Request $request)
    {
        $sedeSlug = $request->input('sede', session('kiosco_sede', 'instituto'));
        $sede     = Sede::where('slug', $sedeSlug)->where('activo', true)->firstOrFail();

        // Guardar sede en sesión
        session(['kiosco_sede' => $sede->slug, 'kiosco_sede_id' => $sede->id]);

        $categorias = Categoria::activas()->deSede($sede->id)->get();

        return view('kiosco.menu', compact('sede', 'categorias'));
    }

    /**
     * API — devuelve productos de una sede/categoría para JS
     */
    public function apiProductos(Request $request)
    {
        $sedeId      = $request->input('sede_id');
        $categoriaId = $request->input('categoria_id');

        $query = Producto::with('categoria')
            ->where('activo', true)
            ->deSede($sedeId);

        if ($categoriaId && $categoriaId !== 'todos') {
            $query->where('categoria_id', $categoriaId);
        }

        $productos = $query->get()->map(fn($p) => [
            'id'          => $p->id,
            'nombre'      => $p->nombre,
            'descripcion' => $p->descripcion,
            'precio'      => $p->precio,
            'imagen_url'  => $p->imagen_url,
            'disponible'  => $p->disponible,
            'es_combo'    => $p->es_combo,
            'categoria'   => $p->categoria?->nombre,
            'stock_max'   => $p->stock_actual,
        ]);

        // También incluir combos si se pide todos o categoría 'combos'
        $combos = collect();
        if (!$categoriaId || $categoriaId === 'todos' || $categoriaId === 'combos') {
            $combos = Combo::with('items.producto')->activos()->deSede($sedeId)->get()->map(fn($c) => [
                'id'          => 'combo-'.$c->id,
                'nombre'      => $c->nombre,
                'descripcion' => $c->descripcion,
                'precio'      => $c->precio,
                'imagen_url'  => $c->imagen_url,
                'disponible'  => $c->disponible,
                'es_combo'    => true,
            ]);
        }

        $resultado = $categoriaId === 'combos'
            ? $combos->values()
            : $productos->merge($combos)->values();

        return response()->json([
            'productos' => $resultado,
            'sede'      => $sedeId,
        ]);
    }

    /**
     * API — devuelve promociones activas para el modo reposo
     */
    public function apiPromociones(Request $request)
    {
        $sedeId = $request->input('sede_id');

        $promociones = Promocion::activas()
            ->deSede($sedeId)
            ->with(['producto1','producto2','producto3'])
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'titulo'           => $p->titulo,
                'descripcion'      => $p->descripcion,
                'imagen_url'       => $p->imagen_url,
                'duracion'         => $p->duracion_segundos,
                'precio_destacado' => $p->precio_destacado,
                'productos'        => array_values(array_filter([
                    $p->producto_id_1 ? ['id' => $p->producto_id_1, 'nombre' => optional($p->producto1)->nombre, 'precio' => optional($p->producto1)->precio, 'disponible' => optional($p->producto1)->disponible ?? false] : null,
                    $p->producto_id_2 ? ['id' => $p->producto_id_2, 'nombre' => optional($p->producto2)->nombre, 'precio' => optional($p->producto2)->precio, 'disponible' => optional($p->producto2)->disponible ?? false] : null,
                    $p->producto_id_3 ? ['id' => $p->producto_id_3, 'nombre' => optional($p->producto3)->nombre, 'precio' => optional($p->producto3)->precio, 'disponible' => optional($p->producto3)->disponible ?? false] : null,
                ])),
            ]);

        return response()->json(['promociones' => $promociones]);
    }
}