<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\StockMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'sede'])->latest();

        if ($request->filled('categoria'))  $query->where('categoria_id', $request->categoria);
        if ($request->filled('stock'))      $query->where('stock_activo', $request->stock === 'true');
        if ($request->filled('buscar'))     $query->where('nombre', 'like', '%'.$request->buscar.'%');

        $productos  = $query->paginate(20)->withQueryString();
        $categorias = Categoria::activas()->get();
        $todosLosProductos = Producto::select('id', 'nombre')->get();

        return view('admin.productos.index', compact('productos', 'categorias', 'todosLosProductos'));
    }

    public function create()
    {
        $categorias = Categoria::activas()->get();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        try {

            Log::info('=== PRODUCTO STORE INICIO ===', [
                'input' => $request->except(['imagen']),
                'tiene_imagen' => $request->hasFile('imagen'),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            $data = $request->validate([
                'nombre'       => 'required|string|min:2|max:100|unique:productos,nombre',
                'categoria_id' => 'required|exists:categorias,id',
                'descripcion'  => 'nullable|string|max:500',
                'precio'       => 'required|numeric|min:0.01|max:999.99',
                'stock_actual' => 'required|integer|min:0|max:99999',
                'stock_minimo' => 'required|integer|min:0|max:99999',
                'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            ], [
                'nombre.required'       => 'El nombre del producto es obligatorio.',
                'nombre.unique'         => 'Ya existe un producto con ese nombre.',
                'nombre.min'            => 'El nombre debe tener al menos 2 caracteres.',
                'categoria_id.required' => 'Debes seleccionar una categoría.',
                'precio.required'       => 'El precio es obligatorio.',
                'precio.min'            => 'El precio debe ser de al menos $0.01.',
                'precio.max'            => 'El precio no puede exceder $999.99.',
                'stock_actual.integer'  => 'El stock actual debe ser un número entero.',
                'stock_actual.min'      => 'El stock actual no puede ser negativo.',
                'stock_minimo.integer'  => 'El stock mínimo debe ser un número entero.',
                'stock_minimo.min'      => 'El stock mínimo no puede ser negativo.',
            ]);

            Log::info('=== VALIDACION STORE OK ===', $data);

            $baseSlug = Str::slug($data['nombre']);
            $slug = $baseSlug;
            $i = 1;

            while (Producto::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $data['slug'] = $slug;
            $data['stock_activo'] = $data['stock_actual'] > 0;

            Log::info('=== SLUG GENERADO ===', [
                'slug' => $slug
            ]);

            if ($request->hasFile('imagen')) {

                Log::info('=== IMAGEN DETECTADA ===', [
                    'nombre_original' => $request->file('imagen')->getClientOriginalName(),
                    'mime' => $request->file('imagen')->getMimeType(),
                    'size' => $request->file('imagen')->getSize(),
                ]);

                $data['imagen_path'] = $this->guardarImagen(
                    $request->file('imagen'),
                    $data['slug']
                );

                Log::info('=== IMAGEN GUARDADA ===', [
                    'ruta' => $data['imagen_path']
                ]);
            }

            Log::info('=== DATOS FINALES CREATE ===', $data);

            $producto = Producto::create($data);

            Log::info('=== PRODUCTO CREADO ===', [
                'id' => $producto->id,
                'nombre' => $producto->nombre
            ]);

            if ($producto->stock_actual > 0) {

                StockMovimiento::create([
                    'producto_id'   => $producto->id,
                    'user_id'       => Auth::id(),
                    'tipo'          => 'entrada',
                    'cantidad'      => $producto->stock_actual,
                    'stock_antes'   => 0,
                    'stock_despues' => $producto->stock_actual,
                    'motivo'        => 'Stock inicial al crear el producto',
                ]);

                Log::info('=== STOCK INICIAL CREADO ===');
            }

            return redirect()
                ->route('admin.productos.index')
                ->with('success', "Producto '{$producto->nombre}' creado correctamente.");

        } catch (\Throwable $e) {

            Log::error('=== ERROR STORE PRODUCTO ===', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::activas()->get();
        $todosLosProductos = Producto::select('id', 'nombre')->get();
        return view('admin.productos.edit', compact('producto', 'categorias', 'todosLosProductos'));
    }

    public function update(Request $request, Producto $producto)
    {
        try {

            Log::info('=== PRODUCTO UPDATE INICIO ===', [
                'id' => $producto->id,
                'input' => $request->except(['imagen']),
                'tiene_imagen' => $request->hasFile('imagen'),
            ]);

            $data = $request->validate([
                'nombre'       => 'required|string|min:2|max:100|unique:productos,nombre,' . $producto->id,
                'categoria_id' => 'required|exists:categorias,id',
                'descripcion'  => 'nullable|string|max:500',
                'precio'       => 'required|numeric|min:0.01|max:999.99',
                'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            ], [
                'nombre.required'       => 'El nombre del producto es obligatorio.',
                'nombre.unique'         => 'Ya existe un producto con ese nombre.',
                'nombre.min'            => 'El nombre debe tener al menos 2 caracteres.',
                'categoria_id.required' => 'Debes seleccionar una categoría.',
                'precio.required'       => 'El precio es obligatorio.',
                'precio.min'            => 'El precio debe ser de al menos $0.01.',
                'precio.max'            => 'El precio no puede exceder $999.99.',
            ]);

            Log::info('=== UPDATE VALIDACION OK ===', $data);

            $antes = $producto->toArray();

            if ($request->hasFile('imagen')) {

                Log::info('=== UPDATE IMAGEN DETECTADA ===');

                if ($producto->imagen_path) {

                    Log::info('=== ELIMINANDO IMAGEN ANTERIOR ===', [
                        'ruta' => $producto->imagen_path
                    ]);

                    Storage::disk('public')->delete(
                        $producto->imagen_path
                    );
                }

                $data['imagen_path'] = $this->guardarImagen(
                    $request->file('imagen'),
                    Str::slug($data['nombre'])
                );

                Log::info('=== NUEVA IMAGEN GUARDADA ===', [
                    'ruta' => $data['imagen_path']
                ]);
            }

            Log::info('=== DATOS UPDATE ===', $data);

            $producto->update($data);

            Log::info('=== UPDATE COMPLETADO ===', [
                'id' => $producto->id
            ]);

            return redirect()
                ->route('admin.productos.index')
                ->with('success', "Producto '{$producto->nombre}' actualizado.");

        } catch (\Throwable $e) {

            Log::error('=== ERROR UPDATE PRODUCTO ===', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen_path) {
            Storage::disk('public')->delete($producto->imagen_path);
        }
        $nombre = $producto->nombre;
        $producto->delete();

        AuditLog::registrar('Producto eliminado', Producto::class, $producto->id);

        return redirect()->route('admin.productos.index')
            ->with('success', "Producto '{$nombre}' eliminado.");
    }

    public function toggleStock(Producto $producto)
    {
        $producto->update(['stock_activo' => !$producto->stock_activo]);

        $estado = $producto->stock_activo ? 'activado' : 'desactivado';
        AuditLog::registrar("Stock {$estado} — {$producto->nombre}", Producto::class, $producto->id);

        return back()->with('success', "Stock de '{$producto->nombre}' {$estado}.");
    }

    private function guardarImagen(UploadedFile $file, string $slug): string
    {
        Log::info('=== GUARDAR IMAGEN INICIO ===');

        $filename = $slug . '-' . time() . '.' . $file->getClientOriginalExtension();

        Log::info('=== ARCHIVO GENERADO ===', [
            'filename' => $filename
        ]);

        $path = $file->storeAs(
            'productos',
            $filename,
            'public'
        );

        Log::info('=== STORE AS OK ===', [
            'path' => $path
        ]);

        return $path;
    }
}