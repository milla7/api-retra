<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Producto;
use App\Orden;
use Illuminate\Support\Facades\Session;
use DateTime;
use App\ProductoOrden;
use App\ProductoFotos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\TiraOrden;

class CargarController extends Controller
{
    public function index(Request $request){

    	if(!Session::has('orden')){
    		$fecha = new DateTime();

    		$orden = Orden::create([
    			'fecha' => $fecha->format('Y-m-d'),
    			'total' => '10'
    		]);
    		$orden->numero_orden = 'ORDEN-' . $orden->id;
    		$orden->save();
    		Session::put('orden', $orden->id);
    	}else{
    		$orden = Orden::find(Session::get('orden'));
    	}
    	$producto = Producto::find($request->id);
        $suborden = ProductoOrden::create([
            "id_orden" => $orden->id,
            "id_producto" => $producto->id,
            "id_dimension" => $request->dimension,
            "etiqueta" => $request->etiqueta
        ]);

        if( Auth::user() ){
            $orden->id_usuario = Auth::user()->id;
            $orden->save();
        }
        if( $request->id == 1 || $request->id == 2 || $request->id == 3 || $request->id == 11 ){
            return view('panel.editor.diseno', compact('suborden'));
        }else{
            $dimension = explode("-", $suborden->dimension->dimension);
            $dimension = $dimension[1];
            $this->guardarDimension($suborden, $dimension);
    	   return view('panel.editor.cargar', compact('producto', 'orden', 'dimension', 'suborden'));
        }
    }

