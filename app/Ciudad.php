<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    
    public function provincia(){
        return $this->belongsTo('App\Provincia', 'id_provincia');
    }
    protected $casts = [
        'precio' => 'decimal:2',
    ];
    protected $hidden = [
        'precio', 'rapid',
    ];
}
