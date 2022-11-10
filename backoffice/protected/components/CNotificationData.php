<?php
class CNotificationData
{
	public static function getRealtimeSettings()
	{
		$realtime = AR_admin_meta::getMeta(array('realtime_provider','realtime_app_enabled',
		  'pusher_app_id','pusher_key','pusher_secret','pusher_cluster','ably_apikey',
		  'piesocket_clusterid','piesocket_api_key','piesocket_api_secret'
		));						
		
		$realtime_app_enabled = isset($realtime['realtime_app_enabled'])?$realtime['realtime_app_enabled']['meta_value']:'';
		$realtime_provider = isset($realtime['realtime_provider'])?$realtime['realtime_provider']['meta_value']:'';
		
		$pusher_app_id = isset($realtime['pusher_app_id'])?$realtime['pusher_app_id']['meta_value']:'';
		$pusher_key = isset($realtime['pusher_key'])?$realtime['pusher_key']['meta_value']:'';
		$pusher_secret = isset($realtime['pusher_secret'])?$realtime['pusher_secret']['meta_value']:'';
		$pusher_cluster = isset($realtime['pusher_cluster'])?$realtime['pusher_cluster']['meta_value']:'';				
		
		$ably_apikey = isset($realtime['ably_apikey'])?$realtime['ably_apikey']['meta_value']:'';
		
		$piesocket_clusterid = isset($realtime['piesocket_clusterid'])?$realtime['piesocket_clusterid']['meta_value']:'';
		$piesocket_api_key = isset($realtime['piesocket_api_key'])?$realtime['piesocket_api_key']['meta_value']:'';
		$piesocket_api_secret = isset($realtime['piesocket_api_secret'])?$realtime['piesocket_api_secret']['meta_value']:'';
						
		if($realtime_app_enabled==1){
			return array(
			  'notication_channel'=>'',
			  'notification_event'=>'',
			  'provider'=>$realtime_provider,
			  'app_id'=>$pusher_app_id,
			  'key'=>$pusher_key,
			  'secret'=>$pusher_secret,
			  'cluster'=>$pusher_cluster,
			  'ably_apikey'=>$ably_apikey,
			  'piesocket_clusterid'=>$piesocket_clusterid,
			  'piesocket_api_key'=>$piesocket_api_key,
			  'piesocket_api_secret'=>$piesocket_api_secret,
			);
		} else throw new Exception( 'realtime settings disabled' );
	}
	
	public static function interestListing($meta_name='')
	{		
		$criteria=new CDbCriteria();	    
	    $criteria->condition = "meta_name=:meta_name";		    
		$criteria->params  = array(
		  ':meta_name'=>$meta_name,		  
		);
		$criteria->order ="date_modified ASC";
		$model = AR_admin_meta::model()->findAll($criteria); 				
		if($model){
			$data = array();
			foreach ($model as $item) {
				$data[$item->meta_value1] = $item->meta_value;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getData($notification_uuid='')
	{
		$model = AR_notifications::model()->find("notification_uuid=:notification_uuid",array(
		  ':notification_uuid'=>$notification_uuid
		));
		if($model){
			return $model;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getList($notication_channel='' , $limit=30)
	{
		PrettyDateTime::$category='backend';

		$criteria=new CDbCriteria();
		$criteria->condition = "notication_channel=:notication_channel";
		$criteria->params  = array(		  
		  ':notication_channel'=>$notication_channel
		);
		$criteria->order = "date_created DESC";
		$criteria->limit = $limit;
		
		$model = AR_notifications::model()->findAll($criteria);  
		if($model){
			$data = array();
			$count = AR_notifications::model()->count($criteria); 
			foreach ($model as $item) {
				
				$image=''; $url = '';
				if($item->image_type=="icon"){
					$image = !empty($item->image)?$item->image:'';
				} else {
					if(!empty($item->image)){
						$image = CMedia::getImage($item->image,$item->image_path,
						Yii::app()->params->size_image_thumbnail ,
						CommonUtility::getPlaceholderPhoto('item') );
					}
				}
				
				$params = !empty($item->message_parameters)?json_decode($item->message_parameters,true):'';
				
				$data[]=array(
				  'notification_type'=>$item->notification_type,
				  'message'=>t($item->message,(array)$params),
				  'date'=>PrettyDateTime::parse(new DateTime($item->date_created)),				  
				  'image_type'=>$item->image_type,
				  'image'=>$image,
				  'url'=>$url
				);
			}
			return array(
			  'count'=>$count,
			  'data'=>$data
			);
		}
		throw new Exception( 'no results' );
	}
	
	public static function getUserSettings($user_id='', $user_type='')
	{		
		$model = AR_device::model()->find("user_id=:user_id AND user_type=:user_type",array(
	      ':user_id'=>intval($user_id),
	      ':user_type'=>$user_type
	    ));			   
	    if($model){	    	
	    	return array(
	    	  'enabled'=>$model->enabled,
	    	  'device_token'=>$model->device_token,
	    	  'interest'=>CNotificationData::getDeviceInterest($model->device_id)
	    	);
	    }
	    throw new Exception( 'no results' );
	}
	
	public static function getDeviceInterest($device_id='')
	{
		
		$criteria=new CDbCriteria();
		$criteria->alias = "a";			
		$criteria->select = "a.meta_value, b.device_id";
		$criteria->join='LEFT JOIN {{device}} b on  a.device_id=b.device_id ';		
		$criteria->condition = "b.device_id=:device_id AND meta_name=:meta_name ";
		$criteria->params = array(
		  ':device_id'=>$device_id,
		  ':meta_name'=>'interest'
		);
		$model=AR_device_meta::model()->findAll($criteria);		
		if($model){
			$data = array();
			foreach ($model as $item) {			
				$data[] = $item->meta_value;
			}
			return $data;
		}
		return array();
	}
	
	/*public static function getDeviceInterest($device_token='')
	{
		
		$criteria=new CDbCriteria();
		$criteria->alias = "a";			
		$criteria->select = "a.meta_value, b.device_id";
		$criteria->join='LEFT JOIN {{device}} b on  a.device_id=b.device_id ';		
		$criteria->condition = "b.device_token=:device_token AND meta_name=:meta_name ";
		$criteria->params = array(
		  ':device_token'=>$device_token,
		  ':meta_name'=>'interest'
		);
		$model=AR_device_meta::model()->findAll($criteria);
		if($model){
			$data = array();
			foreach ($model as $item) {			
				$data[] = $item->meta_value;
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}*/
	
	/*public static function getDeviceInterest($interest=array())
	{						
		$criteria=new CDbCriteria();
		$criteria->alias = "a";			
		$criteria->select = "device_token , b.interest";
		$criteria->join='LEFT JOIN {{device_interest}} b on  a.device_id=b.device_id ';
		$criteria->condition = "a.status=:status";
		$criteria->params = array(
		  ':status'=>'active',		  
		);
		$criteria->addInCondition('b.interest', (array) $interest );
		$model=AR_device::model()->findAll($criteria);		
		if($model){
			$data = array();
			foreach ($model as $item) {
				$data[] = $item->device_token;
			}
			return $data;
		}
		return false;
		
	}*/
}
/*end class*/