    public function guardarDiseno(Request $request){
        $suborden = ProductoOrden::find($request->id);
        /*$data = $this->validate_diseno($request, $suborden);
        if(!$data->passes()){
            return back()->withErrors($data)->withInput();
        }*/
        $carpeta_orden = 'assets/clientes/'. $suborden->orden->numero_orden . "/" . $suborden->producto->nombre . "/" . $suborden->id . "/";
        if (!file_exists($carpeta_orden)) {
            mkdir($carpeta_orden, 0777, true);
        }

        if(isset($request['fondo_2'])){
            $contenido  = "Fondo 1: ".$request['fondo'];
            $contenido .= "\nFondo 2: ".$request['fondo_2'];
        }else{
            $contenido  = "Fondo: ".$request['fondo'];
        }
        if ($request['motivo']!="undefined"){
            $contenido .= "\nMotivo: ".$request['motivo'];
            $contenido .= "\nMotivo Imagen: ".$request['motivo_img'];
            $contenido .= "\nMensaje: ".$request['mensaje'];
            $contenido .= "\nEtiqueta: ".$suborden->etiqueta;
        }
        if (file_exists($carpeta_orden."/datos.txt")){
           /* $archivo = fopen("tmp/datos.txt", "a");
            fwrite($archivo, PHP_EOL ."$contenido");
            fclose($archivo);*/
        }
        else{
            $archivo = fopen($carpeta_orden."/datos.txt", "w");
            fwrite($archivo, PHP_EOL ."$contenido");
            fclose($archivo);
        }
        $producto = $suborden->producto;
        $orden = $suborden->orden;
        $dimension = '10';
        return view('panel.editor.cargar', compact('producto', 'orden', 'dimension', 'suborden'));
    }
    public function copiarCarpeta( ProductoOrden $producto_orden ){
        File::copyDirectory( 
            public_path() .'/assets/clientes/' . $producto_orden->orden->numero_orden . "/" . $producto_orden->producto->nombre . "/" . $producto_orden->id, 
            public_path() . "/originales/" . $producto_orden->id
        );
        File::copyDirectory( 
            public_path() .'/assets/clientes/' . $producto_orden->orden->numero_orden . "/" . $producto_orden->producto->nombre . "/" . $producto_orden->id, 
            public_path() . "/ediciones/" . $producto_orden->id
        );
    }
    public function guardarDimension($suborden, $dimension){
        $carpeta_orden = 'assets/clientes/'. $suborden->orden->numero_orden . "/" . $suborden->producto->nombre . "/" . $suborden->id . "/";
        if (!file_exists($carpeta_orden)) {
            mkdir($carpeta_orden, 0777, true);
        }
        $contenido  = "Dimension: ". $dimension;
        $contenido .= "\nEtiqueta: ".$suborden->etiqueta;
        $archivo = fopen($carpeta_orden."/datos.txt", "w");
        fwrite($archivo, PHP_EOL ."$contenido");
        fclose($archivo);
    }
    public function cargarFoto(Request $request, $id /*Orden $orden, Producto $producto, ProductoOrden $producto_orden*/){
        $producto_orden = ProductoOrden::find($id);
        $orden = Orden::find($producto_orden->id_orden);
        $producto = Producto::find($producto_orden->id_producto);
        //dd($producto);
        $destinationPath = base_path() . '/public/assets/clientes/' . $orden->numero_orden . "/" . $producto->nombre . "/" . $producto_orden->id . "/";
        $src = 'assets/clientes/' . $orden->numero_orden . "/" . $producto->nombre . "/" . $producto_orden->id . "/";
        $archivo = $request->file('imagen')->getClientOriginalName();
        $documento = pathinfo($archivo,PATHINFO_FILENAME);
        $extension = pathinfo($archivo,PATHINFO_EXTENSION);
        $permitted_chars = '0123456789';
        $producto_foto = ProductoFotos::create([
            "id_producto_orden" => $producto_orden->id,
            "foto_original" => ""
        ]);
        $nombre = 'IMG-'. substr(str_shuffle($permitted_chars), 0, 6) . "-" . $producto_foto->id . "." . $extension;
        $nombre_copia = 'ORIGINAL-'. substr(str_shuffle($permitted_chars), 0, 6) . "-" . $producto_orden->id . "." . $extension;
        $file =  $request->file('imagen');
        $file->move($destinationPath, $nombre);
        $producto_foto->foto_original =  $src . $nombre;
        $producto_foto->nombre = $nombre;
        $producto_foto->save();
        if($producto_orden->id_producto == 8 || $producto_orden->id_producto == 11){
            $cantidad = TiraOrden::where('id_orden_producto', $producto_orden->id)->count();
            TiraOrden::create([
                "image_name" => $producto_foto->foto_original,
                "nombre" => $producto_foto->nombre,
                "display_order" => $cantidad,
                "id_orden_producto" => $producto_orden->id
            ]);
            $cantidad = $cantidad + 1;
        }
        $e = exif_read_data( $destinationPath . $nombre );
        if( isset($e["Orientation"]) ){
            $r = $e["Orientation"];
        }else{
            $r = null;
        }

        $origen = imagecreatefromjpeg($destinationPath . $nombre) or die('Error opening file '. $destinationPath . $nombre);
        $this->adjustImageOrientation($destinationPath . $nombre);
        $this->copiarCarpeta($producto_orden);
        return response()->json([
            "status" => "success",
            "data" => "La imagen se cargo con exito."
        ]);
        
    }
    public function reflejarImagen ($imagenOriginal) {
            $anchura = imagesx ($imagenOriginal);
            $altura = imagesy ($imagenOriginal);
 
            $origenDeX = $anchura -1;
            $origenDeY = 0;
            $anchura_original = -$anchura;
            $altura_original = $altura;
 
            $imagenDeDestino = imagecreatetruecolor ($anchura, $altura);
 
            if (imagecopyresampled ($imagenDeDestino, $imagenOriginal, 0, 0, $origenDeX, $origenDeY, $anchura, $altura, $anchura_original, $altura_original)) return $imagenDeDestino;
 
            return $imagenOriginal;
        }
     
