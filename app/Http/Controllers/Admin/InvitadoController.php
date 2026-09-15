<?php namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

/** MÓDULO EXTRA — Invitados / externos */
class InvitadoController extends Controller {
    public function index() { return view('admin.invitados.index'); }
}