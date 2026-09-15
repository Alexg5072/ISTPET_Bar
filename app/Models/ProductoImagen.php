<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductoImagen extends Model {
    protected $fillable = ['producto_id','path','orden','es_principal'];
    protected $casts = ['es_principal'=>'boolean'];
    public function producto() { return $this->belongsTo(Producto::class); }
    public function getUrlAttribute(): string {
        return Storage::disk('public')->exists($this->path)
            ? asset('storage/'.$this->path)
            : asset('images/producto-placeholder.webp');
    }
}