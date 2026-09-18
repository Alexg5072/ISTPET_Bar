<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $fillable = [
        'codigo', 'sede_id', 'user_id', 'tipo_cliente_id',
        'area_origen', 'metodo_pago', 'estado', 'estado_pago',
        'subtotal', 'total', 'observaciones',
        'entregado_por', 'entregado_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'entregado_at' => 'datetime',
    ];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tipoCliente(): BelongsTo
    {
        return $this->belongsTo(TipoCliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(Comprobante::class);
    }

    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    public function scopeDelDia(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeDeSede(Builder $query, ?int $sedeId = null): Builder
    {
        if (!$sedeId) {
            return $query;
        }

        return $query->where('sede_id', $sedeId);
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereIn('estado', [
            'pendiente_pago',
            'pendiente_verificacion',
            'pagado',
        ]);
    }

    public function getEstadoBadgeAttribute(): array
    {
        return match ($this->estado) {
            'pendiente_pago' => ['label' => 'Pend. Pago', 'color' => 'yellow'],
            'pendiente_verificacion' => ['label' => 'Pend. QR', 'color' => 'blue'],
            'pagado' => ['label' => 'Pagado', 'color' => 'green'],
            'entregado' => ['label' => 'Entregado', 'color' => 'gray'],
            'cancelado' => ['label' => 'Cancelado', 'color' => 'red'],
            default => ['label' => $this->estado, 'color' => 'gray'],
        };
    }

    public function getTotalFormateadoAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }

    public function puedeEntregarse(): bool
    {
        return in_array($this->estado, ['pagado']);
    }

    public function puedeCancelarse(): bool
    {
        return !in_array($this->estado, ['entregado', 'cancelado']);
    }

    public static function generarCodigo(): string
    {
        // Obtener el número más alto del día para evitar duplicados
        $ultimos = static::whereDate('created_at', today())
            ->orderByDesc('id')
            ->pluck('codigo');

        $maxNumero = 0;
        foreach ($ultimos as $cod) {
            $n = (int) substr($cod, 4);
            if ($n > $maxNumero) $maxNumero = $n;
        }

        // Intentar hasta encontrar un código libre
        do {
            $maxNumero++;
            $codigo = 'PED-' . str_pad($maxNumero, 3, '0', STR_PAD_LEFT);
        } while (static::where('codigo', $codigo)->exists());

        return $codigo;
    }
}