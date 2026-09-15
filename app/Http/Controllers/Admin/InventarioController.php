<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

/** MÓDULO EXTRA — Inventario avanzado por lotes */
class InventarioController extends Controller {
    public function index() { return view('admin.inventario.index'); }
}