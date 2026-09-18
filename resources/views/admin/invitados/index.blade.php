@extends('layouts.app')
@section('title','Invitados')
@section('page-icon')
<x-admin.icon name="invitados" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Invitados / Externos')
@section('page-subtitle','Módulo en preparación')
@section('content')
<div class="pt-4">
    <x-modulo-proximamente modulo="invitados y usuarios externos"
        descripcion="Este módulo permitirá atender a personas externas al instituto y a la escuela de conducción, con un flujo de pedido separado, reportes propios y posibilidad de segmentación avanzada por tipo de cliente." />
</div>
@endsection