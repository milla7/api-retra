<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marco extends Model
{
    protected $table = 'marcos';
    protected $guarded = [];
    public $timestamps = false;
    protected $hidden = [
        'id', 'id_producto', 'tipo'
    ];
}