    public static function adjustImageOrientation($ficheroDeImagen) {            
        $codificacionExif = exif_read_data($ficheroDeImagen);
        if($codificacionExif && isset($codificacionExif['Orientation'])) {
            $orientacion = $codificacionExif['Orientation'];
            if($orientacion != 1){
                $imagenEnProceso = imagecreatefromjpeg($ficheroDeImagen);
                $reflejo = false;
                $grados = 0;
                switch ($orientacion) {
                    case 2:
                        $reflejo = true;
                        break;
                    case 3:
                        $grados = 180;
                        break;
                    case 4:
                        $grados = 180;
                        $reflejo = true; 
                        break;
                    case 5:
                        $grados = 270;
                        $reflejo = true; 
                        break;
                    case 6:
                        $grados = 270;
                        break;
                    case 7:
                        $grados = 90;
                        $reflejo = true; 
                        break;
                    case 8:
                        $grados = 90;
                        break;
                }
                if ($grados) $imagenEnProceso = imagerotate($imagenEnProceso, $grados, 0); 
                if ($reflejo) $imagenEnProceso = self::reflejarImagen($imagenEnProceso);
                imagejpeg($imagenEnProceso, $ficheroDeImagen);
            }
        }
        return true;
    }
    public function listarFotos(Request $request, $id){
        /*
        $tipo = explode("-",$producto_orden->dimension->dimension);
        $tipo = $tipo[2];
        */
        $producto_orden = ProductoOrden::find($id);
        $fotos = $producto_orden->fotos;
        foreach ($fotos as $foto) {
            $foto->foto_original = 'ediciones/'. $id . '/' . $foto->nombre;
        }
        $tipo = explode('-',$producto_orden->dimension->dimension);
        $tipo = $tipo[2];
        $data["fotos"] = $fotos;
        $data["marco"] = "no";
        $data["ordenar"] = "no";
        $data["cantidad"] = "no";
        if($tipo =='2' OR $tipo =='3' OR $tipo =='7' OR $tipo =='9' OR $tipo =='11' OR $tipo =='14'){
            $data["marco"] = "si";
        }
        if($producto_orden->id_producto == 8 || $producto_orden->id_producto == 11){
            $data["ordenar"] = "si";
            foreach ( $producto_orden->ordenamiento as $foto) {
                $foto->image_name = "ediciones/" . $foto->id_orden_producto . "/" . $foto->nombre;
            }
            $data["orden"] = $producto_orden->ordenamiento;
        }
        if( $producto_orden->id_producto == 4 || $producto_orden->id_producto == 5  || $producto_orden->id_producto == 6 ){
             $data["cantidad"] = "si";
        }
        return response()->json([
            "status" => "success",
            "data" => $data
        ]);
    }

    public function previewFotos($id){
        $producto_orden = ProductoOrden::find($id);
        File::copyDirectory( 
            public_path() . "/ediciones/" . $producto_orden->id,
            public_path() .'/assets/clientes/' . $producto_orden->orden->numero_orden . "/" . $producto_orden->producto->nombre . "/" . $producto_orden->id 
        );
        $orden = $this->procesarImg($producto_orden);
        $tiras = [];
        if($producto_orden->producto->id == 11 || $producto_orden->producto->id == 8){
            foreach(glob('./'. 'assets/clientes/' . $producto_orden->orden->numero_orden . "/" . $producto_orden->producto->nombre . "/" . $producto_orden->id .'/*.j*') as $filename){
                $r = explode("/", $filename);
                $tiras[] = $r[6];
            }
            foreach ($tiras as $key => $value) {
                $d = 'assets/clientes/' . $producto_orden->orden->numero_orden . "/" . $producto_orden->producto->nombre . "/" . $producto_orden->id .'/' . $value;
                $tiras[$key] = [
                    "id" => 19,
                    "nombre" => "test.jpg",
                    "id_producto_orden" => 3,
                    "cantidad" => 1,
                    "fondo" => "assets/fondos/7x10/pattern/7x10-2.jpg",
                    "fuente" => "assets/fuentes/ttf/font-2.ttf",
                    "texto" => "test",
                    "foto" => null,
                    "tipo" => null,
                    "foto_original" => $d
                ];
            }


        }else{
            $tiras = ProductoFotos::where('id_producto_orden', $producto_orden->id)->get();
        }
        return response()->json([
            "status" => "success",
            "data" => [
                "fotos" => $tiras
            ]
        ]);
        //return view("panel.editor.preview_general", compact("producto_orden", "tiras"));

    }

