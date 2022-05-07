<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function signIn(Request $request){
        $validator = Validator::make($request->all(), [
            "email" => 'required|email',
            "id_device" => "required",
            "clave" => "required|min:6",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }    
    	$user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
	        	"status" => "error",
			    "data" => [
                    "email" => ["Este email no se encuetra registrado."]
                ]
           	]);
        }
        if (!Hash::check($request->clave, $user->clave)) {
            return response()->json([
	        	"status" => "error",
                "data" => [
                    "clave" => ["Clave incorrecta"]
                ]
           	]);
        }
        if($user->estatus != 1){
            return response()->json([
                "status" => "error",
                "data" => 
                [
                    "usuario" => ["El usuario se encuentra suspendido"]
                ]

            ]); 
        }
        $data = $user;
        $data["token"] = $user->createToken($request->id_device)->plainTextToken;
        return response()->json([
                "status" => "success",
                "data" => [
                    "usuario" => [
                        $data
                    ],
                ]
            ]);
        
    }
    public function singOut(Request $request, $id){
        $user = User::find($id);
        if($user){
            $user->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'El usuario ha cerrado sesión.'
            ]);
        }
        return response()->json([
                'status' => 'error',
                'message' => 'El usuario no ha sido encontrado.'
            ]);
    }
}
