<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoFotos extends Model
{
    protected $table = 'fotos_producto';
    protected $fillable = ['nombre', 'id_producto_orden', 'cantidad', 'fondo', 'fuente', 'texto', 'foto', 'tipo', 'foto_original'];
    protected $guarded = [];
    public $timestamps = false;
    public function productoOrden(){
    	return $this->belongsTo('App\ProductoOrden', 'id_producto_orden');
    }
    
}
