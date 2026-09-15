<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasAnyRole(['superadmin', 'admin', 'cajero', 'visor'])) {
            abort(403, 'Acceso no autorizado.');
        }

        $user->registrarAcceso();

        return $next($request);
    }
}