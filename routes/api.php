<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*

*/
/*-------------------------------------------------------------------------------------------------------------*/
Route::post('/loginUsuario', 'LoginController@signIn');
Route::post('/crearUsuario', 'RegisterController');
Route::post('/editarUsuario', 'UsuarioController@editarUsuario');
Route::post('/recuperarClave', 'ForgotPasswordController@sendResetLinkEmail');
Route::get('/logoutUsuario/{id}', 'LoginController@singOut');
Route::get('/listarCategorias', 'Producto\ProductoController@listarCategorias');
Route::get('/listarProductosCategoria/{id}', 'Producto\ProductoController@listarProductosCategoria');
Route::get('/listarProducto/{id}', 'Producto\ProductoController@listarProducto');
/*-------------------------------------------------------------------------------------------------------------*/
Route::post('/crearOrden', 'Orden\OrdenController@crearOrden');
Route::post('/crearProducto', 'Orden\OrdenController@crearProducto');
Route::post('cargarFoto/{id}', 'Editor\CargarController@cargarFoto');
Route::post('agregarDiseno/{id}', 'Producto\ProductoController@agregarDiseno');
Route::get('listarFotos/{id}', 'Editor\CargarController@listarFotos'); 
Route::post('lapiz/{producto_orden}', 'Editor\EditorController@lapiz');
Route::post('guardarMarco', 'Editor\EditorController@guardarMarco');
Route::get('revertirCambios/{id}', 'Editor\CargarController@revertirCambios');
Route::get('ordenFotos/{id}', 'Producto\ProductoController@ordenFotos');
Route::get('previewFotos/{id}', 'Editor\CargarController@previewFotos');
Route::get('finalizarProducto/{id}', 'Editor\CargarController@finalizarProducto');
Route::post('ordenarFotos', 'Producto\ProductoController@ordenarFotos');
Route::get('listarMarcos/{id}', 'Producto\ProductoController@listarMarcos');
Route::get('restar/{producto_fotos}', function (App\ProductoFotos $producto_fotos){
	if($producto_fotos->cantidad > 1){
		$producto_fotos->cantidad--;
		$producto_fotos->save();
	}else{
		return response()->json([
			"status" => "error",
			"data" => "la cantidad debe ser igual o mayor a 1!"
		]);
	}
	return response()->json([
		"status" => "success",
		"data" => "la cantidad ha sido actualizada con exito!"
	]);
});
Route::get('sumar/{producto_fotos}', function (App\ProductoFotos $producto_fotos){
	$producto_fotos->cantidad++;
	$producto_fotos->save();
	return response()->json([
		"status" => "success",
		"data" => "la cantidad ha sido actualizada con exito!"
	]);
});

Route::get('ordenesUsuario/{id}', 'Usuario\UsuarioController@ordenes');
Route::get('indexCarrito/{id}', 'Pago\PagoController@ordenes');
Route::get('eliminarProducto/{producto_orden}', 'Pago\PagoController@eliminar');
Route::get('datosFactura/{id}', function($id){
	$user = App\User::find($id);
	return response()->json(
		[
			"status" => "success",
			"data" => $user
		]
	);
});

Route::get('aplicarCupon/{cupon}', function ($cupon){
	$cupon = App\Cupon::where('codigo', $cupon)->first();
	if($cupon && $cupon->estatus == 1){
		return response()->json([
			"status" => "success",
			"data" => [
				"cupon" => [
					"id" => $cupon->id,
					"monto" => $cupon->monto
				]
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

Route::get('listarCiudades', function (){
	$provincias = App\Provincia::where('id',">", 0)->with('ciudades')->get();
	return response()->json([
		"status" => "success",
		"data" => $provincias
	]);
});
Route::get('calcularEnvio/{ciudad}', "Pago\GuiaController@calcular");
Route::get('datosFactura/{id}', "Pago\PagoController@datosFactura");
Route::post('pagarOrden', "Pago\PagoController@pagarOrden");
Route::get('token', "TokenController@token");
Route::get('token2', "TokenController@token2");  

//Route::put('pago/{orden}', 'Pago\PagoController@pagar');

 
