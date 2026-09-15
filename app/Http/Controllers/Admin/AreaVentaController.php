<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

/** MÓDULO EXTRA — Áreas de venta (Bar / Cocina / Pastelería) */
class AreaVentaController extends Controller {
    public function index()   { return view('admin.areas-venta.index'); }
    public function create()  { return view('admin.areas-venta.index'); }
    public function store()   { return redirect()->route('admin.areas-venta.index'); }
    public function edit($id) { return redirect()->route('admin.areas-venta.index'); }
    public function update($r,$id) { return redirect()->route('admin.areas-venta.index'); }
    public function destroy($id) { return redirect()->route('admin.areas-venta.index'); }
}