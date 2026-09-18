<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Combo extends Model
{
    protected $fillable = [
        'sede_id', 'nombre', 'slug', 'descripcion',
        'precio', 'imagen_path', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function items()
    {
        return $this->hasMany(ComboItem::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'combo_items')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen_path && Storage::disk('public')->exists($this->imagen_path)) {
            return asset('storage/' . $this->imagen_path);
        }

        return asset('images/combo-placeholder.webp');
    }

    public function getDisponibleAttribute(): bool
    {
        if (! $this->activo) {
            return false;
        }

        if (! $this->relationLoaded('items')) {
            return true; // si no hay items cargados, asumimos disponible
        }

        return $this->items->every(fn ($item) => $item->producto?->disponible);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeDeSede(Builder $query, ?int $sedeId = null): Builder
    {
        if (!$sedeId) {
            return $query;
        }

        return $query->where(fn ($q) => $q->where('sede_id', $sedeId)->orWhereNull('sede_id'));
    }
}