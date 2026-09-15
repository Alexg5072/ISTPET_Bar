<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ComboItem extends Model {
    protected $fillable = ['combo_id','producto_id','cantidad'];
    public function combo()    { return $this->belongsTo(Combo::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}