<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fuente extends Model
{
    protected $table = "fuentes";
    protected $hidden = [
        'id',
    ];
}
 