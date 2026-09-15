<?php

use App\Http\Controllers\Admin\CajaController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ComboController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\PromocionController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\FiadoController;
use App\Http\Controllers\Admin\AreaVentaController;
use App\Http\Controllers\Admin\InvitadoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\QrCuentaController;
use App\Http\Controllers\Kiosco\CarritoController;
use App\Http\Controllers\Kiosco\ComprobanteController;
use App\Http\Controllers\Kiosco\EstadoKioscoController;
use App\Http\Controllers\Kiosco\MenuController;
use App\Http\Controllers\Kiosco\MisPedidosController;
use App\Http\Controllers\Kiosco\PedidoKioscoController;
use Illuminate\Support\Facades\Route;

// ── Raíz — redirige al kiosco ──────────────────────────────────────────
Route::get('/', fn() => redirect()->route('kiosco.inicio'));

// ── Auth (Laravel Breeze) ──────────────────────────────────────────────
require __DIR__.'/auth.php';

// ══════════════════════════════════════════════════════════════════════
// KIOSCO — público, sin autenticación
// ══════════════════════════════════════════════════════════════════════
Route::prefix('kiosco')->name('kiosco.')->group(function () {

    Route::get('/',             [MenuController::class, 'inicio'])->name('inicio');
    Route::get('/menu',         [MenuController::class, 'menu'])->name('menu');

    // Carrito — manejado en sesión
    Route::post('/carrito/agregar',  [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/quitar',   [CarritoController::class, 'quitar'])->name('carrito.quitar');
    Route::post('/carrito/limpiar',  [CarritoController::class, 'limpiar'])->name('carrito.limpiar');
    Route::get('/carrito/datos',     [CarritoController::class, 'datos'])->name('carrito.datos');

    // Pago y pedido
    Route::get('/pago',              [PedidoKioscoController::class, 'pago'])->name('pago');
    Route::post('/pedido',           [PedidoKioscoController::class, 'store'])->name('pedido.store');

    Route::get('/api/config-publica', function () {
    return response()->json([
        'titular_deuna' => \App\Models\Configuracion::get('pago.titular_deuna', 'Bar ISTPET'),
    ]);
    })->name('api.config-publica');

    // Comprobante
    Route::get('/comprobante/{codigo}', [ComprobanteController::class, 'show'])->name('comprobante');

    // API — productos por sede/categoría (para JS)
    Route::get('/api/productos',    [MenuController::class, 'apiProductos'])->name('api.productos');
    Route::get('/api/promociones',  [MenuController::class, 'apiPromociones'])->name('api.promociones');

    // Huella de estado — polling liviano para auto-refresh sin recargar la página
    Route::get('/api/huella', [EstadoKioscoController::class, 'huella'])->name('api.huella');

    // Mis pedidos — historial del cliente logueado
    Route::get('/mis-pedidos', [MisPedidosController::class, 'index'])
        ->middleware('auth')
        ->name('mis-pedidos');
});

// ══════════════════════════════════════════════════════════════════════
// ADMIN — requiere autenticación y rol
// ══════════════════════════════════════════════════════════════════════
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // ── Dashboard: todos los roles ──────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Pedidos: todos los roles ────────────────────────────────────
    Route::resource('pedidos', PedidoController::class)->only(['index','show']);
    Route::patch('/pedidos/{pedido}/verificar-qr', [PedidoController::class, 'verificarQr'])->name('pedidos.verificar-qr');
    Route::patch('/pedidos/{pedido}/cobrar',        [PedidoController::class, 'cobrar'])->name('pedidos.cobrar');
    Route::patch('/pedidos/{pedido}/entregar',      [PedidoController::class, 'entregar'])->name('pedidos.entregar');
    Route::patch('/pedidos/{pedido}/cancelar',      [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');

    // ── Stock: cajero + admin + superadmin ─────────────────────────
    Route::middleware('role:superadmin|admin|cajero')->group(function () {
        Route::get('/stock',             [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/ajuste',     [StockController::class, 'ajuste'])->name('stock.ajuste');
        Route::get('/stock/movimientos', [StockController::class, 'movimientos'])->name('stock.movimientos');
    });

    // ── Admin + Superadmin ─────────────────────────────────────────
    Route::middleware('role:superadmin|admin')->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::patch('/productos/{producto}/toggle-stock', [ProductoController::class, 'toggleStock'])->name('productos.toggle-stock');
        Route::resource('categorias', CategoriaController::class);
        Route::resource('combos', ComboController::class);
        Route::get('/reportes',           [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar',  [ReporteController::class, 'exportar'])->name('reportes.exportar');
        Route::get('/caja',               [CajaController::class, 'index'])->name('caja.index');
        Route::get('/caja/cierre/{sede}', [CajaController::class, 'cierre'])->name('caja.cierre');
        Route::resource('promociones', PromocionController::class)->parameters(['promociones' => 'promocion']);
        Route::patch('/promociones/{promocion}/toggle', [PromocionController::class, 'toggleActivo'])->name('promociones.toggle');
        Route::post('/promociones/reordenar', [PromocionController::class, 'reordenar'])->name('promociones.reordenar');
        Route::resource('usuarios', UsuarioController::class);
        Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    });

    // ── Solo Superadmin ────────────────────────────────────────────
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/configuracion',  [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    });

    // ══════════════════════════════════════════════════════════
    // MÓDULOS EXTRA — bloqueados por middleware si no están activos
    // Visibles en menú con badge "Próximamente"
    // ══════════════════════════════════════════════════════════

    Route::middleware('modulo:fiado')->prefix('fiado')->name('fiado.')->group(function () {
        Route::get('/',           [FiadoController::class, 'index'])->name('index');
        Route::get('/{fiado}',    [FiadoController::class, 'show'])->name('show');
    });

    Route::middleware('modulo:areas_venta')->prefix('areas-venta')->name('areas-venta.')->group(function () {
        Route::get('/',           [AreaVentaController::class, 'index'])->name('index');
        Route::resource('/', AreaVentaController::class)->except(['index'])->names([
            'create' => 'create', 'store' => 'store',
            'edit' => 'edit', 'update' => 'update', 'destroy' => 'destroy',
        ]);
    });

    Route::middleware('modulo:invitados')->prefix('invitados')->name('invitados.')->group(function () {
        Route::get('/',           [InvitadoController::class, 'index'])->name('index');
    });

    Route::middleware('modulo:inventario_avanzado')->prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/',           [InventarioController::class, 'index'])->name('index');
    });

    Route::middleware('modulo:qr_multiple')->prefix('qr-cuentas')->name('qr-cuentas.')->group(function () {
        Route::get('/',           [QrCuentaController::class, 'index'])->name('index');
        Route::resource('/', QrCuentaController::class)->except(['index'])->names([
            'create' => 'create', 'store' => 'store',
            'edit' => 'edit', 'update' => 'update', 'destroy' => 'destroy',
        ]);
    });
});