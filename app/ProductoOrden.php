<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoOrden extends Model
{
    protected $table = 'productos_orden';
    protected $guarded = [];
    public $timestamps = false; 
    public function fotos(){
        return $this->hasMany('App\ProductoFotos', 'id_producto_orden');
    }
    public function dimension(){
    	return $this->belongsTo('App\Dimension', 'id_dimension');
    }
    public function producto(){
    	return $this->belongsTo('App\Producto', 'id_producto');
    }
    public function orden(){
        return $this->belongsTo('App\Orden', 'id_orden');
    }
    public function ordenamiento(){
        return $this->hasMany('App\TiraOrden', 'id_orden_producto');
    }
    protected $casts = [
        'id' => 'string'
    ];
}
