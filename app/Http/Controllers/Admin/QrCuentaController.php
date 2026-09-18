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
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'titular'  => 'required|string|max:255',
            'imagen'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $filename = 'deuna-qr-'.time().'.'.$request->imagen->extension();
            $request->imagen->storeAs('qr', $filename, 'public');
            $data['imagen_qr_path'] = 'qr/'.$filename;
        }

        $qr = QrCuenta::create(array_merge($data, ['activo' => true, 'is_global' => true]));
        AuditLog::registrar('QR cuenta creada — '.$qr->nombre, QrCuenta::class, $qr->id);

        return back()->with('success', 'Cuenta QR guardada.');
    }

    public function update(Request $r, $id) { return redirect()->route('admin.qr-cuentas.index'); }
    public function destroy($id)            { return redirect()->route('admin.qr-cuentas.index'); }
    public function edit($id)               { return redirect()->route('admin.qr-cuentas.index'); }
    public function show($id)               { return redirect()->route('admin.qr-cuentas.index'); }
}