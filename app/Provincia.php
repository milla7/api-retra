<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';
    
	public function ciudades(){
        return $this->hasMany('App\Ciudad', 'id_provincia');
    }
}
