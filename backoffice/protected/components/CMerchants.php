<?php
class CMerchants
{	
	public static function get($merchant_id='')
	{
		$dependency = CCacheData::dependency();
		$model = AR_merchant::model()->cache(Yii::app()->params->cache, $dependency)->find('merchant_id=:merchant_id', 
		array(':merchant_id'=>$merchant_id)); 
		if($model){
			return $model;
		}
		throw new Exception( 'merchant not found' );
	}
	
	public static function getByUUID($merchant_uuid='')
	{
		$dependency = CCacheData::dependency();
		$model = AR_merchant::model()->cache(Yii::app()->params->cache, $dependency)->find('merchant_uuid=:merchant_uuid', 
		array(':merchant_uuid'=>$merchant_uuid)); 
		if($model){
			return $model;
		}
		throw new Exception( 'merchant not found' );
	}
	
	public static function getTotalOrders($merchant_id=0)
	{
		$draft = AttributesTools::initialStatus();
		$not_in_status = AOrderSettings::getStatus(array('status_cancel_order','status_rejection'));
		array_push($not_in_status,$draft);    		
		$criteria=new CDbCriteria();
		$criteria->select="sum(total) as total";		
		$criteria->condition = "merchant_id=:merchant_id";		    
		$criteria->params  = array(
		  ':merchant_id'=>intval($merchant_id)		  
		);				
		$criteria->addNotInCondition('status', (array)$not_in_status );
		$count = AR_ordernew::model()->count($criteria); 
		return intval($count);
	}
	
	public static function getMerchantType($merchant_id=0)
	{
		$model = self::get($merchant_id);
		if($model){
			return $model->merchant_type;
		}
	}

	public static function getBanner($merchant_id=0,$owner='merchant')
	{		
		$criteria=new CDbCriteria();
		$criteria->condition = "owner=:owner AND meta_value1=:meta_value1 AND status=:status";		    
		$criteria->params  = array(
		  ':owner'=>$owner,
		  ':meta_value1'=>intval($merchant_id),
		  ':status'=>1
		);				
		$criteria->order = "sequence ASC";
		$model = AR_banner::model()->findAll($criteria); 
		if($model){
			$data = [];
			foreach ($model as $items) {
				$data[] = [
					'banner_id'=>$items->banner_id,
					'banner_uuid'=>$items->banner_uuid,
					'title'=>Chtml::encode($items->title),
					'banner_type'=>$items->banner_type,
					'image'=>CMedia::getImage($items->photo,$items->path)
				];
			}
			return $data;
		}
		throw new Exception( 'Banner not found' );
	}

	public static function MapsConfig($merchant_id=0,$geocoding_api = true)
	{		
		if($merchant_id>0){
			$items = OptionsTools::find([
				'merchant_map_provider','merchant_google_geo_api_key','merchant_google_maps_api_key','merchant_mapbox_access_token'
			],$merchant_id);

			$provider = isset($items['merchant_map_provider'])?$items['merchant_map_provider']:'';
			$google_geo_api_key = isset($items['merchant_google_geo_api_key'])?$items['merchant_google_geo_api_key']:'';
			$google_maps_api_key = isset($items['merchant_google_maps_api_key'])?$items['merchant_google_maps_api_key']:'';
			$mapbox_access_token = isset($items['merchant_mapbox_access_token'])?$items['merchant_mapbox_access_token']:'';
			
			MapSdk::$map_provider = $provider;
			MapSdk::setKeys(array(
			  'google.maps'=>$geocoding_api==true?$google_geo_api_key:$google_maps_api_key,
			  'mapbox'=>$mapbox_access_token
			));
			return array(
				'provider'=>MapSdk::$map_provider,
				'key'=>MapSdk::$api_key,
				'zoom'=>15,		  
				'icon'=>websiteDomain().Yii::app()->theme->baseUrl."/assets/images/marker2@2x.png",
				'icon_merchant'=>websiteDomain().Yii::app()->theme->baseUrl."/assets/images/restaurant-icon1.png",
				'icon_destination'=>websiteDomain().Yii::app()->theme->baseUrl."/assets/images/home-icon1.png",
				'default_lat'=> '34.04703',
				'default_lng'=> '-118.246860',
			  );			
		}
		return false;
	}
	
}
/*end class*/