<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
class TokenController extends Controller
{
    public function token(){
		$server_application_code = "TPP-EC-SERVER";
		$server_app_key = "mYmEhYuLxNkCZrw8FkFVTeZxx4rfY9";
		$date = new DateTime();
		$unix_timestamp = $date->getTimestamp();
		// $unix_timestamp = "1546543146";
		$production = false;
		if( $server_application_code != "TPP-EC-SERVER" ){
			$production = true;
		}
		$uniq_token_string = $server_app_key.$unix_timestamp;
		$uniq_token_hash = hash('sha256', $uniq_token_string);
		$auth_token = base64_encode($server_application_code.";".$unix_timestamp.";".$uniq_token_hash);
		return response()->json([
			"status" => "succes",
			"data" => [
				"token" => $auth_token,
				"production" => $production
			]
		]);
    }
    public function token2(){
		$server_application_code = "TPP-EC-CLIENT";
		$server_app_key = "JfjHyC1y1xJQH4MrfxJFWUV56d963K";
		$date = new DateTime();
		$unix_timestamp = $date->getTimestamp();
		$uniq_token_string = $server_app_key.$unix_timestamp;
		$uniq_token_hash = hash('sha256', $uniq_token_string);
		$auth_token = base64_encode($server_application_code.";".$unix_timestamp.";".$uniq_token_hash);
		return response()->json([
			"status" => "succes",
			"data" => ["token" => $auth_token]
		]);
    }
}
