<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', 'TestController@test');
Route::get('/add-card', function () {
    return view('add');
});
/*
Route::get('/mis-pedidos', 'Usuario\UsuarioController@ordenes');
Route::get('pagar', 'Pago\PagoController@ordenes');
Route::get('sub-orden/{producto_orden}/eliminar', 'Pago\PagoController@eliminar');
Route::get('datos-factura', function(){
	$user = Auth::user();
	return response()->json(
		[
			"status" => "success",
			"data" => $user
		]
	);
});
Route::get('cupon/{cupon}', function ($cupon){
	$cupon = App\Cupon::where('codigo', $cupon)->first();
	if($cupon && $cupon->estatus == 1){
		return response()->json([
			"status" => "success",
			"data" => [
				"cupon" => $cupon
			]
		]);
	}else{
		return response()->json([
			"status" => "error",
			"data" => [
				"error" => 'Cupon no encontrado'
			]
		]);
	}
});
Route::get('calcular-envio/{ciudad}', "Pago\GuiaController@calcular");
Route::put('/pago/{orden}', 'Pago\PagoController@pagar');
*/