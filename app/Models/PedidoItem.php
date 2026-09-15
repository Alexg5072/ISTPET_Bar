<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model {
    protected $fillable = [
        'pedido_id','producto_id','combo_id',
        'nombre_snapshot','precio_snapshot','cantidad','subtotal',
    ];
    protected $casts = [
        'precio_snapshot' => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];
    public function pedido()   { return $this->belongsTo(Pedido::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
    public function combo()    { return $this->belongsTo(Combo::class); }
}