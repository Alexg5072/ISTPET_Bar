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
        $sedes    = \App\Models\Sede::activos()->get();
        return view('admin.usuarios.index', compact('usuarios','roles','sedes'));
    }
    public function store(Request $request) {
        $request->merge([
            'email' => strtolower(trim((string) $request->email)),
            'name'  => preg_replace('/\s+/', ' ', trim((string) $request->name)),
        ]);

        $data = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'    => ['required', 'string', 'email:rfc,filter', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'exists:roles,name'],
            'sede_id'  => ['required_if:role,cajero,visor', 'nullable', 'exists:sedes,id'],
        ], [
            'name.required'        => 'El nombre completo es obligatorio.',
            'name.min'             => 'El nombre debe tener al menos 3 caracteres.',
            'name.regex'           => 'El nombre solo puede contener letras y espacios.',
            'email.required'       => 'El correo electrónico es obligatorio.',
            'email.email'          => 'Ingresa un formato de correo electrónico válido con @ y dominio.',
            'email.unique'         => 'Este correo electrónico ya está registrado.',
            'password.required'    => 'La contraseña es obligatoria.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required'        => 'Debes asignar un rol al usuario.',
            'sede_id.required_if'  => 'Los cajeros y visores deben tener una sede asignada.',
            'sede_id.exists'       => 'La sede seleccionada no es válida.',
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
        return back()->with('success',"Usuario '{$user->name}' creado con éxito.");
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