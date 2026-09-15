<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model {

    protected $table = 'configuraciones';

    protected $fillable = ['clave','valor','tipo','descripcion','grupo'];

    /**
     * Obtener valor casteado según tipo
     */
    public function getValorCasteadoAttribute(): mixed {
        return match($this->tipo) {
            'boolean' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->valor,
            'json'    => json_decode($this->valor, true),
            default   => $this->valor,
        };
    }

    /**
     * Obtener una configuración por clave con valor casteado
     */
    public static function get(string $clave, mixed $default = null): mixed {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor_casteado : $default;
    }

    /**
     * Establecer una configuración
     */
    public static function set(string $clave, mixed $valor): void {
        static::updateOrCreate(
            ['clave' => $clave],
            ['valor' => is_bool($valor) ? ($valor ? 'true' : 'false') : (string) $valor]
        );
    }

    /**
     * Verificar si un módulo extra está habilitado
     */
    public static function moduloHabilitado(string $modulo): bool {
        return static::get("modulo.{$modulo}", false) === true;
    }
}