    public function procesarImg($producto_orden){
        $orden = Orden::find($producto_orden->id_orden);
        $tipo =  explode('-', $producto_orden->dimension->dimension);
        $tipo = $tipo[2];
        if($tipo == '5' || $tipo == '15'){
            foreach($producto_orden->fotos as $foto){
                $dimensiones = $this->posibilidades($tipo);
                $source_file = base_path() . '/public/' . $foto->foto_original;
                $this->cortar($dimensiones['ancho'], $dimensiones["alto"], $source_file, $source_file, 100);
            }
            $this->tira($producto_orden);
        }else{
            foreach($producto_orden->fotos as $foto){
                $dimensiones = $this->posibilidades($tipo);
                $source_file = base_path() . '/public/' . $foto->foto_original;
                if( $tipo != '4' && $tipo != '12' && $tipo != '8' ){ 
                    $this->cortar($dimensiones['ancho'], $dimensiones["alto"], $source_file, $source_file, 100);
                    $this->aplicar_cambios($tipo, $foto);
                }elseif ( $tipo == '4' || $tipo == '12' || $tipo == '8' ) {
                    $this->invertir_imagen($source_file);
                    $this->cortar($dimensiones['ancho'], $dimensiones["alto"], $source_file, $source_file, 100);
                    $this->aplicar_cambios($tipo, $foto);
                }
                $this->duplicar($foto);
            }
        }
        if( $producto_orden->id_producto == 8 || $producto_orden->id_producto == 9 || $producto_orden->id_producto == 10 || $producto_orden->id_producto == 11 ){
            $producto_orden->total = $producto_orden->producto->precio;
            $producto_orden->save();
        }else{
            $producto_orden->total = $this->calculo_precio($producto_orden);
            $producto_orden->save();
        }
        return $orden;
    }
    public function revertirCambios($id){
        $foto = ProductoFotos::find($id);
        $r = File::copy( 
            public_path() .'/originales/' . $foto->productoOrden->id . "/" . $foto->nombre, 
            public_path() . "/ediciones/" .  $foto->productoOrden->id . "/" . $foto->nombre
        );
        return response()->json([
            "status" => "success",
            "data" => "Se han revertido los cambios con exito."
        ]);
    }

    public function invertir_imagen($imagen){
        $nombre = basename($imagen);
        $foto = imagecreatefromjpeg($imagen);
        $anchura = imagesx ($foto);
        $altura = imagesy ($foto);
        if($anchura > $altura){
            $image = $imagen;
            //Destino de la nueva imagen vertical
            $image_rotate = $imagen;
            //Definimos los grados de rotacion
            $degrees = 90;
            //Creamos una nueva imagen a partir del fichero inicial
            $source = imagecreatefromjpeg($image);
            //Rotamos la imagen 90 grados
            $rotate = imagerotate($source, $degrees, 0);
            //Creamos el archivo jpg vertical
            imagejpeg($rotate, $image_rotate);
        }
    }

    public function finalizarProducto( $id ){
        $producto_orden = ProductoOrden::find($id);
        $producto_orden->estatus = 1;
        $producto_orden->save();
        $orden = Orden::find($producto_orden->id_orden);
        return response()->json([
            "status" => "success",
            "data" => "El producto fue agregado con exito al carrito."
        ]);
    }

