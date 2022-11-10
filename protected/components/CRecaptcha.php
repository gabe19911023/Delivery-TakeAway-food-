<?php
class CRecaptcha
{
	private static $error_codes='';
	
	public static function verify($secret='',$response='')
	{
		if(empty($response)){
			throw new Exception( 'Please validated captcha' );
		}
		if(empty($secret)){
			throw new Exception( 'Invalid recaptcha secret key' );
		}
		
		$data = array(
            'secret' => $secret,
            'response' => $response
        );
		$verify = curl_init();
		curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($verify, CURLOPT_POST, true);
		curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($verify);
		
		if($json = json_decode($response,true)){			
			$success = isset($json['success'])?$json['success']:false;
			$error_code = isset($json['error-codes'])?$json['error-codes']:'';
			if($success){
				return $json;
			} else {
				if(is_array($error_code) && count($error_code)>=1){					
					$err_all = '';
					foreach ($error_code as $err) {
						$err_all.="$err\n";
						self::$error_codes = $err;
					}
				} else $err_all = "Invalid google recaptcha error";
				throw new Exception( $err_all );
			}			
		}
		throw new Exception( 'Invalid response from google recaptcha' );
	}
	
	public static function getError()
	{
		return self::$error_codes;
	}
	
}
/*end class*/