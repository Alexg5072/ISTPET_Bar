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
        if ($this->imagen_qr_path) {
            if (Storage::disk('public')->exists($this->imagen_qr_path)) {
                return asset('storage/' . $this->imagen_qr_path);
            }
            if (file_exists(public_path($this->imagen_qr_path))) {
                return asset($this->imagen_qr_path);
            }
            if (file_exists(public_path('storage/' . $this->imagen_qr_path))) {
                return asset('storage/' . $this->imagen_qr_path);
            }
        }

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (file_exists(public_path("images/qr/DEUNA.{$ext}"))) {
                return asset("images/qr/DEUNA.{$ext}");
            }
            if (file_exists(public_path("storage/qr/DEUNA.{$ext}"))) {
                return asset("storage/qr/DEUNA.{$ext}");
            }
            if (Storage::disk('public')->exists("qr/DEUNA.{$ext}")) {
                return asset("storage/qr/DEUNA.{$ext}");
            }
        }

        return null;
    }

    public static function activa(): ?self
    {
        return static::where('activo', true)->first();
    }
}