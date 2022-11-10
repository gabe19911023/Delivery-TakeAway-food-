<?php
class CPaypal
{
	private static $token;
	private static $is_live=0;
	
	public static function setToken($token='')
	{
		CPaypal::$token = trim($token);
	}
	
	public static function setProduction($is_live='')
	{
		CPaypal::$is_live = intval($is_live);
	}
	
	public static function ChangeUrl($url ='' )
	{
		if(self::$is_live==1){
		  $url = str_replace("sandbox.","",$url);
		}
		return $url;
	}	
	
	public static function getOrders($order_id='')
	{
		$ch = curl_init();
						
		curl_setopt($ch, CURLOPT_URL, self::ChangeUrl("https://api-m.sandbox.paypal.com/v2/checkout/orders/$order_id") );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');		
		
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: Bearer '.CPaypal::$token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);		
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}
		curl_close($ch);		
		if($json_resp = json_decode($result,true)){
			if(isset($json_resp['error'])){
				throw new Exception ( isset($json_resp['error_description'])?$json_resp['error_description']:$json_resp['error'] );
			} else {
				if(isset($json_resp['status'])){
					if($json_resp['status']=="COMPLETED" || $json_resp['status']=="PENDING" ){
						return $json_resp;
					}
				}
			}
		}
		throw new Exception( "An error has occured" ." ".json_encode($result) );
	}
	
	public static function authorizations($authorization_id='')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::ChangeUrl("https://api-m.sandbox.paypal.com/v2/payments/authorizations/$authorization_id/capture") );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: Bearer '.CPaypal::$token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);		
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}
		curl_close($ch);		
		if($json_resp = json_decode($result,true)){						
			if(isset($json_resp['status'])){
				if($json_resp['status']=="COMPLETED" || $json_resp['status']=="PENDING"){
					return $json_resp;
				}
			}
			$details = '';
			if(isset($json_resp['details'])){
				if(is_array($json_resp['details'])  && count($json_resp['details'])>=1 ){
					foreach ($json_resp['details'] as $item) {
				   		$details.=$item['description'];
				   	}	
				}
				throw new Exception( !empty($details)?$details:$json_resp['message'] );
			}
		}
		throw new Exception( "An error has occured" ." ".json_encode($result) );

	}
	
	public static function createOrders($amount=0,$currency_code='USD')
	{
		$ch = curl_init();
		
		$params = array(
		  'intent'=>"CAPTURE",
		  'purchase_units'=>array( 
		    array(
		      'amount'=>array(
		         'currency_code'=>$currency_code,
		         'value'=>$amount
		      )
		    )
		  )
		);
							
		curl_setopt($ch, CURLOPT_URL, self::ChangeUrl('https://api-m.sandbox.paypal.com/v2/checkout/orders') );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: Bearer '.CPaypal::$token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);		
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}		
		curl_close($ch);		
		if($json_resp = json_decode($result,true)){			
			if(isset($json_resp['status'])){
				if($json_resp['status']=="CREATED"){
					return $json_resp['id'];
				}
			}
			return $json_resp;
		}
		throw new Exception( "An error has occured" ." ".json_encode($result) );
	}
	
    public static function captureOrder($order_id='', $transaction_id='')
	{
		$ch = curl_init();
		
		$params = array(
		  'payment_source'=>array(
		     'token'=>array(
		        'id'=>$transaction_id,
		        'type'=>"PAYPAL_TRANSACTION_ID"
		     )
		  )
		);
					
		curl_setopt($ch, CURLOPT_URL, self::ChangeUrl("https://api-m.sandbox.paypal.com/v2/checkout/orders/$order_id/capture") );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: Bearer '.CPaypal::$token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);		
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}		
		curl_close($ch);		
		
		if($json_resp = json_decode($result,true)){				
			return $json_resp;
		}
		throw new Exception( "An error has occured" ." ".json_encode($result) );
	}	
	
	public static function refund($payment_id='', $amount=0, $currency_code='')
	{		
		$ch = curl_init();
		$params = array();
		
		if($amount>0 && !empty($currency_code)){
			$params = array(
			  'amount'=>array( 
			    'total'=>$amount,
			    'currency'=>$currency_code
			  )
			);
		}
		
		curl_setopt($ch, CURLOPT_URL, self::ChangeUrl("https://api-m.sandbox.paypal.com/v1/payments/capture/".$payment_id."/refund") );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        if(is_array($amount) && count($amount)>=1){
           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        $headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: Bearer '.CPaypal::$token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);
				
		if (curl_errno($ch)) {		    
		    throw new Exception( curl_error($ch) );
		}		
		curl_close($ch);		
		
		if($json_resp = json_decode($result,true)){							
			if(isset($json_resp['id'])){				
				return $json_resp;
			} elseif (isset($json_resp['message'])){
				throw new Exception( "An error has occured" ." ".$json_resp['message'] );
			}
		}
		throw new Exception( "An error has occured" ." ".json_encode($result) );
	}
	
}
/*end class*/