<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'sede_id', 'area_venta_id',
        'nombre', 'slug', 'descripcion', 'precio',
        'imagen_path', 'stock_actual', 'stock_minimo',
        'stock_activo', 'es_combo', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock_activo' => 'boolean',
        'es_combo' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function areaVenta(): BelongsTo
    {
        return $this->belongsTo(AreaVenta::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class)->orderBy('orden');
    }

    public function stockMovimientos(): HasMany
    {
        return $this->hasMany(StockMovimiento::class);
    }

    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class);
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen_path && Storage::disk('public')->exists($this->imagen_path)) {
            return asset('storage/' . $this->imagen_path);
        }

        return asset('images/producto-placeholder.webp');
    }

    public function getDisponibleAttribute(): bool
    {
        return $this->activo && $this->stock_activo && $this->stock_actual > 0;
    }

    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format($this->precio, 2);
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('activo', true)
            ->where('stock_activo', true)
            ->where('stock_actual', '>', 0);
    }

    public function scopeDeSede(Builder $query, ?int $sedeId = null): Builder
    {
        if (!$sedeId) {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($sedeId) {
            $subQuery->where('sede_id', $sedeId)
                ->orWhereNull('sede_id');
        });
    }

    public function decrementarStock(int $cantidad, ?int $pedidoId = null, ?int $userId = null): void
    {
        if ($cantidad <= 0) return;

        $antes = $this->stock_actual;
        if ($antes < $cantidad) {
            throw new \Exception("Stock insuficiente para '{$this->nombre}'. Disponible: {$antes}, solicitado: {$cantidad}.");
        }

        $nuevoStock = max(0, $antes - $cantidad);
        $this->update([
            'stock_actual' => $nuevoStock,
            'stock_activo' => $nuevoStock > $this->stock_minimo,
        ]);

        StockMovimiento::create([
            'producto_id'   => $this->id,
            'user_id'       => $userId,
            'pedido_id'     => $pedidoId,
            'tipo'          => 'venta',
            'cantidad'      => -$cantidad,
            'stock_antes'   => $antes,
            'stock_despues' => $nuevoStock,
            'motivo'        => 'Venta por pedido',
        ]);

        // Desactivar promociones vinculadas si se queda sin stock
        if ($nuevoStock <= 0) {
            \App\Models\Promocion::where(function ($q) {
                $q->where('producto_id_1', $this->id)
                  ->orWhere('producto_id_2', $this->id)
                  ->orWhere('producto_id_3', $this->id);
            })->where('activo', true)->update(['activo' => false]);
        }
    }
}