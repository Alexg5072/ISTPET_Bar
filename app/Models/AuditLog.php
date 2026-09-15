<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'accion',
        'modelo',
        'modelo_id',
        'datos_antes',
        'datos_despues',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'datos_antes' => 'array',
            'datos_despues' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(
        string $accion,
        ?string $modelo = null,
        ?int $modeloId = null,
        ?array $antes = null,
        ?array $despues = null
    ): void {
        static::create([
            'user_id' => Auth::id(),
            'accion' => $accion,
            'modelo' => $modelo,
            'modelo_id' => $modeloId,
            'datos_antes' => $antes,
            'datos_despues' => $despues,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}