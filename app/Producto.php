<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = "productos";
    public function categoria(){
    	return $this->belongsTo('App\Categoria', 'id_categoria');
    }
    public function dimensionesSolo(){
        return $this->hasMany('App\Dimension', 'id_producto');
    }
    public function iconos(){
        return $this->hasMany('App\Icono', 'id_producto');
    }

    public function dimensiones(){
        return $this->hasMany('App\Dimension', 'id_producto')->with('precios');
    }

    public function fondos(){
        return $this->hasMany('App\ProductoFondos', 'id_producto');
    }
    public function marcos(){
        return $this->hasMany('App\Marco', 'id_producto');
    }
}
