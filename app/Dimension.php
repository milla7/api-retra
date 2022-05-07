<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    protected $table = "productos_dimensiones";
    public function precios(){
        return $this->hasMany('App\ProductoPrecios', 'id_dimension');
    }
    
    public function getSoloDimensionAttribute()
    {
        $data = explode('-', $this->dimension);
        $data = $data[1];
        return "{$data}";
    }
}
