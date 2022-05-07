<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
class TestController extends Controller
{
    public function test(){
    	$producto = Producto::find(11);
    	dd($producto->fondos[0]->fondo);
    }
    
}
