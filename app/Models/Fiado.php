<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fiado extends Model
{
    protected $fillable = [
        'user_id',
        'sede_id',
        'monto_total',
        'monto_pagado',
        'monto_pendiente',
        'limite_credito',
        'estado',
        'activo',
        'is_future_module',
    ];

    protected function casts(): array
    {
        return [
            'monto_total' => 'decimal:2',
            'monto_pagado' => 'decimal:2',
            'monto_pendiente' => 'decimal:2',
            'limite_credito' => 'decimal:2',
            'activo' => 'boolean',
            'is_future_module' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(FiadoMovimiento::class);
    }
}