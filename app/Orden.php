<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Orden extends Model
{
    use Notifiable;
    protected $table = 'orden';
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'id' => 'string',
        'iva' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'envio' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function productos_activos(){
        return $this->hasMany('App\ProductoOrden', 'id_orden')->where('estatus', 1);
    }
    public function productos(){
        return $this->hasMany('App\ProductoOrden', 'id_orden');
    }

    public function estatus(){
    	return $this->belongsTo('App\EstatusOrden', 'id_estatus');
    }

    public function pago(){
        return $this->hasOne('App\Pago', 'id_orden');
    }

    public function direccion(){
        return $this->belongsTo('App\DireccionEnvio', 'id_direccion');
    }
    public function usuario(){
        return $this->belongsTo('App\User', 'id_usuario');
    }

    public function guia(){
        return $this->hasOne('App\Guia', 'id_orden');
    }
}
