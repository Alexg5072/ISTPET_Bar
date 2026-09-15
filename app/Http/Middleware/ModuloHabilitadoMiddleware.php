<?php

namespace App\Http\Middleware;

use App\Models\Configuracion;
use Closure;
use Illuminate\Http\Request;

/**
 * Bloquea el acceso a módulos extra que aún no están habilitados.
 * Si el módulo está desactivado en configuraciones, redirige al dashboard
 * con un mensaje informativo.
 *
 * Uso en rutas:
 *   Route::middleware('modulo:fiado')->group(...)
 */
class ModuloHabilitadoMiddleware
{
    public function handle(Request $request, Closure $next, string $modulo)
    {
        if (!Configuracion::moduloHabilitado($modulo)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "El módulo '{$modulo}' aún no está habilitado.",
                ], 403);
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('info', "El módulo '".ucfirst(str_replace('_',' ',$modulo))."' está en preparación y se habilitará próximamente.");
        }

        return $next($request);
    }
}