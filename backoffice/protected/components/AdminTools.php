<?php
class AdminTools
{	
	public static function displayAdminName()
	{		
		$name = Yii::app()->user->first_name." ".Yii::app()->user->last_name;
		return $name;
	}
	
	public static function getProfilePhoto()
	{								
		$upload_path = CMedia::adminFolder();
		$avatar = CMedia::getImage(Yii::app()->user->avatar,$upload_path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('customer'));
		return $avatar;
	}
	
	public static function getLogo($filename='')
	{						
		return websiteDomain()."/".Yii::app()->theme->baseUrl."/assets/images/sample-merchant-logo@2x.png";		
	}
	
	public static function getMeta($meta_name='')
	{
		$model = AR_admin_meta::model()->find("meta_name=:meta_name",array(
		  'meta_name'=>$meta_name,		  
		));
		if($model){
			return $model;
		}
		return false;
	}
	
	public static function getPayoutSettings()
     {
     	$options = AR_admin_meta::getMeta(array('payout_request_enabled','payout_minimum_amount'));		
		$payout_request_enabled = isset($options['payout_request_enabled'])?$options['payout_request_enabled']['meta_value']:'';
		$payout_minimum_amount = isset($options['payout_minimum_amount'])?$options['payout_minimum_amount']['meta_value']:'';
		$payout_request_enabled = $payout_request_enabled==1?true:false;		
		return array(
		   'enabled'=>$payout_request_enabled,
		   'minimum_amount'=>$payout_minimum_amount,
		);
     }
		
}
/*end class*/