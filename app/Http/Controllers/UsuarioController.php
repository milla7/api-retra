<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function editarUsuario(Request $request){
    	$validator = Validator::make($request->all(), [
    		'id' => 'required|exists:usuarios,id',
            'email' => 'required|max:200|unique:usuarios,email,'. $request->id,
            "nombres" => "required",
            "apellidos" => "required",
            "celular" => 'required|min:10',
            "cedula" => "required|min:10",
            "direccion" => "required",
            "clave" => "nullable|min:6",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }else{
        	User::where('id', $request->id)->update($request->except('clave'));
        	if($request->clave != null){
        		User::where('id', $request->id)->update([
        			"clave" => Hash::make($request->clave)
        		]);
        	}
        	return response()->json([
                "status" => "success",
                "data" => "El usuario ha sido editado"
            ]);
        }
    }
}
