<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{User, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller {
    public function index() {
        $usuarios = User::with(['roles','sede'])->latest()->paginate(20);
        $roles    = Role::all();
        return view('admin.usuarios.index', compact('usuarios','roles'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|exists:roles,name',
            'sede_id'  => 'nullable|exists:sedes,id',
        ]);
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'sede_id'  => $data['sede_id'] ?? null,
            'activo'   => true,
        ]);
        $user->assignRole($data['role']);
        AuditLog::registrar('Usuario creado — '.$user->name, User::class, $user->id);
        return back()->with('success',"Usuario '{$user->name}' creado.");
    }
    public function toggle(User $usuario) {
        if ($usuario->id === Auth::id()) return back()->with('error','No puedes desactivar tu propia cuenta.');
        $usuario->update(['activo' => !$usuario->activo]);
        $estado = $usuario->activo ? 'activado' : 'desactivado';
        AuditLog::registrar("Usuario {$estado} — {$usuario->name}", User::class, $usuario->id);
        return back()->with('success',"Usuario {$estado}.");
    }
    public function destroy(User $usuario) {
        if ($usuario->id === Auth::id()) return back()->with('error','No puedes eliminar tu propia cuenta.');
        if ($usuario->hasRole('superadmin')) return back()->with('error','No puedes eliminar un superadmin.');
        AuditLog::registrar('Usuario eliminado — '.$usuario->name, User::class, $usuario->id);
        $usuario->delete();
        return back()->with('success','Usuario eliminado.');
    }
    public function create() { return redirect()->route('admin.usuarios.index'); }
    public function show(User $u) { return redirect()->route('admin.usuarios.index'); }
    public function edit(User $u) { return redirect()->route('admin.usuarios.index'); }
    public function update(Request $r, User $u) { return redirect()->route('admin.usuarios.index'); }
}