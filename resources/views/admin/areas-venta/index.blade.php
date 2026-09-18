@extends('layouts.app')
@section('title','Áreas de Venta')
@section('page-icon')
<x-admin.icon name="areas-venta" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Áreas de Venta')
@section('page-subtitle','Bar · Cocina · Pastelería — Módulo en preparación')
@section('content')
<div class="pt-4">
    <x-modulo-proximamente modulo="áreas de venta"
        descripcion="Este módulo permitirá separar el sistema por áreas operativas: Bar, Cocina y Pastelería. Cada área tendrá su propio stock, reportes y categorías de productos. La base de datos ya está preparada con la tabla areas_venta." />
</div>
@endsection