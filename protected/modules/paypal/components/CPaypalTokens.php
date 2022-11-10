<?php
class CPaypalTokens
{
	private static $credentials;
	private static $payment_code;
	private static $is_live=0;
	
	public static function setCredentials($credentials=array(), $payment_code='')
	{
		CPaypalTokens::$credentials = $credentials;
		CPaypalTokens::$payment_code = $payment_code;
	}
	
	public static function setProduction($is_live='')
	{
		self::$is_live = intval($is_live);
	}
	
	public static function ChangeUrl($url ='' )
	{
		if(self::$is_live==1){
		  $url = str_replace("sandbox.","",$url);
		}
		return $url;
	}	
	
	public static function getToken($time_now='')
	{						
		if( !is_array(CPaypalTokens::$credentials) && count(CPaypalTokens::$credentials)>=1){
			throw new Exception("invalid credentials");
		}

		
		$is_live = intval(CPaypalTokens::$credentials['is_live']);
		$client_id = CPaypalTokens::$credentials['attr1'];
		$secret_key = CPaypalTokens::$credentials['attr2'];
		$merchant_id = intval(CPaypalTokens::$credentials['merchant_id']);		
						
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::ChangeUrl('https://api-m.sandbox.paypal.com/v1/oauth2/token') );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
		curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $secret_key);
						
		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Accept-Language: en_US';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);		
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}
		curl_close($ch);			
		if($json_resp = json_decode($result,true)){				
			if(isset($json_resp['access_token'])){
			   	$access_token = $json_resp['access_token'];
			   	$expires_in = $json_resp['expires_in'];			   	
			   	$time = intval(gmdate("H", $json_resp['expires_in'] ));
                $mins = intval(gmdate("i", $json_resp['expires_in'] ));                
                $date_expired = date('Y-m-d H:i:s',strtotime("+$time hour +$mins minutes",strtotime($time_now)));
                return $access_token;               
			} elseif ( $json_resp['error_description']){
				throw new Exception( $json_resp['error_description'] );
			}			
		}
		throw new Exception( "An error has occured" ." ".json_encode($result) );
		
	}
}
/*end class*/