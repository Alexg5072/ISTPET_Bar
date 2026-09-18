<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{QrCuenta, AuditLog};
use Illuminate\Http\Request;

/** QR básico funcional en v1 — múltiples QR es módulo extra */
class QrCuentaController extends Controller
{
    public function index()
    {
        $qrCuentas = QrCuenta::with('sede')->get();
        return view('admin.qr-cuentas.index', compact('qrCuentas'));
    }

    public function create()  { return redirect()->route('admin.qr-cuentas.index'); }

    public function store(Request $request)
    {
        $request->merge([
            'nombre'  => preg_replace('/\s+/', ' ', trim((string) $request->nombre)),
            'titular' => preg_replace('/\s+/', ' ', trim((string) $request->titular)),
        ]);

        $data = $request->validate([
            'id'       => 'nullable|exists:qr_cuentas,id',
            'nombre'   => 'required|string|min:3|max:100',
            'titular'  => 'required|string|min:3|max:100|regex:/^[\pL\s]+$/u',
            'imagen'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'nombre.required'  => 'El nombre de la cuenta es obligatorio.',
            'nombre.min'       => 'El nombre debe tener al menos 3 caracteres.',
            'titular.required' => 'El nombre del titular es obligatorio.',
            'titular.min'      => 'El nombre del titular debe tener al menos 3 caracteres.',
            'titular.regex'    => 'El titular solo debe contener letras y espacios.',
        ]);

        if ($request->hasFile('imagen')) {
            $filename = 'deuna-qr-'.time().'.'.$request->imagen->extension();
            $request->imagen->storeAs('qr', $filename, 'public');
            $request->imagen->storeAs('qr', 'DEUNA.jpg', 'public');
            $data['imagen_qr_path'] = 'qr/'.$filename;
        }

        $id = $request->input('id');
        $qr = $id ? QrCuenta::find($id) : QrCuenta::where('is_global', true)->first();

        if ($qr) {
            $qr->update($data);
            AuditLog::registrar('QR cuenta actualizada — '.$qr->nombre, QrCuenta::class, $qr->id);
        } else {
            $qr = QrCuenta::create(array_merge($data, ['activo' => true, 'is_global' => true]));
            AuditLog::registrar('QR cuenta creada — '.$qr->nombre, QrCuenta::class, $qr->id);
        }

        // Sincronizar titular con configuración general
        \App\Models\Configuracion::where('clave', 'pago.titular_deuna')->update([
            'valor' => $qr->titular,
        ]);

        return back()->with('success', 'Cuenta QR guardada correctamente.');
    }

    public function update(Request $r, $id) { return redirect()->route('admin.qr-cuentas.index'); }
    public function destroy($id)            { return redirect()->route('admin.qr-cuentas.index'); }
    public function edit($id)               { return redirect()->route('admin.qr-cuentas.index'); }
    public function show($id)               { return redirect()->route('admin.qr-cuentas.index'); }
}