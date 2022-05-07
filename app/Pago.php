<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $guarded = [];
    public $timestamps = false;
    public function orden(){
    	return $this->belongsTo('App\Orden', 'id_orden');
    }

}
