<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiraOrden extends Model
{
   protected $table = 'reorder';
    protected $guarded = [];
    public $timestamps = false;
    public function suborden(){
    	return $this->belongsTo('App\ProductoOrden', 'id_orden_producto');
    }
}
