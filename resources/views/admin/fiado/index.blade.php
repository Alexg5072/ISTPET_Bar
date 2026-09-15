{{-- resources/views/admin/fiado/index.blade.php --}}
@extends('layouts.app')
@section('title','Fiado')
@section('page-icon','💳')
@section('page-title','Fiado / Cuentas por Cobrar')
@section('page-subtitle','Módulo en preparación — disponible en próxima fase')
@section('content')
<div class="pt-4">
    <x-modulo-proximamente modulo="fiado y cuentas por cobrar"
        descripcion="Este módulo permitirá gestionar cuentas por cobrar, registrar cargos y abonos por cliente, y ver el historial de deudas. La estructura de base de datos ya está lista. Se habilitará en la siguiente fase del proyecto." />
</div>
@endsection