<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpiar audit_logs antiguos (más de 90 días) — ejecutar diariamente
Schedule::command('model:prune', ['--model' => 'App\Models\AuditLog'])->daily();