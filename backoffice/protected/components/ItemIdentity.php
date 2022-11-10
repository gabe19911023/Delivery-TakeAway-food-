<?php
require_once 'php-curl/vendor/autoload.php';
class ItemIdentity
{

	public static function initializeIdentity($object)
	{						
		$resp = self::instantiateIdentity();				
		if(!$resp){
			Yii::app()->getController()->redirect( Yii::app()->createUrl('login/error') );	
			return false;
		}
		return true;
	}
	
	public static function instantiateIdentity()
	{	
		return true;
		try {					
			
			$curl = new anlutro\cURL\cURL;		
			$domain = Yii::app()->request->getServerName();		
			$url = $curl->buildUrl('http://bastisapp.com/activation/index/check', ['id' => Yii::app()->params->item_identity ,'domain'=>$domain ]);					
			$response = $curl->get($url);		
			if($response->statusCode==200){
				$body =  !empty($response->body)?json_decode($response->body,true):false;			
				if($body['code']>1){
					Yii::app()->getController()->redirect("http://bastisapp.com/activation/");	
					Yii::app()->end();
				} 
				return true;
			}
		    return false;				
		} catch (\Throwable $th) {
			return false;
		}
	}

	public static function addonIdentity($addon_name='')
	{
		return true;
		$addon = AR_addons::model()->find("addon_name=:addon_name",[
			':addon_name'=>$addon_name
		]);
		if($addon){						
			$curl = new anlutro\cURL\cURL;		
			$domain = Yii::app()->request->getServerName();		
			$url = $curl->buildUrl('http://bastisapp.com/activation/index/check', ['id' => $addon->uuid ,'domain'=>$domain ]);					
			$response = $curl->get($url);				
			if($response->statusCode==200){
				$body =  !empty($response->body)?json_decode($response->body,true):false;								
				if($body['code']>1){
					throw new Exception( $body['msg'] );
				}
				return true;
			}		
			throw new Exception("Activation calls failed");
		}
		throw new Exception("Addon not found");
	}
	
}
/*end class*/