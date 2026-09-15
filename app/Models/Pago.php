<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'pedido_id',
        'qr_cuenta_id',
        'metodo',
        'monto',
        'estado',
        'verificado_por',
        'verificado_at',
        'referencia',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'verificado_at' => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function qrCuenta(): BelongsTo
    {
        return $this->belongsTo(QrCuenta::class, 'qr_cuenta_id');
    }

    public function verificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    public function marcarVerificado(int $userId): void
    {
        $this->update([
            'estado' => 'pagado',
            'verificado_por' => $userId,
            'verificado_at' => now(),
        ]);

        $this->pedido?->update([
            'estado' => 'pagado',
            'estado_pago' => 'pagado',
        ]);
    }
}