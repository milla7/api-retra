<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DireccionEnvio extends Model
{
    protected $table = 'direcciones_envio';
    protected $guarded = [];
    public $timestamps = false;
    public function ciudad(){
    	return $this->belongsTo('App\Ciudad', 'id_ciudad');
    }
}
