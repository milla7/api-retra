<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Orden;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class UsuarioController extends Controller
{
    public function perfil(){
    	$usuario = Auth::user();
    	return view('panel.usuario.perfil', compact('usuario'));
    }

    public function update(Request $request){
        $data = [
                'email' => 'required|max:200|unique:usuarios,email,'. Auth::user()->id,
                "nombres" => "required|max:50",
                "apellidos" => "required|max:50",
                "celular" => "required|max:20",
                "cedula" => "required|min:10|max:10",
                "direccion" => "required|max:300"
        ];
        if( isset($request->clave) ){
            $data['clave'] = "required|min:6|max:15";
        }
    	$data = $request->validate($data);
        $usuario = User::find(Auth::user()->id);
        $usuario->update($data);
        if( isset($request->clave) ){
            $usuario->clave = Hash::make($usuario->clave);
            $usuario->save();
        }
        return redirect('/perfil')->with(['success' => 'Tu Perfil ha sido actualizado con exito!']);
    }

    public function ordenes($id){
    	$usuario = User::find($id); 
        $ordenes = $usuario->ordenes;
        foreach ($ordenes as $orden) {
            $orden->estatus;
        }
    	return response()->json([
            "status" => "success",
            "data" => [
                "ordenes" => $ordenes
            ]
        ]);
    }
}
