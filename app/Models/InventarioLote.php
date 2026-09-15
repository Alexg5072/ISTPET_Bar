<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/** MÓDULO EXTRA — solo estructura preparada. Lógica pendiente. */
class InventarioLote extends Model {
    protected $fillable = [
        'producto_id','area_venta_id','numero_lote',
        'cantidad_inicial','cantidad_actual',
        'fecha_ingreso','fecha_vencimiento',
        'costo_unitario','activo','is_future_module',
    ];
    protected $casts = [
        'fecha_ingreso'    => 'date',
        'fecha_vencimiento'=> 'date',
        'costo_unitario'   => 'decimal:2',
        'activo'           => 'boolean',
        'is_future_module' => 'boolean',
    ];
    public function producto()  { return $this->belongsTo(Producto::class); }
    public function areaVenta() { return $this->belongsTo(AreaVenta::class); }
}