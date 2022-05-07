<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Producto;
use Illuminate\Support\Facades\Session;
use App\ProductoOrden;
use App\TiraOrden;
use DB;
use App\Categoria;
use App\Fondo;
use App\Motivo;
use App\ImagenesMotivo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\ProductoFotos;
use App\Fuente;
class ProductoController extends Controller
{
    
    public function listarCategorias(){
        $categorias = Categoria::all();
        return response()->json([
            "status" => "success",
            "data" => [
                "categorias" => $categorias
            ]
        ]);
    }
    

    public function listarProductosCategoria($id){
        $productos = Producto::where('id_categoria', $id)->get();
        foreach($productos as $producto){
            $producto->img_1 = 'assets/productos/' . $producto->img_1;
            $producto->img_2 = 'assets/productos/' . $producto->img_2;
            $producto->img_3 = 'assets/productos/' . $producto->img_3;
            $producto->img_4 = 'assets/productos/' . $producto->img_4;
        }
        return response()->json([
           "status" => "success",
            "data" => [
                "productos" => $productos
            ]
        ]);
    }

    public function listarProducto($id){
        $producto = Producto::where('id', $id)->with(['dimensiones', 'iconos'])->get();
        $producto_aux =  Producto::where('id', $id)->with(['dimensiones', 'iconos'])->get();
        $producto[0]->img_1 = 'assets/productos/' . $producto[0]->img_1;
        $producto[0]->img_2 = 'assets/productos/' . $producto[0]->img_2;
        $producto[0]->img_3 = 'assets/productos/' . $producto[0]->img_3;
        $producto[0]->img_4 = 'assets/productos/' . $producto[0]->img_4;
        $fondos = [];
        foreach ($producto[0]->iconos as $icono) {
            $icono->nombre = 'assets/img/' . $icono->nombre;;
        }
        foreach ($producto_aux[0]->fondos as $fondo ) {
            $aux = Fondo::find($fondo->id_fondo);
            $fondos[] = $aux;
        }
        $producto[0]["fondos"] = $fondos;
        $producto[0]["motivos"] = [];
        if($producto[0]->id == 1){ 
            $motivos = Motivo::where('id', '>=', 1)->with('imagenes')->get();
            $producto[0]["motivos"] = $motivos;
        }
        
        return response()->json([
           "status" => "success",
            "data" => [
                "producto" => $producto[0]
            ]
        ]);
    }

    public function getProductosCategoria(Request $request, $id){
    	$productos = Producto::where("id_categoria", $id)->get();
    	$nombre = $productos[0]->categoria->nombre;
    	return view('panel.productos.index', compact('productos', 'nombre'));
    }
    public function getProductoDetalle($id){
    	$data = Producto::find($id);
    	return view('panel.productos.detalle', compact('data'));
    }

    public function ordenFotos($id){
        $fotos = TiraOrden::where('id_orden_producto', $id)->orderBy('display_order', 'asc')->get();
        foreach ($fotos as $foto) {
            $foto->image_name = 'ediciones/'. $id . '/' . $foto->nombre;
        }
        return response()->json([
            "status" => "success",
            "data" => [
                "orden" => $fotos
            ]
        ]);
        
    }

    public function ordenarFotos(Request $request){
        $order  = explode(",",$request["order"]);
        for($i=0; $i < count($order);$i++) {
            DB::select("UPDATE reorder SET display_order='" . $i . "' WHERE id=". $order[$i]);       
        }
        return response()->json([
            "status" => "success",
            "data" => "se ha ordenado de manera exitosa!"
        ]);
    }

