<?php

namespace App\Http\Controllers\Orden;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Orden;
use DateTime;
use Illuminate\Support\Facades\Validator;
use App\ProductoOrden;
class OrdenController extends Controller
{
    public function crearOrden(Request $request){
    	$validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }
    	$fecha = new DateTime();
		$orden = Orden::create([
			'fecha' => $fecha->format('Y-m-d'),
			'total' => '0',
			'id_usuario' => $request->id_usuario,
            'numero_orden' => 'ORDEN-'
		]);
        $orden->numero_orden = $orden->numero_orden . $orden->id;
        $orden->save();
		return response()->json([
			"status" => "success",
			"data" => [
				"orden" => $orden
			]
		]);
    }

    public function crearProducto(Request $request){
    	$validator = Validator::make($request->all(), [
            'id_orden' => 'required|exists:orden,id',
            'id_producto' => 'required|exists:productos,id',
            'id_dimension' => 'required|exists:productos_dimensiones,id',
            'etiqueta' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }
    	$producto = ProductoOrden::create([
            "id_orden" => $request->id_orden,
            "id_producto" => $request->id_producto,
            "id_dimension" => $request->id_dimension,
            "etiqueta" => $request->etiqueta
        ]);


        if( $request->id_orden == 1 || $request->id_orden == 2 || $request->id_orden == 3 || $request->id_orden == 11 ){
        }else{
            $dimension = explode("-", $producto->dimension->dimension);
            $dimension = $dimension[1];
            $this->guardarDimension($producto, $dimension);
        }

        return response()->json([
        	"status" => "success",
        	"data" => [
        		"producto" => $producto
        	]
        ]);
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

}
