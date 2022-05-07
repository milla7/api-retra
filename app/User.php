<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable; 
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'usuarios';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'clave', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'id' => 'string'
    ];

    public $timestamps = false;

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function ordenes(){
        return $this->hasMany('App\Orden', 'id_usuario')->whereHas('pago');
    }
}
