<?php
require 'twilio/vendor/autoload.php';
use Twilio\Rest\Client;

class CSMSsender
{
	private static $model;
	private static $to;
	private static $body;
	private static $merchant_id;
	private static $client_name;
	private static $client_id;
	
	
	public static function init()
	{		
		self::$model = AR_sms_provider::model()->find('as_default=:as_default', 
		array(':as_default'=>1)); 		
		if(self::$model){		    
		    return true;
		}
		throw new Exception( 'no default email provider' );
	}
	
	public static function setTo($to)
	{		
		self::$to = trim($to);
	}
			
	public static function setBody($body='')
	{
		self::$body = trim($body);
	}
	
	public static function setMerchantID($id='')
	{
		self::$merchant_id = intval($id);
	}
	
	public static function setClientID($id='')
	{
		self::$client_id = intval($id);
	}
	
	public static function setName($name='')
	{
		self::$client_name = trim($name);
	}
	
	public static function send()
	{
		$model = self::$model;
		$error = ''; $success = ''; $gateway_response='';
		
		try {
			switch ($model->provider_id) {
				
				case "smsglobal":					
					
					$url = "https://api.smsglobal.com/http-api.php";
					$curl = curl_init($url);
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					
					$headers = array(
					   "Content-Type: application/x-www-form-urlencoded",
					);
					curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);					
					
					$data = array(
					  'action'=>'sendsms',
					  'user'=>trim($model->key2),
					  'password'=>trim($model->key3),
					  'from'=>trim($model->key1),
					  'to'=>self::$to,
					  'text'=>self::$body
					);
					
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));									
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					
					$resp = curl_exec($curl);
					curl_close($curl);					
					if (preg_match("/OK/i", $resp)) {
						$success = "sent";
						$gateway_response = $resp;
					} else $error = $resp;
					
					break;
				
				case "clickatell":							    
					$ch = curl_init();
					$params = array(  
					  'apiKey'=>trim($model->key1),
					  'to'=>self::$to,
					  'content'=>self::$body
					);
					$url = "https://platform.clickatell.com/messages/http/send?".http_build_query($params);					
					curl_setopt($ch, CURLOPT_URL, $url );
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					
					$result = curl_exec($ch);
					if (curl_errno($ch)) {
					    $error =  'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					if($json = json_decode($result,true)){						
						if(isset($json['errorCode'])){
							$error = $json['errorDescription'];
						} else {
							if(is_array($json['messages'])){
								foreach ($json['messages'] as $item) {									
									if(isset($item['apiMessageId'])){
										$success = "sent";
										$gateway_response = $item['apiMessageId'];
									} else $error = isset($item['errorDescription'])?$item['errorDescription']:'invalid response';
								}
							} else $error = isset($json['errorDescription'])?$json['errorDescription']:'invalid response';
						}
					} else $error = "invalid response";

				    break;
				    
				case "nexmo":			
				    
				    require 'nexmo/vendor/autoload.php';						    
				    $basic  = new \Vonage\Client\Credentials\Basic( trim($model->key2) , trim($model->key3) );
                    $client = new \Vonage\Client($basic);
                    
                    $response = $client->sms()->send(
					    new \Vonage\SMS\Message\SMS(CSMSsender::$to, trim($model->key1) ,CSMSsender::$body)
					);
										
					$message = $response->current();
					
					if ($message->getStatus() == 0) {
					    $success = "sent";
					    $gateway_response = $message->getMessageId();
					} else {
					    $error = $message->getStatus();
					}
					break;
					
				case "twilio":		
				    $phone_to = self::addPlusBeginning(CSMSsender::$to);				    
					$client = new Client( trim($model->key2) , trim($model->key3) );
					$message = $client->messages->create(
					  "+".CSMSsender::$to, 
					  [
					    'from' => $model->key1,
					    'body' => CSMSsender::$body
					  ]
					);
					$success = "sent";
					$gateway_response = $message->sid;					
					break;
			
				default:
					$error = "no sms provider set in admin panel";
					break;
			}
		} catch (Exception $e) {
		   $error = $e->getMessage();		  
		}		
				
		$log = new AR_sms_broadcast_details;
		$log->contact_phone = CSMSsender::$to;
		$log->sms_message = CSMSsender::$body;
		$log->status = !empty($success)? CommonUtility::cutString($success) : CommonUtility::cutString($error) ;
		$log->gateway = $model->provider_id;
		$log->gateway_response = !empty($gateway_response)?$gateway_response:$error;
		$log->merchant_id = intval(self::$merchant_id);
		$log->client_id = intval(self::$client_id);
		$log->client_name = self::$client_name;		
		if(!$log->save()){			
			$error.=CommonUtility::parseModelErrorToString( $log->getErrors() );
		} 
		
		if($success=="sent"){
			return true;
		} else throw new Exception( $error );
	}
	
	public static function addPlusBeginning($phone='')
	{
		$pos = strpos($phone, "+");
		if ($pos === false) {
			return "+$phone";
		} else return $phone;
	}
	
	public static function removePlusBeginning($phone='')
	{
		$pos = strpos($phone, "+");
		if ($pos === false) {
			return $phone;
		} else {
			return str_replace("+","",$phone);
		}	
	}
	
}
/*end class*/