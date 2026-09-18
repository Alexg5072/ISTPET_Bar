<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->email)),
        ]);

        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email:rfc,filter'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Ingresa un formato de correo válido (ej: usuario@ejemplo.com).',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'No se pudo obtener el usuario autenticado.',
            ]);
        }

        if (!$user->activo) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ]);
        }

        $request->session()->regenerate();
        $user->registrarAcceso();

        AuditLog::registrar('Inicio de sesión', null, null, null, [
            'ip' => $request->ip(),
        ]);

        // Usuarios con rol 'usuario' van al kiosco, el resto al panel admin
        if ($user->hasRole('usuario') && !$user->hasAnyRole(['superadmin', 'admin', 'cajero', 'visor'])) {
            return redirect()->route('kiosco.inicio');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        AuditLog::registrar('Cierre de sesión');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}