    public function tira($producto_orden){
        //Formato Tira de Fotos
        $ancho = 532;
        $alto  = 406;

        $nombres = [];
        $foto = 'assets/clientes/'. $producto_orden->orden->numero_orden ."/" . $producto_orden->producto->nombre  .  "/" . $producto_orden->id . "/";
        //Tira 1
        $destino = imagecreatefromjpeg('assets/fondos/5x15/color/5x15-0.jpg');
        $data = TiraOrden::where('id_orden_producto', $id)->orderBy('display_order', 'asc')->get();
        $origen = imagecreatefromjpeg($data[0]->image_name);
        $origen1 = imagecreatefromjpeg($data[1]->image_name);
        $origen2 = imagecreatefromjpeg($data[2]->image_name);
        $origen3 = imagecreatefromjpeg($data[3]->image_name);
        imagecopymerge($destino, $origen, 28, 28, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen1, 28, 465, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen2, 28, 900, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen3, 28, 1335, 0, 0, 532, 406, 100);
        imagejpeg($destino, 'archivos/' . "T" . $producto_orden->fotos[0]->nombre);
        $nombres[0] = 'archivos/' . "T" . $producto_orden->fotos[0]->nombre;
        imagedestroy($destino);
        imagedestroy($origen);

        //Tira 2
        $destino = imagecreatefromjpeg('assets/fondos/5x15/color/5x15-0.jpg');
        $origen = imagecreatefromjpeg($data[0]->image_name);
        $origen1 = imagecreatefromjpeg($data[1]->image_name);
        $origen2 = imagecreatefromjpeg($data[2]->image_name);
        $origen3 = imagecreatefromjpeg($data[3]->image_name);
        imagecopymerge($destino, $origen, 28, 28, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen1, 28, 465, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen2, 28, 900, 0, 0, 532, 406, 100);
        imagecopymerge($destino, $origen3, 28, 1335, 0, 0, 532, 406, 100);
        imagejpeg($destino, 'archivos/' . "T" . $producto_orden->fotos[4]->nombre);
        $nombres[1] = 'archivos/' . "T" . $producto_orden->fotos[4]->nombre;
        imagedestroy($destino);
        imagedestroy($origen);
        return $nombres;

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

    public function agregarDiseno(Request $request ,$id){
        $suborden = ProductoOrden::find($id);
        $data = $this->validate_diseno($request, $suborden);
        if(!$data->passes()){
            return response()->json([
                "status" => "error",
                "data" => $data->messages()
            ]);
        }
        $carpeta_orden = 'assets/clientes/'. $suborden->orden->numero_orden . "/" . $suborden->producto->nombre . "/" . $suborden->id . "/";
        if (!file_exists($carpeta_orden)) {
            mkdir($carpeta_orden, 0777, true);
        }
        if( file_exists( $carpeta_orden .'datos.txt' ) ){
            File::delete( $carpeta_orden .'datos.txt' );
        }

        if( $suborden->id_producto == 11 || $suborden->id_producto == 8 ){
            $contenido  = "Fondo 1: ".$request['fondo_1'];
            $contenido .= "\nFondo 2: ".$request['fondo_2'];
        }else{
            $contenido  = "Fondo: ".$request['fondo_1'];
        }
        if ( $suborden->id_producto == 1 ){
            $contenido .= "\nMotivo: ".$request['motivo'];
            $contenido .= "\nMotivo Imagen: ".$request['motivo_img'];
            $contenido .= "\nMensaje: ".$request['mensaje'];
        }
        $contenido .= "\nEtiqueta: ".$suborden->etiqueta;
        $contenido .= "\nDimension: ".$suborden->dimension->dimensiones;
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
        return response()->json([
            "status" => "success",
            "data" => "el diseño se guardo con exito!"
        ]);
    }
    public function validate_diseno($request, $suborden){
        $data = [ 
                    "fondo_1" => "required",
                    "motivo" => "required",
                    "motivo_img" => "required",
                    "mensaje" => "required"
                ];
        if($suborden->id_producto != 1){
            $data = [
                    "fondo_1" => "required"
                ];
            if($suborden->id_producto == 11){
                $data["fondo_2"] = "required";
            }
        }
        $data = Validator::make($request->all(), $data);
        return $data;

    }

    public function listarMarcos($id){
        $foto = ProductoFotos::find($id);

        return response()->json([
            "status" => "success",
            "data" => [
                "fondos" => $foto->ProductoOrden->producto->marcos,
                "fuentes" => Fuente::all()
            ]
        ]);
    }


}
