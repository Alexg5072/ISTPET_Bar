<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Promocion, AuditLog, Sede, Producto};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::with(['sede','producto1','producto2','producto3'])->orderBy('orden')->get();
        $sedes       = Sede::activos()->get();
        $productos   = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('admin.promociones.index', compact('promociones', 'sedes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'titulo' => preg_replace('/\s+/', ' ', trim((string) $request->titulo)),
            'precio_destacado' => $request->filled('precio_destacado') ? ltrim(trim((string) $request->precio_destacado), '$') : null,
        ]);

        $data = $request->validate([
            'titulo'            => 'required|string|min:3|max:100',
            'descripcion'       => 'nullable|string|max:500',
            'precio_destacado'  => 'nullable|numeric|min:0.01|max:999.99',
            'orden'             => 'nullable|integer|min:0|max:999',
            'duracion_segundos' => 'nullable|integer|min:2|max:300',
            'imagen'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'sede_id'           => 'nullable|exists:sedes,id',
            'producto_id_1'     => 'nullable|exists:productos,id',
            'producto_id_2'     => 'nullable|exists:productos,id',
            'producto_id_3'     => 'nullable|exists:productos,id',
        ], [
            'titulo.required'          => 'El título de la promoción es obligatorio.',
            'titulo.min'               => 'El título debe tener al menos 3 caracteres.',
            'duracion_segundos.min'    => 'La duración debe ser de al menos 2 segundos.',
            'duracion_segundos.max'    => 'La duración no puede superar los 300 segundos (5 minutos).',
            'precio_destacado.numeric' => 'El precio destacado debe ser un valor numérico.',
            'precio_destacado.min'     => 'El precio destacado debe ser mayor a 0.',
        ]);

        // Verificar que no se repitan los mismos productos seleccionados
        $prods = array_filter([$data['producto_id_1'] ?? null, $data['producto_id_2'] ?? null, $data['producto_id_3'] ?? null]);
        if (count($prods) !== count(array_unique($prods))) {
            return back()->withErrors(['producto_id_2' => 'No puedes seleccionar el mismo producto más de una vez en la misma promoción.'])->withInput();
        }

        if ($request->hasFile('imagen')) {
            $filename = Str::slug($data['titulo']) . '-' . time() . '.' . $request->imagen->extension();
            $request->imagen->storeAs('promociones', $filename, 'public');
            $data['imagen_path'] = 'promociones/' . $filename;
        }

        $data['activo'] = true;

        foreach (['producto_id_1','producto_id_2','producto_id_3','sede_id'] as $f) {
            if (isset($data[$f]) && $data[$f] === '') $data[$f] = null;
        }

        $promo = Promocion::create($data);
        AuditLog::registrar('Promoción creada — ' . $promo->titulo, Promocion::class, $promo->id);

        return back()->with('success', 'Promoción "' . $promo->titulo . '" creada con éxito.');
    }

    public function update(Request $request, Promocion $promocion)
    {
        Log::info('=== UPDATE PROMOCION ===', [
            'id'     => $promocion->id,
            'titulo' => $promocion->titulo,
            'input'  => $request->except(['_token','_method','imagen']),
        ]);

        $request->merge([
            'titulo' => preg_replace('/\s+/', ' ', trim((string) $request->titulo)),
            'precio_destacado' => $request->filled('precio_destacado') ? ltrim(trim((string) $request->precio_destacado), '$') : null,
        ]);

        $data = $request->validate([
            'titulo'            => 'required|string|min:3|max:100',
            'descripcion'       => 'nullable|string|max:500',
            'precio_destacado'  => 'nullable|numeric|min:0.01|max:999.99',
            'orden'             => 'nullable|integer|min:0|max:999',
            'duracion_segundos' => 'nullable|integer|min:2|max:300',
            'activo'            => 'nullable|boolean',
            'imagen'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'sede_id'           => 'nullable|exists:sedes,id',
            'producto_id_1'     => 'nullable|exists:productos,id',
            'producto_id_2'     => 'nullable|exists:productos,id',
            'producto_id_3'     => 'nullable|exists:productos,id',
        ], [
            'titulo.required'          => 'El título de la promoción es obligatorio.',
            'titulo.min'               => 'El título debe tener al menos 3 caracteres.',
            'duracion_segundos.min'    => 'La duración debe ser de al menos 2 segundos.',
            'duracion_segundos.max'    => 'La duración no puede superar los 300 segundos (5 minutos).',
            'precio_destacado.numeric' => 'El precio destacado debe ser un valor numérico.',
            'precio_destacado.min'     => 'El precio destacado debe ser mayor a 0.',
        ]);

        // Verificar que no se repitan los mismos productos seleccionados
        $prods = array_filter([$data['producto_id_1'] ?? null, $data['producto_id_2'] ?? null, $data['producto_id_3'] ?? null]);
        if (count($prods) !== count(array_unique($prods))) {
            return back()->withErrors(['producto_id_2' => 'No puedes seleccionar el mismo producto más de una vez en la misma promoción.'])->withInput();
        }

        foreach (['producto_id_1','producto_id_2','producto_id_3','sede_id'] as $f) {
            if (array_key_exists($f, $data) && ($data[$f] === '' || $data[$f] === null)) {
                $data[$f] = null;
            }
        }

        if ($request->hasFile('imagen')) {
            if ($promocion->imagen_path && Storage::disk('public')->exists($promocion->imagen_path)) {
                Storage::disk('public')->delete($promocion->imagen_path);
            }
            $filename = Str::slug($data['titulo']) . '-' . time() . '.' . $request->imagen->extension();
            $request->imagen->storeAs('promociones', $filename, 'public');
            $data['imagen_path'] = 'promociones/' . $filename;
        }

        Log::info('=== DATA A GUARDAR ===', $data);

        $result = $promocion->update($data);

        Log::info('=== RESULTADO UPDATE ===', [
            'result'        => $result,
            'id_en_modelo'  => $promocion->id,
            'titulo_nuevo'  => $promocion->titulo,
        ]);

        return back()->with('success', 'Promoción actualizada.');
    }

    public function toggleActivo(Promocion $promocion)
    {
        $promocion->update(['activo' => !$promocion->activo]);
        $estado = $promocion->activo ? 'activada' : 'pausada';
        return back()->with('success', "Promoción {$estado}.");
    }

    public function destroy(Promocion $promocion)
    {
        Log::info('=== DESTROY PROMOCION ===', [
            'id'     => $promocion->id,
            'titulo' => $promocion->titulo,
        ]);

        $titulo = $promocion->titulo;

        if ($promocion->imagen_path && Storage::disk('public')->exists($promocion->imagen_path)) {
            Storage::disk('public')->delete($promocion->imagen_path);
            Log::info('Imagen eliminada: ' . $promocion->imagen_path);
        }

        $deleted = $promocion->delete();

        Log::info('=== DELETE RESULT ===', [
            'deleted'       => $deleted,
            'id_eliminado'  => $promocion->id,
        ]);

        return back()->with('success', '"' . $titulo . '" eliminada.');
    }

    public function create()  { return redirect()->route('admin.promociones.index'); }
    public function show(Promocion $p) { return redirect()->route('admin.promociones.index'); }
    public function edit(Promocion $p) { return redirect()->route('admin.promociones.index'); }
}