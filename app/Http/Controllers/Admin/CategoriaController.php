<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Categoria, Sede};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller {
    public function index() {
        $categorias = Categoria::with('sede')->orderBy('orden')->paginate(20);
        $sedes = Sede::activos()->get();
        $maxOrden = Categoria::max('orden') ?? 0;
        return view('admin.categorias.index', compact('categorias', 'sedes', 'maxOrden'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre'  => 'required|string|max:100|unique:categorias,nombre',
            'icono'   => 'nullable|string|max:20',
            'sede_id' => 'nullable|exists:sedes,id',
            'orden'   => 'nullable|integer',
        ], [
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);
        $data['slug']  = Str::slug($data['nombre']);
        $data['icono'] = $data['icono'] ?: 'utensils'; // default si viene null
        // Orden automático: siguiente al último
        if (empty($data['orden']) || $data['orden'] == 0) {
            $data['orden'] = (Categoria::max('orden') ?? 0) + 1;
        }
        Categoria::create($data);
        return back()->with('success','Categoría creada.');
    }

    public function update(Request $request, Categoria $categoria) {
        $data = $request->validate([
            'nombre'  => 'required|string|max:100|unique:categorias,nombre,'.$categoria->id,
            'icono'   => 'nullable|string|max:20',
            'sede_id' => 'nullable|exists:sedes,id',
            'orden'   => 'nullable|integer',
        ], [
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        $data['icono']  = $data['icono'] ?: ($categoria->icono ?: 'utensils');
        // ← Manejar activo FUERA del validate, directamente
        $data['activo'] = $request->has('activo');

        $categoria->update($data);
        return back()->with('success','Categoría actualizada.');
    }

    public function destroy(Categoria $categoria) {
        $categoria->delete();
        return back()->with('success','Categoría eliminada.');
    }

    public function create() { return view('admin.categorias.index'); }
    public function show(Categoria $c) { return redirect()->route('admin.categorias.index'); }
    public function edit(Categoria $c) { return redirect()->route('admin.categorias.index'); }
}