<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Illuminate\Support\Str;
use DateTime;
use Notification;
use App\Notifications\PasswordReset;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        //You can add validation login here
		$user = User::where('email', $request->email)->first(); 
		//Check if the user exists
		if(!$user){
    		return response()->json([
            	'status' => "error",
    			'data' => "Email no existe!"
            ]);
    	}
 
		//Create Password Reset Token
		DB::table('password_resets')->insert([
		    'email' => $request->email,
		    'token' => Str::random(60),
		    'created_at' => new DateTime()
		]); 
		//Get the token just created above 
		$tokenData = DB::table('password_resets')
		    ->where('email', $request->email)->first();

		if ($this->sendResetEmail($request->email, $tokenData->token)) {
		    
    		return response()->json([
            	'status' => "success",
    			'data' => "Se envió un email con los datos de recuperación."
            ]);
	    	
		} else {
		    return response()->json([
	            	'status' => "error",
	    			'data' => "Error en el servidor, intente de nuevo. "
	        ]);
		}
    }

    private function sendResetEmail($email, $token)
	{
		//Retrieve the user from the database
		$user = User::where('email', $email)->first();
		//Generate, the password reset link. The token generated is embedded in the link

		$link = 'htpps://laretrateriaec.com/password/reset/' . $token ;
	    try {
	    //Here send the link with CURL with an external email API 
	    	Notification::send($user, new PasswordReset($link));
	        return true;
	    } catch (\Exception $e) {
	        return false;
	    }
	}
}
