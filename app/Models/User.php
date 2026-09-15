<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'sede_id', 'tipo_cliente_id',
        'cedula', 'telefono',
        'carrera', 'tipo_sede',
        'activo', 'ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acceso'     => 'datetime',
        'activo'            => 'boolean',
        'password'          => 'hashed',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function tipoCliente()
    {
        return $this->belongsTo(TipoCliente::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function fiado()
    {
        return $this->hasOne(Fiado::class);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        return strtoupper(
            substr($words[0], 0, 1) .
            (isset($words[1]) ? substr($words[1], 0, 1) : '')
        );
    }

    /**
     * Primer nombre del usuario (para saludos en el kiosco)
     */
    public function getPrimerNombreAttribute(): string
    {
        return explode(' ', trim($this->name))[0];
    }

    public function registrarAcceso(): void
    {
        $this->update(['ultimo_acceso' => now()]);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeUsuarios(Builder $query): Builder
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'usuario'));
    }
}