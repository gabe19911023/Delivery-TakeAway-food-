<?php
require_once 'php-curl/vendor/autoload.php';
class BItemInstant
{
    const BITEM_IDENTITY = 'UYIiWfAfWx414it65oUbeXf4I1yjDNSZi2UxnBBLQa8hpHAcVlyP+Sx0OL8vmfcwnzSYkw==';
	
	public static function instantiateIdentity()
	{	
		// $curl = new anlutro\cURL\cURL;		
		// $domain = Yii::app()->request->getServerName();		
		// $url = $curl->buildUrl('http://bastisapp.com/activation/index/check', ['id' => self::BITEM_IDENTITY ,'domain'=>$domain ]);					
		// $response = $curl->get($url);		
		// if($response->statusCode==200){
		// 	$body =  !empty($response->body)?json_decode($response->body,true):false;			
		// 	if($body['code']>1){
		// 		Yii::app()->getController()->redirect("http://bastisapp.com/activation/");	
		// 		Yii::app()->end();
		// 	} 
		// 	return true;
		// }
		return true;				
	}
	
}
/*end class*/