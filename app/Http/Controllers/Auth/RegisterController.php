<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Sede};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function create()
    {
        $sedes = Sede::activos()->get();
        return view('auth.register', compact('sedes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'cedula'                => ['required', 'string', 'size:10', 'unique:users,cedula', 'regex:/^[0-9]{10}$/'],
            'email'                 => ['required', 'email', 'max:100', 'unique:users,email'],
            'telefono'              => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/'],
            'tipo_sede'             => ['required', 'in:instituto,conduccion'],
            'carrera'               => ['required', 'string', 'max:100'],
            'password'              => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'         => 'El nombre completo es obligatorio.',
            'cedula.required'       => 'La cédula es obligatoria.',
            'cedula.size'           => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.unique'         => 'Esta cédula ya está registrada.',
            'cedula.regex'          => 'La cédula solo debe contener números.',
            'email.unique'          => 'Este correo ya está registrado.',
            'telefono.size'         => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono.regex'        => 'El teléfono solo debe contener números.',
            'tipo_sede.required'    => 'Debes seleccionar a qué institución perteneces.',
            'carrera.required'      => 'Debes seleccionar tu carrera.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Validar cédula ecuatoriana (dígito verificador)
        if (!$this->validarCedula($request->cedula)) {
            return back()->withErrors(['cedula' => 'La cédula ingresada no es válida.'])->withInput();
        }

        // Obtener sede según tipo
        $sede = Sede::where('slug', $request->tipo_sede)->first();

        $user = User::create([
            'name'       => trim($request->name),
            'cedula'     => $request->cedula,
            'email'      => $request->email,
            'telefono'   => $request->telefono,
            'password'   => Hash::make($request->password),
            'tipo_sede'  => $request->tipo_sede,
            'carrera'    => $request->carrera,
            'sede_id'    => $sede?->id,
            'activo'     => true,
        ]);

        // Asignar rol 'usuario' (para fiados y módulo de clientes)
        $rolUsuario = Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
        $user->assignRole($rolUsuario);

        // Login automático
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('kiosco.inicio')
            ->with('success', '¡Bienvenido, ' . explode(' ', $user->name)[0] . '! Tu cuenta fue creada.');
    }

    /**
     * Validar cédula ecuatoriana por dígito verificador
     */
    /**
     * AJAX: verificar si la cédula ya está registrada
     */
    public function verificarCedula(Request $request)
    {
        $cedula = $request->query('cedula', '');
        $existe = User::where('cedula', $cedula)->exists();
        return response()->json(['existe' => $existe]);
    }

    private function validarCedula(string $cedula): bool
    {
        if (strlen($cedula) !== 10 || !ctype_digit($cedula)) return false;

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) return false;

        $digitos    = array_map('intval', str_split($cedula));
        $verificador = $digitos[9];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $val = $digitos[$i];
            if ($i % 2 === 0) {
                $val *= 2;
                if ($val > 9) $val -= 9;
            }
            $suma += $val;
        }

        $residuo = $suma % 10;
        $digitoCalculado = $residuo === 0 ? 0 : 10 - $residuo;

        return $digitoCalculado === $verificador;
    }

    public function verificarTelefono(Request $request)
    {
        $telefono = $request->query('telefono', '');
        $existe = User::where('telefono', $telefono)->exists();
        return response()->json(['existe' => $existe]);
    }
}