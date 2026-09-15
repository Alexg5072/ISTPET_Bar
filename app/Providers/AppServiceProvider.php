<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Agregamos esta importación

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzar HTTPS si se detecta el túnel de Ngrok
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        // Prevenir lazy loading en desarrollo
        Model::preventLazyLoading(app()->isLocal());

        // Traducir paginación al español
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.tailwind');
    }
}
