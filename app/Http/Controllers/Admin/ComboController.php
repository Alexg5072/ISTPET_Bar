<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Combo, ComboItem, Producto, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ComboController extends Controller
{
    public function index()
    {
        $combos = Combo::with(['items.producto', 'sede'])->latest()->paginate(20);
        $todosLosCombos = Combo::select('id', 'nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('admin.combos.index', compact('combos', 'todosLosCombos', 'productos'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('admin.combos.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombre' => preg_replace('/\s+/', ' ', trim((string) $request->nombre)),
        ]);

        $data = $request->validate([
            'nombre'       => 'required|string|min:2|max:100|unique:combos,nombre',
            'descripcion'  => 'nullable|string|max:500',
            'precio'       => 'required|numeric|min:0.05|max:999.99',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'productos'    => 'required|array|min:1',
            'productos.*'  => 'exists:productos,id',
            'cantidades'   => 'required|array|min:1',
            'cantidades.*' => 'integer|min:1|max:100',
        ], [
            'nombre.required'       => 'El nombre del combo es obligatorio.',
            'nombre.min'            => 'El nombre del combo debe tener al menos 2 caracteres.',
            'nombre.unique'         => 'Ya existe un combo con ese nombre.',
            'precio.required'       => 'El precio del combo es obligatorio.',
            'precio.min'            => 'El precio del combo debe ser de al menos $0.05.',
            'precio.max'            => 'El precio del combo no puede superar $999.99.',
            'productos.required'    => 'Debes incluir al menos un producto en el combo.',
            'productos.min'         => 'Debes incluir al menos un producto en el combo.',
            'cantidades.*.min'      => 'La cantidad por producto debe ser de al menos 1 unidad.',
            'cantidades.*.max'      => 'La cantidad por producto no puede exceder 100 unidades.',
        ]);

        if ($request->hasFile('imagen')) {
            $slug     = Str::slug($data['nombre']);
            $filename = $slug.'-'.time().'.'.$request->imagen->extension();
            $request->imagen->storeAs('combos', $filename, 'public');
            $data['imagen_path'] = 'combos/'.$filename;
        }

        // Generar slug único
        $baseSlug = Str::slug($data['nombre']);
        $slug = $baseSlug;
        $i = 1;
        while (Combo::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $combo = Combo::create([
            'nombre'      => $data['nombre'],
            'slug'        => $slug,
            'descripcion' => $data['descripcion'] ?? null,
            'precio'      => $data['precio'],
            'imagen_path' => $data['imagen_path'] ?? null,
            'activo'      => true,
        ]);

        foreach ($data['productos'] as $i => $productoId) {
            ComboItem::create([
                'combo_id'    => $combo->id,
                'producto_id' => $productoId,
                'cantidad'    => $data['cantidades'][$i] ?? 1,
            ]);
        }

        AuditLog::registrar('Combo creado — '.$combo->nombre, Combo::class, $combo->id);

        return redirect()->route('admin.combos.index')
            ->with('success', "Combo '{$combo->nombre}' creado.");
    }

    public function edit(Combo $combo)
    {
        $combo->load('items.producto');
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $todosLosCombos = Combo::select('id', 'nombre')->get();
        return view('admin.combos.edit', compact('combo', 'productos', 'todosLosCombos'));
    }

    public function update(Request $request, Combo $combo)
    {
        $request->merge([
            'nombre' => preg_replace('/\s+/', ' ', trim((string) $request->nombre)),
        ]);

        $data = $request->validate([
            'nombre'      => 'required|string|min:2|max:100|unique:combos,nombre,'.$combo->id,
            'descripcion' => 'nullable|string|max:500',
            'precio'      => 'required|numeric|min:0.05|max:999.99',
            'imagen'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'nombre.required' => 'El nombre del combo es obligatorio.',
            'nombre.min'      => 'El nombre del combo debe tener al menos 2 caracteres.',
            'nombre.unique'   => 'Ya existe un combo con ese nombre.',
            'precio.required' => 'El precio del combo es obligatorio.',
            'precio.min'      => 'El precio del combo debe ser de al menos $0.05.',
            'precio.max'      => 'El precio del combo no puede superar $999.99.',
        ]);

        // Checkbox activo — si no viene en POST es false
        $data['activo'] = $request->boolean('activo');
        $baseSlug = Str::slug($data['nombre']);
        $slug = $baseSlug;
        $i = 1;
        while (Combo::where('slug', $slug)->where('id', '!=', $combo->id)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('imagen')) {
            if ($combo->imagen_path && Storage::disk('public')->exists($combo->imagen_path)) {
                Storage::disk('public')->delete($combo->imagen_path);
            }
            $slug     = Str::slug($data['nombre']);
            $filename = $slug.'-'.time().'.'.$request->imagen->extension();
            $request->imagen->storeAs('combos', $filename, 'public');
            $data['imagen_path'] = 'combos/'.$filename;
        }

        // Eliminar 'imagen' del array para que no sobreescriba imagen_path
        unset($data['imagen']);

        $combo->update($data);
        AuditLog::registrar('Combo actualizado — '.$combo->nombre, Combo::class, $combo->id);

        return redirect()->route('admin.combos.index')
            ->with('success', "Combo '{$combo->nombre}' actualizado.");
    }

    public function destroy(Combo $combo)
    {
        if ($combo->imagen_path) Storage::disk('public')->delete($combo->imagen_path);
        $nombre = $combo->nombre;
        $combo->delete();
        AuditLog::registrar('Combo eliminado — '.$nombre, Combo::class, $combo->id);

        return redirect()->route('admin.combos.index')
            ->with('success', "Combo '{$nombre}' eliminado.");
    }

    public function show(Combo $combo) { return redirect()->route('admin.combos.index'); }
}