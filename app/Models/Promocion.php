<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Promocion extends Model
{
    protected $table = 'promociones';

    protected $fillable = [
        'sede_id',
        'titulo',
        'descripcion',
        'imagen_path',
        'precio_destacado',
        'producto_id_1',
        'producto_id_2',
        'producto_id_3',
        'orden',
        'duracion_segundos',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            // precio_destacado se guarda como string ("2.50") para evitar errores de cast
        ];
    }

    public function producto1() { return $this->belongsTo(\App\Models\Producto::class, 'producto_id_1'); }
    public function producto2() { return $this->belongsTo(\App\Models\Producto::class, 'producto_id_2'); }
    public function producto3() { return $this->belongsTo(\App\Models\Producto::class, 'producto_id_3'); }

    public function verificarStockYDesactivar(): void
    {
        $productos = array_filter([
            $this->producto_id_1 ? \App\Models\Producto::find($this->producto_id_1) : null,
            $this->producto_id_2 ? \App\Models\Producto::find($this->producto_id_2) : null,
            $this->producto_id_3 ? \App\Models\Producto::find($this->producto_id_3) : null,
        ]);
        foreach ($productos as $prod) {
            if ($prod && $prod->stock_activo && $prod->stock_actual <= 0) {
                $this->update(['activo' => false]);
                return;
            }
        }
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function getImagenUrlAttribute(): ?string
    {
        if ($this->imagen_path && Storage::disk('public')->exists($this->imagen_path)) {
            return asset('storage/' . $this->imagen_path);
        }

        return null;
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function scopeDeSede(Builder $query, ?int $sedeId = null): Builder
    {
        if (!$sedeId) {
            return $query;
        }

        return $query->where(function (Builder $subquery) use ($sedeId) {
            $subquery->where('sede_id', $sedeId)
                ->orWhereNull('sede_id');
        });
    }
}