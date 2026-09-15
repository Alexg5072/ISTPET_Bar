<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaVenta extends Model
{
    protected $table = 'areas_venta';

    protected $fillable = [
        'sede_id',
        'nombre',
        'slug',
        'tipo',
        'descripcion',
        'activo',
        'is_future_module',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'is_future_module' => 'boolean',
    ];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}