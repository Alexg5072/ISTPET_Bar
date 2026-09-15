<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model {
    protected $fillable = [
        'pedido_id','numero_comprobante','contenido_json',
        'pdf_path','impreso','impreso_at',
    ];
    protected $casts = [
        'contenido_json' => 'array',
        'impreso'        => 'boolean',
        'impreso_at'     => 'datetime',
    ];
    public function pedido() { return $this->belongsTo(Pedido::class); }

    public static function generarNumero(): string {
        $hoy   = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return 'COMP-'.$hoy.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}