    public function cortar($max_width, $max_height, $source_file, $dst_dir, $quality = 100){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $image_create = "imagecreatefromjpeg";
        $image = "imagejpeg";
        if($imgsize["mime"] == "image/png"){
            $image_create = "imagecreatefrompng";
            //$image = "imagepng";
        }
        $quality = 100;
        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        if($width_new > $width){+
            $h_point = (($height - $height_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
        $image($dst_img, $dst_dir, $quality);
        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);
    }

    public function aplicar_cambios($tipo, $foto_aux){
        $foto = $foto_aux->foto_original;
        $foto_c = $foto_aux->foto_original;
        if($foto_aux['fondo'] == null){
            $dimension = explode( "-", $foto_aux->productoOrden->dimension->dimension );
            $dimension = $dimension[1];
            $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/color/' . $dimension . '-0.jpg';
        }
        switch($tipo){
            case '1':
            case '6':
            case '10':
                $foto =  'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                $foto_c = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;

                    $dimension = explode("-", $foto_aux->productoOrden->dimension->dimension);
                    $dimension = $dimension[1];
                    $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/pattern/' . $dimension . '-0.jpg';
                
                $destino = imagecreatefromjpeg( $foto_aux['fondo'] );
                $origen = imagecreatefromjpeg($foto);

                    $foto_aux['fuente'] = 'assets/fuentes/ttf/font-2.ttf';
                
                $font = realpath($foto_aux['fuente']);

                $foto_aux['texto'] = ' ';
                
                $text = $foto_aux['texto'];
                $textColor = imagecolorallocate($origen, 0, 0, 0);
                $size = 40;
                imagecopymerge($destino, $origen, 59, 59, 0, 0, 1063, 1063, 100);
                $box = imagettfbbox($size, 0, $font, $text);
                $text_width = abs($box[2]) - abs($box[0]);
                $text_height = abs($box[5]) - abs($box[3]);
                $image_width = imagesx($destino);
                $image_height = imagesy($destino);
                $x = ($image_width - $text_width) / 2;
                $y = ($image_height + $text_height) / 2;
                imagettftext($destino, $size, 0, $x, 1110, $textColor, $font, $text);
                imagejpeg($destino,$foto_c);
                //imagedestroy($destino);
                //imagedestroy($origen);
                //unlink($foto);
            break; 
            case '13':
                $foto =  'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                $foto_c = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;

                    $dimension = explode("-", $foto_aux->productoOrden->dimension->dimension);
                    $dimension = $dimension[1];
                    $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/pattern/' . $dimension . '-0.jpg';
                
                $destino = imagecreatefromjpeg( $foto_aux['fondo'] );
                $origen = imagecreatefromjpeg($foto);

                    $foto_aux['fuente'] = 'assets/fuentes/ttf/font-2.ttf';
                
                $font = realpath($foto_aux['fuente']);

                $foto_aux['texto'] = ' ';
                
                $text = $foto_aux['texto'];
                $textColor = imagecolorallocate($origen, 0, 0, 0);
                $size = 40;
                imagecopymerge($destino, $origen, 59, 59, 0, 0, 709, 709, 100);
                $box = imagettfbbox($size, 0, $font, $text);
                $text_width = abs($box[2]) - abs($box[0]);
                $text_height = abs($box[5]) - abs($box[3]);
                $image_width = imagesx($destino);
                $image_height = imagesy($destino);
                $x = ($image_width - $text_width) / 2;
                $y = ($image_height + $text_height) / 2;
                imagettftext($destino, $size, 0, $x, 1110, $textColor, $font, $text);
                imagejpeg($destino,$foto_c);
                //imagedestroy($destino);
                //imagedestroy($origen);
                //unlink($foto);
            break;
            case '8':
            case '12':
            case '4':
                $foto =  'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                $foto_c = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;

                    $dimension = explode("-", $foto_aux->productoOrden->dimension->dimension);
                    $dimension = $dimension[1];
                    $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/pattern/' . $dimension . '-0.jpg';
                
                $destino = imagecreatefromjpeg( $foto_aux['fondo'] );
                $origen = imagecreatefromjpeg($foto);

                    $foto_aux['fuente'] = 'assets/fuentes/ttf/font-2.ttf';
                
                $font = realpath($foto_aux['fuente']);

                $foto_aux['texto'] = ' ';
                
                $text = $foto_aux['texto'];
                $textColor = imagecolorallocate($origen, 0, 0, 0);
                $size = 40;
                imagecopymerge($destino, $origen, 59, 59, 0, 0, 1063, 1654  , 100);
                $box = imagettfbbox($size, 0, $font, $text);
                $text_width = abs($box[2]) - abs($box[0]);
                $text_height = abs($box[5]) - abs($box[3]);
                $image_width = imagesx($destino);
                $image_height = imagesy($destino);
                $x = ($image_width - $text_width) / 2;
                $y = ($image_height + $text_height) / 2;
                imagettftext($destino, $size, 0, $x, 1110, $textColor, $font, $text);
                imagejpeg($destino,$foto_c);
                //imagedestroy($destino);
                //imagedestroy($origen);
                //unlink($foto);
            break;
            break;
            case '2':
            case '7':
            case '11':    
                $foto =  'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                $foto_c = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                if($foto_aux['fondo'] == null){
                    $dimension = explode("-", $foto_aux->productoOrden->dimension->dimension);
                    $dimension = $dimension[1];
                    $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/pattern/' . $dimension . '-0.jpg';
                }
                $destino = imagecreatefromjpeg( $foto_aux['fondo'] );
                $origen = imagecreatefromjpeg($foto);

                if($foto_aux['fuente'] == null){
                    $foto_aux['fuente'] = 'assets/fuentes/ttf/font-2.ttf';
                }
                $font = realpath($foto_aux['fuente']);

                if($foto_aux['texto'] == null){
                    $foto_aux['texto'] = ' ';
                }
                $text = $foto_aux['texto'];
                $textColor = imagecolorallocate($origen, 0, 0, 0);
                $size = 40;
                imagecopymerge($destino, $origen, 59, 59, 0, 0, 1063, 1153.5, 100);
                $box = imagettfbbox($size, 0, $font, $text);
                $text_width = abs($box[2]) - abs($box[0]);
                $text_height = abs($box[5]) - abs($box[3]);
                $image_width = imagesx($destino);
                $image_height = imagesy($destino);
                $x = ($image_width - $text_width) / 2;
                $y = ($image_height + $text_height) / 2;
                imagettftext($destino, $size, 0, $x, 1310, $textColor, $font, $text);
                imagejpeg($destino,$foto_c);
                /*imagedestroy($destino);
                imagedestroy($origen);
                unlink($foto);*/
            break;
            case '3':
            case '9':
                $foto =  'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                $foto_c = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                if($foto_aux['fondo'] == null){
                    $dimension = explode("-", $foto_aux->productoOrden->dimension->dimension);
                    $dimension = $dimension[1];
                    $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/pattern/' . $dimension . '-0.jpg';
                }
                $destino = imagecreatefromjpeg( $foto_aux['fondo'] );
                $origen = imagecreatefromjpeg($foto);

                if($foto_aux['fuente'] == null){
                    $foto_aux['fuente'] = 'assets/fuentes/ttf/font-2.ttf';
                }
                $font = realpath($foto_aux['fuente']);

                if($foto_aux['texto'] == null){
                    $foto_aux['texto'] = ' ';
                }
                $text = $foto_aux['texto'];
                $textColor = imagecolorallocate($origen, 0, 0, 0);
                $size = 40;
                imagecopymerge($destino, $origen, 59, 59, 0, 0, 709, 960, 100);
                $box = imagettfbbox($size, 0, $font, $text);
                $text_width = abs($box[2]) - abs($box[0]);
                $text_height = abs($box[5]) - abs($box[3]);
                $image_width = imagesx($destino);
                $image_height = imagesy($destino);
                $x = ($image_width - $text_width) / 2;
                $y = ($image_height + $text_height) / 2;
                imagettftext($destino, $size, 0, $x, 1110, $textColor, $font, $text);
                imagejpeg($destino,$foto_c);
                //imagedestroy($destino);
                //imagedestroy($origen);
                //unlink($foto);
            break;
            case '14':
                $foto =  'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                $foto_c = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;
                if($foto_aux['fondo'] == null){
                    $dimension = explode("-", $foto_aux->productoOrden->dimension->dimension);
                    $dimension = $dimension[1];
                    $foto_aux['fondo'] = 'assets/fondos/' . $dimension . '/pattern/' . $dimension . '-0.jpg';
                }
                $destino = imagecreatefromjpeg( $foto_aux['fondo'] );
                $origen = imagecreatefromjpeg( $foto );

                if($foto_aux['fuente'] == null){
                    $foto_aux['fuente'] = 'assets/fuentes/ttf/font-2.ttf';
                }
                $font = realpath($foto_aux['fuente']);

                if($foto_aux['texto'] == null){
                    $foto_aux['texto'] = ' ';
                }
                $text = $foto_aux['texto'];
                $textColor = imagecolorallocate($origen, 0, 0, 0);
                $size = 30;
                imagecopymerge($destino, $origen, 59, 59, 0, 0, 709 , 749, 100); 
                $box = imagettfbbox($size, 0, $font, $text);
                $text_width = abs($box[2]) - abs($box[0]);
                $text_height = abs($box[5]) - abs($box[3]);
                $image_width = imagesx($destino);
                $image_height = imagesy($destino);
                $x = ($image_width - $text_width) / 2;
                $y = ($image_height + $text_height) / 2;
                imagettftext($destino, $size, 0, $x, 870, $textColor, $font, $text);
                imagejpeg($destino,$foto_c);
                /*imagedestroy($destino);
                imagedestroy($origen);
                unlink($foto);*/
            break;


        }
    }
    public function posibilidades($data){
        switch ($data) {
            case '1':
            case '6':
            case '10':
                //Formato 10x10 Cuadrado
                $ancho = 1063;
                $alto  = 1063;
            break;
            case '4':
            case '8':
            case '12':
                //Formato 10x15 Clasico
                $ancho = 1063;
                $alto  = 1654;
            break;
            case '2':
            case '7':            
            case '11':
                //Formato 10x12 Retro
                $ancho = 1063;
                $alto  = 1301;
            break;
            case '3':
            case '9':
                //Formato Mini Retro
                $ancho = 797;
                $alto  = 990;
            break;
            case '5':
            case '15':
                //Formato Tira de Fotos
                $ancho = 532;
                $alto  = 406;

            break;
            case '13':
                //Imanes Cuadrado
                $ancho = 709;
                $alto  = 709;
            break;
            case '14':
                //Formato Imanes Retro aqui
                $ancho = 709;
                $alto  = 749;
            break;
        }
        return $data = ["ancho" => $ancho, "alto" => $alto];
    }

