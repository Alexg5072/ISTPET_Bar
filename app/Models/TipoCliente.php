<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCliente extends Model
{
    protected $table = 'tipos_cliente';

    protected $fillable = [
        'nombre',
        'slug',
        'tipo',
        'descripcion',
        'permite_fiado',
        'activo',
        'is_future_module',
    ];

    protected $casts = [
        'permite_fiado' => 'boolean',
        'activo' => 'boolean',
        'is_future_module' => 'boolean',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}