<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockMovimiento extends Model {
    protected $fillable = [
        'producto_id','user_id','pedido_id',
        'tipo','cantidad','stock_antes','stock_despues','motivo',
    ];
    public function producto() { return $this->belongsTo(Producto::class); }
    public function user()     { return $this->belongsTo(User::class); }
    public function pedido()   { return $this->belongsTo(Pedido::class); }
}