    public function calculo_precio($producto_orden){
        $cantidad = 0;
        foreach ($producto_orden->fotos as $foto) {
            $cantidad = $cantidad + $foto->cantidad;
        }
        /* VARIABLES */
        $precio_min = $producto_orden->producto->precio_min;
        $precio_max = $producto_orden->producto->precio_max;
        $precio_emp = $producto_orden->producto->precio_emp;
        $total = $precio_emp + ( $precio_max * 5 );
        if($cantidad > 5){
            // se resta 5 debido a que el precio de las primeras 5 fotos ya esta caculado en la declaracion de la variable total
            for( $i = 1; $i <= $cantidad - 5; $i++ ){
                if( $i <= 5 ){
                    $total = $total + $producto_orden->producto["precio_" . $i];
                }else{
                    $total = $total + $precio_min;
                }
            }
        }
        return round($total, 2);
    }

    public function duplicar($foto_aux){
        $dir = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $foto_aux->nombre;

        for ($i = 1; $i < $foto_aux->cantidad ; $i++) {

            $nombre = explode(".", $foto_aux->nombre);
            $nombre = $nombre[0] . "-" . $i . '.jpg';
            $nombre = 'assets/clientes/'. $foto_aux->productoOrden->orden->numero_orden ."/" . $foto_aux->productoOrden->producto->nombre  .  "/" . $foto_aux->productoOrden->id . "/" . $nombre;
            File::copy(public_path($dir), public_path($nombre));

        }

    }

