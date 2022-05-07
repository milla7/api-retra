<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\Documento;
use App\User;
use DateTime;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(Request $request){
        $validator = Validator::make($request->all(), [
            "email" => 'required|string|email|max:255|unique:usuarios',
            "nombres" => "required",
            "apellidos" => "required",
            "celular" => 'required|min:10',
            "cedula" => "required|min:10",
            "direccion" => "required",
            "clave" => "required|min:6",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "data" => $validator->messages()
            ]);
        }else{
            $user = User::create($request->all());
            $user->clave = Hash::make($user->clave);
            $user->save();
            return response()->json([
                "status" => "success",
                "data" => "El usuario ha sido creado" 
            ]);
        }
    }
}
