<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class QrCuenta extends Model
{
    protected $fillable = [
        'sede_id',
        'nombre',
        'titular',
        'imagen_qr_path',
        'descripcion',
        'activo',
        'is_global',
        'is_future_module',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'is_global' => 'boolean',
            'is_future_module' => 'boolean',
        ];
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'qr_cuenta_id');
    }

    public function getQrUrlAttribute(): ?string
    {
        if ($this->imagen_qr_path && Storage::disk('public')->exists($this->imagen_qr_path)) {
            return asset('storage/' . $this->imagen_qr_path);
        }

        return null;
    }

    public static function activa(): ?self
    {
        return static::where('activo', true)->first();
    }
}