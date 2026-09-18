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
        $hoy = now()->format('Ymd');
        $ultimos = static::whereDate('created_at', today())
            ->orderByDesc('id')
            ->pluck('numero_comprobante');

        $maxNumero = 0;
        foreach ($ultimos as $num) {
            $partes = explode('-', (string) $num);
            $n = isset($partes[2]) ? (int) $partes[2] : 0;
            if ($n > $maxNumero) $maxNumero = $n;
        }

        do {
            $maxNumero++;
            $numero = 'COMP-' . $hoy . '-' . str_pad($maxNumero, 4, '0', STR_PAD_LEFT);
        } while (static::where('numero_comprobante', $numero)->exists());

        return $numero;
    }
}