<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'sede_id', 'area_venta_id', 'nombre', 'slug',
        'icono', 'imagen_path', 'orden', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function areaVenta(): BelongsTo
    {
        return $this->belongsTo(AreaVenta::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function scopeDeSede(Builder $query, int $sedeId): Builder
    {
        return $query->where(
            fn (Builder $subQuery) => $subQuery
                ->where('sede_id', $sedeId)
                ->orWhereNull('sede_id')
        );
    }
}