<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoFondos extends Model
{
    protected $table = "fondos_productos";
    public function fondo(){
    	return $this->belongsTo('App\Fondo', 'id_fondo');
    }
}