    public function tira($producto_orden){
        //Formato Tira de Fotos
        $ancho = 532;
        $alto  = 406;


        $foto = 'assets/clientes/'. $producto_orden->orden->numero_orden ."/" . $producto_orden->producto->nombre  .  "/" . $producto_orden->id . "/";
        //Tira 1
        $data = TiraOrden::where('id_orden_producto', $producto_orden->id)->orderBy('display_order', 'asc')->get();
        $destino = imagecreatefromjpeg('assets/fondos/5x15/color/5x15-0.jpg');
        $origen = imagecreatefromjpeg($data[0]->image_name);
        $origen1 = imagecreatefromjpeg($data[2]->image_name);
        $origen2 = imagecreatefromjpeg($data[4]->image_name);
        $origen3 = imagecreatefromjpeg($data[6]->image_name);
        imagecopymerge($destino, $origen, 28, 28, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen1, 28, 465, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen2, 28, 900, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen3, 28, 1335, 0, 0, 532, 406, 100);
        header('Content-Type: image/jpeg');
        imagejpeg($destino, $foto . "T" . $producto_orden->fotos[0]->nombre);
        imagedestroy($destino);
        imagedestroy($origen);
        File::delete($data[0]->image_name);
        File::delete($data[2]->image_name);
        File::delete($data[4]->image_name);
        File::delete($data[6]->image_name);
        //Tira 2
        $destino = imagecreatefromjpeg('assets/fondos/5x15/color/5x15-0.jpg');
        $origen = imagecreatefromjpeg($data[1]->image_name);
        $origen1 = imagecreatefromjpeg($data[3]->image_name);
        $origen2 = imagecreatefromjpeg($data[5]->image_name);
        $origen3 = imagecreatefromjpeg($data[7]->image_name);
        imagecopymerge($destino, $origen, 28, 28, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen1, 28, 465, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen2, 28, 900, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen3, 28, 1335, 0, 0, 532, 406, 100);
        header('Content-Type: image/jpeg');
        imagejpeg($destino,$foto . "T" . $producto_orden->fotos[4]->nombre);
        imagedestroy($destino);
        imagedestroy($origen);
        File::delete($data[1]->image_name);
        File::delete($data[3]->image_name);
        File::delete($data[5]->image_name);
        File::delete($data[7]->image_name);
        //header('Location: '.$_POST['url']); exit;
    }

    public function validate_diseno($request, $suborden){
        $data = [
                    "fondo" => "required",
                    "motivo" => "required",
                    "motivo_img" => "required",
                    "mensaje" => "required"
                ];
        if($suborden->id_producto != 1){
            $data = [
                    "fondo" => "required"
                ];
            if($suborden->id_producto == 11){
                $data["fondo_2"] = "required";
            }
        }
        $data = Validator::make($request->all(), $data);

        return $data;

    }
}