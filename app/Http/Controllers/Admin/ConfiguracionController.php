<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Configuracion, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $grupos = Configuracion::all()->groupBy('grupo');
        return response()
        ->view('admin.configuracion.index', compact('grupos'))
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
        ->header('Pragma', 'no-cache');
    }

    public function update(Request $request)
    {
        // PHP convierte puntos en guiones bajos en nombres de campos
        // cfg[pago.efectivo_habilitado] llega como cfg[pago_efectivo_habilitado]
        // Por eso usamos guión bajo en el name y aquí convertimos de vuelta
        $submitted = $request->input('cfg', []);

        Log::info('=== CONFIGURACION UPDATE ===', [
            'submitted_keys' => array_keys($submitted),
            'submitted'      => $submitted,
        ]);

        $configs = Configuracion::all();
        $guardados = [];
        $errores   = [];

        foreach ($configs as $config) {
            $clave    = $config->clave;
            // La clave en DB tiene punto: pago.efectivo_habilitado
            // En el form llega con guión bajo: pago_efectivo_habilitado
            $claveForm = str_replace('.', '_', $clave);

            if ($config->tipo === 'boolean') {
                $valor = array_key_exists($claveForm, $submitted) ? 'true' : 'false';

            } elseif ($config->tipo === 'integer') {
                if (!array_key_exists($claveForm, $submitted)) continue;
                $raw = $submitted[$claveForm];
                if (!is_numeric($raw)) {
                    $errores[] = "Valor inválido para {$clave}: {$raw}";
                    continue;
                }
                $valor = (string) max(0, (int) $raw);

            } else {
                if (!array_key_exists($claveForm, $submitted)) continue;
                $valor = substr(trim((string)($submitted[$claveForm] ?? '')), 0, 1000);
            }

            $config->update(['valor' => $valor]);
            $guardados[$clave] = $valor;
        }

        Log::info('=== CONFIGURACION GUARDADA ===', [
            'guardados' => $guardados,
            'errores'   => $errores,
        ]);

        AuditLog::registrar('Configuración del sistema actualizada');

        // Garantiza que el "updated_at" más reciente de configuraciones
        // siempre refleje este guardado, sin depender de si Eloquent detectó
        // el valor como "modificado" en cada fila individual. Esto es lo
        // que el sistema de auto-actualización del kiosco usa para saber
        // si debe refrescarse, así que tiene que ser siempre confiable.
        Configuracion::query()->limit(1)->update(['updated_at' => now()]);

        return redirect()->route('admin.configuracion.index')
        ->with('success', 'Configuración guardada correctamente.')
        ->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
    ]);
    }
}