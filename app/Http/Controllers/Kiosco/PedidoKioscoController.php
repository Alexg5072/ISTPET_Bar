<?php namespace App\Http\Controllers\Kiosco;
use App\Http\Controllers\Controller;
use App\Models\{Configuracion, QrCuenta, Sede};
use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoKioscoController extends Controller
{
    public function __construct(private PedidoService $pedidoService) {}

    public function pago()
    {
        $carrito = session('kiosco_carrito', []);

        if (empty($carrito)) {
            return redirect()->route('kiosco.menu')->with('error', 'Tu carrito está vacío.');
        }

        $sedeSlug = session('kiosco_sede', 'instituto');
        $sede     = Sede::where('slug', $sedeSlug)->firstOrFail();
        $qrActivo = QrCuenta::activa();

        $efectivoHabilitado = Configuracion::get('pago.efectivo_habilitado', true);
        $qrHabilitado       = Configuracion::get('pago.qr_deuna_habilitado', true);
        $titularDeuna       = Configuracion::get('pago.titular_deuna', null);

        // Calcular total del carrito
        $total = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);

        return view('kiosco.pago', compact(
            'carrito', 'sede', 'total',
            'qrActivo', 'efectivoHabilitado', 'qrHabilitado', 'titularDeuna'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr_deuna',
        ]);

        $carrito = session('kiosco_carrito', []);

        if (empty($carrito)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Tu carrito está vacío.'], 422);
            }
            return redirect()->route('kiosco.menu')->with('error', 'Tu carrito está vacío.');
        }

        $sedeId = session('kiosco_sede_id');

        if (!$sedeId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Sede no seleccionada.'], 422);
            }
            return redirect()->route('kiosco.inicio');
        }

        try {
            $pedido = $this->pedidoService->crearDesdeSesion(
                $carrito,
                $request->metodo_pago,
                $sedeId,
                auth()->id()
            );

            // Limpiar carrito tras pedido exitoso
            session()->forget(['kiosco_carrito']);

            // Reinicio automático si está configurado
            if (Configuracion::get('kiosco.reiniciar_tras_pedido', true)) {
                session()->forget(['kiosco_sede', 'kiosco_sede_id']);
            }

            // Extraer número del código (PED-001 → 1)
            $numeroPedido = (int) substr($pedido->codigo, 4);

            // Si es AJAX devolver JSON con el número de pedido
            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success'       => true,
                    'codigo'        => $pedido->codigo,
                    'numero_pedido' => $numeroPedido,
                    'redirect'      => route('kiosco.comprobante', $pedido->codigo),
                ]);
            }

            return redirect()->route('kiosco.comprobante', $pedido->codigo);

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}