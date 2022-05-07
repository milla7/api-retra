<?php

namespace App\Http\Controllers\Pago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ciudad;
class GuiaController extends Controller
{
	public function calcular( Ciudad $ciudad){
		$precio = 5.25;
		if( $ciudad->rapid == 1 ){
			$data = $this->login();
		    $r = $this->crearGuia( $data, $ciudad );
		    if( isset( $r['precio_total'] ) ){
		    	$precio = $r['precio_total'];
		    }
		}
	    return response()->json([
			"status" => "success",
			"data" => [
				"precio" => $precio
			]
		]);
	}
  	public function login(){
	    $post = array(
	        'mail' => env('RAPID_MAIL'),
	        'pass' => env('RAPID_PASS'),
	    );
	    $post = json_encode($post);
	    $ch = curl_init('https://www.rapidservice.com.ec/api/user/login');
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	    $response = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($response, true);
	    return $data;
  	}
  	public function  crearGuia( $data, $ciudad ){
	    $post = array(
	      "cliente_nid" =>   "357633",
	      "remitente_nid" => "357634",
	      "ciudad_destino" => $ciudad->nombre,
	      "crear_guia" => "NO",
	      "flete_al_cobro" => "NO",
	      "asegurar" => "NO",
	      "retirar_en_oficina" => "NO",
	      "destinatario" => array(
	        "nombre" => "Juan Perez A",
	        "tipo_identificacion" => "cedula",
	        "identificacion" => "0104151469",
	        "telefono" => "0987647110",
	        "direccion" => "Gonzalez Suarez",
	        "correo" => "juan@mail.com"
	      ),
	      "paquetes" => 
	      array(
	        array(
	          "descripcion" => "sobre",
	          "valor_declarado" => "0",
	          "cantidad" => "1",
	          "producto" => "882"
	        )
	      )
	    );
	    $post = json_encode($post);
	    $ch = curl_init('https://www.rapidservice.com.ec/api/guia/guiaexterna');
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        'Content-Type: application/json',
	        'Accept: application/json',
	        'Cookie: SSESScb35979250496bc54f53ed20dcb541c7=' . $data["sessid"],
	        'X-CSRF-Token: ' . $data["token"],
	    ));
		$response = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($response, true);
	    return $data;
	}
}
