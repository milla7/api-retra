<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductoOrden;
use App\ProductoFotos;
use Illuminate\Support\Facades\Validator;

class EditorController extends Controller
{
    public function lapiz(Request $request, ProductoOrden $producto_orden){
        $foto_producto = ProductoFotos::find($request->id_foto);
    	$carpeta_orden = base_path() . '/public/ediciones/' . $producto_orden->id . "/";
		$foto = $request->file('imagen')->getClientOriginalName();
		$file_path = pathinfo($foto,PATHINFO_FILENAME);
		move_uploaded_file($file_path, $carpeta_orden.'/'.$foto );
		$file =  $request->file('imagen');
        $file->move($carpeta_orden, $foto_producto->nombre);
		return response()->json([
            "status" => "success",
            "data" => "La imagen fue cargada con exito."
        ]);
    }
    public function marco(Request $request , ProductoFotos $producto_fotos){
    	$dimension = $producto_fotos->productoOrden->dimension->solo_dimension;
    	return view( 'panel.editor.marco', compact( 'producto_fotos', 'dimension' ) );
    }
    public function guardarMarco(Request $request){
        $validator = Validator::make($request->all(), [
            "id" => 'required',
            "fondo" => "nullable",
            "fuente" => "nullable",
            "texto" => "nullable",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }    
    	$foto = ProductoFotos::find($request->id);
    	$foto->update([
    		"fondo" => $request->fondo,
    		"fuente" => $request->fuente,
    		"texto" => $request->texto,
    	]);
    	return response()->json([
            "status" => "success",
            "data" => "Se guardaron los Cambios con Exito!"
        ]);
    }
}
