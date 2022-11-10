<?php
class CSocialLogin{
	
	
	public static function validateAccessToken($access_token='')
	{		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/me?access_token='.$access_token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}
		curl_close($ch);
		
		if ($json = json_decode($result,true)){			
			$id = isset($json['id'])?$json['id']:'';
			$error = isset($json['error'])?$json['error']:'';
			if($id>0){
				return true;
			} elseif ( !empty($error)){
				$error = isset($json['error']['message'])?$json['error']['message']:'undefined facebook error';
				throw new Exception( $error );
			}
		}
		throw new Exception( "Undefined facebook response" );
	}
	
	public static function validateIDToken($id_token='',$client_id='')
	{		
		require_once 'google-client/vendor/autoload.php';
		$client = new Google_Client(['client_id' => $client_id]); 
		$payload = $client->verifyIdToken($id_token);
		if ($payload) {
			return true;
		} else {
			throw new Exception( "Invalid ID token" );
		}			
	}
}
/*end class*/