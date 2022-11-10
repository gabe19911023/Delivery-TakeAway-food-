<?php
class CServices
{
	public static function Listing($lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.service_code,
		(
		 select service_name from {{services_translation}}
		 where service_id = a.service_id
		 and language = ".q($lang)."
		) as service_name
		
		FROM {{services}} a		
		WHERE status = 'publish'
		";
		$dependency = new CDbCacheDependency("SELECT MAX(date_modified) FROM {{services}}");
		if($res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {				
				$data[$val['service_code']]=$val;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getFirstService()
	{	
		// $model = AR_services::model()->find('service_code=:service_code', 
		// array(':service_code'=>'delivery')); 		
		// if($model){
		// 	return $model->service_code;
		// }
		// return false;
		$criteria=new CDbCriteria();		
		$criteria->addInCondition('status', ['publish'] );
		$model = AR_services::model()->find($criteria); 
		if($model){
			return $model->service_code;
		}
		return false;
	}
	
	public static function getSetService($cart_uuid='')
	{
		$transaction_type='';
		try {			
			$merchant_id = CCart::getMerchantId($cart_uuid);
			$transaction_type = CCart::cartTransaction($cart_uuid,Yii::app()->params->local_transtype,$merchant_id);						
		} catch (Exception $e) {			
			if($model = CCart::getAttributes($cart_uuid,Yii::app()->params->local_transtype)){
			  $transaction_type =  $model->meta_id;
			} else $transaction_type = CServices::getFirstService();
		}
		return $transaction_type;
	}
	
	
}
/*end class*/