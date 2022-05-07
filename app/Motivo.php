<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    protected $table = "motivos";
    public function imagenes(){
        return $this->hasMany('App\ImagenesMotivo', 'id_motivo');
    }
}
 