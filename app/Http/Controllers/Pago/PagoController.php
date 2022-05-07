<?php

namespace App\Http\Controllers\Pago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Orden;
use Illuminate\Support\Facades\Auth;
use App\Provincia;
use App\Pago;
use Illuminate\Support\Facades\Session;
use App\DireccionEnvio;
use App\Notifications\Detalle;
use Illuminate\Support\Facades\Notification;
use App\User;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\File;
use App\Cupon;
use App\ProductoOrden;
use App\Guia;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    public function index(Orden $orden){
        $orden->sub_total = 0;
        foreach ($orden->productos as $producto) {
            if($producto->estatus == 1){
                $orden->sub_total = $orden->sub_total + $producto->total;
            }
        }
        $orden->iva = $orden->sub_total * 0.12;
        $orden->total = $orden->iva + $orden->sub_total;
        $orden->save();
    	return view('panel.carrito.index_pago', compact('orden'));
    }

    public function pagarOrden(Request $request){

        $validator = Validator::make($request->all(), [
            "id" => 'present',
            /////////////////////////////////////////////////////
            "iva" => 'present',
            "total" => "present",
            "precio_envio" => "present",
            "cupon_aplicado" => "present",
            ////////////////////////////////////////////////////
            "nombres" => 'present',
            "documento" => "present",
            "direccion" => "present",
            "celular" => "present",
            "email" => 'present',
            "tipo_pago" => "present",
            ////////////////////////////////////////////////////
            "transaction_reference" => "present",
            "authorization_code" => "present",
            ////////////////////////////////////////////////////
            "nombres_envio" => 'present',
            "documento_envio" => "present",
            "telefono_envio" => "present",
            "mail_envio" => "present",
            "ciudad" => 'present',
            "calle_envio" => "present",
            "casa_envio" => "present",
            "calle2_envio" => "present",
            "referencia_envio" => "present",
            "comentarios_envio" => "present"
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }    
        $orden = Orden::find($request->id);
        $this->elimarProductos($orden);
        if(!$orden->pago){
            $orden->update([
                "iva" => $request->iva,
                "total" => $request->total,
                "costo_envio" => $request->precio_envio
            ]);
            $pago = Pago::create([
                "id_orden" => $request->id,
                "nombres" => $request->nombres,
                "documento" => $request->documento,
                "direccion" => $request->direccion,
                "telefono" => $request->celular,
                "email" => $request->email,
                "monto" => $request->total,
                "tipo" => $request->tipo_pago,
                "transaction_reference" => $request->transaction_reference,
                "authorization_code" => $request->authorization_code,
            ]);
            if( $request->tipo_pago == 1 ){
                $pago->estatus = 1; 
            }else{
                $pago->estatus = 0;
                $orden->id_estatus = 3;
                $orden->save();
            } 
            $pago->save();
            if( $request->oficina == 0){
                $direccion = DireccionEnvio::create([
                    "nombres" => $request->nombres_envio,
                    "cedula" => $request->documento_envio,
                    "telefono" => $request->telefono_envio,
                    "email" => $request->mail_envio,
                    "id_ciudad" => $request->ciudad,
                    "calle_principal" => $request->calle_envio,
                    "numero_casa" => $request->casa_envio,
                    "calle_secundaria" => $request->calle2_envio,
                    "referencia" => $request->referencia_envio,
                    "comentario" => $request->comentarios_envio,
                    //"forma_entrega" => $request->forma_entrega
                ]);
            }else{
                $direccion = DireccionEnvio::create([
                    "nombres" => $request->nombres,
                    "cedula" => $request->documento,
                    "telefono" => $request->celular,
                    "email" => $request->email,
                    "forma_entrega" => "Recogida en Oficina",
                    "comentario" => $request->comentarios_envio,
                ]);
            }
            $pago->orden->id_direccion = $direccion->id;
            $pago->orden->save();
            $this->cupon($request);
            $orden = Orden::find($request->id);
            if( $request->oficina == 0 ){
                $this->calcular($orden);
            }
        }
        $orden = Orden::find($request->id);
        $user = User::find($orden->id_usuario);

        
        Notification::send($user, new Detalle($orden)); 
        Notification::route('mail', 'sofia@laretrateriaec.com')->notify( new Detalle($orden) );
        $this->dropbox($orden);
        return response()->json([ 
            "status" => "success",
            "data" => $orden->productos_activos
        ]);
        //return view('panel.carrito.success_tdc', compact('orden'));
    }

    public function ordenes($id){
        $orden = Orden::where('id_usuario', $id)->orderBy('id', 'desc')->first();
        if($orden->pago){
            return response()->json([
                "status" => "error",
                "data" => "no hay datos disponibles"
            ]);
        }
        $orden->sub_total = 0;
        foreach ($orden->productos as $producto) {
            if($producto->estatus == 1){
                $orden->sub_total = $orden->sub_total + $producto->total;
            }
            /*$producto->producto;
            $producto->dimension;*/
            $producto["cantidad"] = 0;
            $productoOrden = ProductoOrden::find($producto->id);
            foreach ($productoOrden->fotos as $foto) {
                $producto["cantidad"] = $producto["cantidad"] + $foto->cantidad;
                $producto["precio_unitario"] = round( $productoOrden->total / $producto["cantidad"], 2 );
                $producto["nombre_producto"] = $productoOrden->producto->nombre . " " . $productoOrden->dimension->dimensiones; 
                $producto["imagen_producto"] = 'assets/productos/' . $productoOrden->producto->img_1;
            }
        }
        $orden->iva = $orden->sub_total * 0.12;
        $orden->total = $orden->iva + $orden->sub_total;
        $orden->save();
        $orden["porcentaje_iva"] = 12;

        return response()->json([
            "status" => "success",
            "data" => $orden
            
        ]);
    }

    public function dropbox($orden){
        $this->zip($orden);
        $path = 'archivos/' . $orden->numero_orden .'.zip';
        $fp = fopen($path, 'rb');
        $size = filesize($path);

        $cheaders = array('Authorization: Bearer kGfHUEAG0bMAAAAAAAAAATEfxjtOPFzwMJvMKY99tgN8CSHVE3U_Fn79Q4pGNSve',
                          'Content-Type: application/octet-stream',
                          'Dropbox-API-Arg: {"path":"/ORDENES/' . $orden->numero_orden . '.zip", "mode":"add", "autorename": true, "mute": false, "strict_conflict": false}');

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        
        fclose($fp);
    }

    public function zip($orden){

        File::copyDirectory( public_path() .'/assets/clientes/' . $orden->numero_orden , public_path() . "/" . $orden->numero_orden);
        $zip = new ZipArchive();
        $dir =  $orden->numero_orden . '/';
        $rutaFinal = "archivos";
        if(!file_exists($rutaFinal)){
        mkdir($rutaFinal);
        }
        $archivoZip = $orden->numero_orden . ".zip";

        if ($zip->open($archivoZip, ZIPARCHIVE::CREATE) === true) {
        $this->agregar_zip($dir, $zip);
        $zip->close();
        rename($archivoZip, "$rutaFinal/$archivoZip");
        }
        File::deleteDirectory( public_path() .'/' . $orden->numero_orden);
    }


    public function agregar_zip($dir, $zip) {
      if (is_dir( public_path() . "/" . $dir )) {
        if ($da = opendir($dir)) {
          while (($archivo = readdir($da)) !== false) {
            if (is_dir($dir . $archivo) && $archivo != "." && $archivo != "..") {
              $this->agregar_zip($dir . $archivo . "/", $zip);
            } elseif (is_file($dir . $archivo) && $archivo != "." && $archivo != "..") {
              $zip->addFile($dir . $archivo, $dir . $archivo);
            }
          }
          closedir($da);
        }
      }
    }


    public function elimarProductos($orden){
        foreach ($orden->productos as $producto) {
            if($producto->estatus == 0){
                $carpeta = $producto->producto->nombre . '/' . $producto->id;
                File::deleteDirectory( public_path() .'/assets/clientes/' . $orden->numero_orden . "/" . $carpeta );
                $producto->delete();
            }
        }
    }

    public function cupon($request){
        if($request->cupon_aplicado == 1 || $request->cupon_aplicado == '1'){
            $cupon = Cupon::where('codigo', $request->cupon)->first();
            $cupon->estatus = 0;
            $cupon->save();
        }
    }

    public function eliminar(ProductoOrden $producto_orden){
        $producto_orden->update([
            "estatus" => 0
        ]);
        return response()->json([
            "status" => "succes",
            "data" => "el producto ha sido eliminado con exito!"
        ]);

    }

    public function calcular( $orden){
        $precio = 5.25;
        if( $orden->direccion->ciudad->rapid == 1 ){
            $data = $this->login();
            $r = $this->crearGuia( $data, $orden );
            if( isset( $r['precio_total'] ) ){
                Guia::create([
                    "id_orden" => $orden->id,
                    "precio_total" => $r["precio_total"],
                    "precio_subtotal" => $r["precio_subtotal"],
                    "qr" => $r["qr"],
                    "rastreo" => $r["rastreo"],
                    "etiquetas" => $r["etiquetas"]
                ]);
            }
        }
        
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
    public function  crearGuia( $data, $orden ){

        $post = array(
          "cliente_nid" =>   "357633",
          "remitente_nid" => "357634",
          "ciudad_destino" => $orden->direccion->ciudad->nombre,
          "crear_guia" => "SI",
          "flete_al_cobro" => "NO",
          "asegurar" => "NO",
          "retirar_en_oficina" => "NO",
          "destinatario" => array(
            "nombre" => $orden->direccion->nombres,
            "tipo_identificacion" => "cedula",
            "identificacion" => $orden->direccion->cedula,
            "telefono" => $orden->direccion->telefono,
            "direccion" => $orden->direccion->calle_principal . " " . $orden->direccion->calle_principal . ", " . $orden->direccion->numero_casa . ", " . $orden->direccion->referencia,
            "correo" => $orden->direccion->email
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

    public function datosFactura($id){
        $user = User::find($id);
        return response()->json(
            [
                "status" => "success",
                "data" => [
                    "usuario" => $user
                ]
            ]
        );
    }

}
