<?php
class MerchantTools
{
	
	public static function displayAdminName()
	{		
		$name = Yii::app()->merchant->first_name." ".Yii::app()->merchant->last_name;
		return $name;
	}
	
	public static function getProfilePhoto()
	{								
		$upload_path = CMedia::merchantFolder();
		$avatar = CMedia::getImage(Yii::app()->merchant->avatar,$upload_path,'@thumbnail',
		CommonUtility::getPlaceholderPhoto('customer'));
		return $avatar;
	}
	
	public static function getLogo($filename='')
	{						
		return websiteDomain()."/".Yii::app()->theme->baseUrl."/assets/images/sample-merchant-logo@2x.png";		
	}
	
	
	/*
	$services = integer example = 1
	*/
	public static function legacyServices($services=array())
	{
		$service_id = 1; $delivery=false;$pickup=false;$dinein=false;
		if(is_array($services) && count($services)>=1){
			foreach ($services as $id) {
				switch ($id) {
					case 1:
						$delivery=true;
						break;
				
					case 2:
						$pickup=true;
						break;
						
					case 3:
						$dinein=true;
						break;
								
					default:
						break;
				}
			}
			if($delivery && $pickup && $dinein){
				$service_id=4;
			} elseif ( $delivery && $pickup){
				$service_id=1;
			} elseif ( $delivery && $dinein){
				$service_id=5;
			} elseif ( $pickup && $dinein){
				$service_id=6;
			} elseif ( $delivery){
				$service_id=2;
			} elseif ( $pickup){
				$service_id=3;
			} elseif ( $dinein){
				$service_id=7;
			}
		}
		return $service_id;
	}
	
	/*
	@parametes 
	merchant_id = merchant id
	params = array() possible values are
	Array
	(
	    [0] => 1
	    [1] => 3
	)	
	$meta_name = different meta name
	*/
	public static function saveMerchantMeta($merchant_id=0,$params=array(),$meta_name='')
	{			
		Yii::app()->db->createCommand("DELETE FROM {{merchant_meta}}
		WHERE merchant_id=".q($merchant_id)."
		AND meta_name=".q($meta_name)."
		")->query();
		if($merchant_id>0 && is_array($params) && count($params)>=1){
			foreach ($params as $id) {
				$params = array(
				  'merchant_id'=>(integer)$merchant_id,
				  'meta_name'=>trim($meta_name),
				  'meta_value'=>trim($id)
				);
				Yii::app()->db->createCommand()->insert("{{merchant_meta}}",$params);
			}
		}
	}
	
	public static function getMerchantMeta($merchant_id=0,$meta_name='')
	{
		$stmt="
		SELECT meta_value
		FROM {{merchant_meta}}
		WHERE merchant_id = ".q($merchant_id)."
		AND 
		meta_name = ".q($meta_name)."
		ORDER BY meta_id ASC
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$data[]=$val['meta_value'];
			}
			return $data;
		}
		return false;
	}
	
	
	/*
	@parametes 
	$cuisine = array()
	Array
	(
	    [0] => 1
	    [1] => 11
	    [2] => 16	 
	)
	*/
	public static function insertCuisine($merchant_id='', $cuisine=array())
	{		
		$merchant_id = (integer)$merchant_id;
		
		Yii::app()->db->createCommand("DELETE FROM 
		{{cuisine_merchant}} WHERE merchant_id=".q($merchant_id)." ")->query();
		
		if(is_array($cuisine) && count($cuisine)>=1){
			foreach ($cuisine as $cuisine_id) {
				Yii::app()->db->createCommand()->insert("{{cuisine_merchant}}",array(
				  'merchant_id'=>(integer)$merchant_id,
				  'cuisine_id'=>(integer)$cuisine_id
				));
			}
		}
	}
	
	public static function getCuisine($merchant_id='')
	{		
		$data = CommonUtility::getDataToDropDown("{{cuisine_merchant}}",'cuisine_id','cuisine_id',"
		WHERE merchant_id=".q(intval($merchant_id))."
		");	
		return $data;
	}
	
	/*
	@parametes 
	$tag_id = array()
	
	*/
	public static function insertTag($merchant_id=0, $tag_id = array())
	{
		$merchant_id = (integer)$merchant_id;		
		Yii::app()->db->createCommand("DELETE FROM 
		{{option}} WHERE merchant_id=".q($merchant_id)."  AND  option_name='tags' ")->query();
		
		if(is_array($tag_id) && count($tag_id)>=1 && $merchant_id>0){
		   foreach ($tag_id as $tagid) {
		      Yii::app()->db->createCommand()->insert("{{option}}",array(
				  'merchant_id'=>(integer)$merchant_id,
				  'option_name'=>'tags',
				  'option_value'=>(integer)$tagid
				));
		   }
		}
	}
	
	public static function saveMerchantUser($merchant_id=0, $params=array())
	{
		$merchant_id = (integer)$merchant_id;		
		Yii::app()->db->createCommand("DELETE FROM 
		{{merchant_user}} WHERE merchant_id=".q($merchant_id)."  AND  main_account='1' ")->query();
		
		Yii::app()->db->createCommand()->insert("{{merchant_user}}",$params);
	}
		
	public static function getMerchantOptions($merchant_id=0,$option_name='')
	{
		$stmt="
		SELECT option_value
		FROM {{option}}
		WHERE merchant_id = ".q($merchant_id)."
		AND 
		option_name = ".q($option_name)."
		ORDER BY id ASC
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$data[]=$val['option_value'];
			}
			return $data;
		}
		return false;
	}
	
	/*
	@params = 
	Array
	(
	    [merchant_master_table_boooking] => 1
	    [merchant_master_disabled_ordering] => 1
	    [disabled_single_app_modules] => 1
	)
	*/
	public static function savedOptions($merchant_id=0,$params=array())
	{
		if($merchant_id>0 && is_array($params) && count($params)>=1){
			foreach ($params as $key=>$val) {				
				$option=AR_option::model()->find('merchant_id=:merchant_id and option_name=:option_name', 
				array(
				  ':merchant_id'=>$merchant_id,
				  ':option_name'=>$key
				));						
				if(!$option){
					$option=new AR_option;
				}
				$option->merchant_id = $merchant_id;
				$option->option_name=$key;
				$option->option_value=$val;				
				$option->save();
			}			
		}
	}
	
	/*
	@params = 
	array(
	 'merchant_master_table_boooking','merchant_master_disabled_ordering','disabled_single_app_modules'
	)
	*/
	public static function getOptions($merchant_id=0, $params=array())
	{
		$data = array();
		$criteria = new CDbCriteria();
		$criteria->condition='merchant_id=:merchant_id';
        $criteria->params=array(':merchant_id'=>$merchant_id);
		$criteria->addInCondition('option_name', (array)$params);
		if($option = AR_option::model()->findAll($criteria)){
			foreach ($option as $val) {		
				$data[$val->option_name]  = $val->option_value;
			}		
			return $data;		
		}
		return false;
	}
	
	public static function MerchantDeleteALl($merchant_id=0)
	{
		$merchant_id = (integer)$merchant_id;
		if($merchant_id>0){
			
			Yii::app()->db->createCommand("DELETE FROM {{merchant_meta}}
			WHERE merchant_id=".q($merchant_id)."			
			")->query();
		
			Yii::app()->db->createCommand("DELETE FROM 
		    {{cuisine_merchant}} WHERE merchant_id=".q($merchant_id)." ")->query();
						
		    Yii::app()->db->createCommand("DELETE FROM 
		    {{option}} WHERE merchant_id=".q($merchant_id)."  ")->query();				

		    Yii::app()->db->createCommand("DELETE FROM 
		    {{merchant_user}} WHERE merchant_id=".q($merchant_id)." ")->query();						    
		    
		    Yii::app()->db->createCommand("DELETE FROM 
		    {{merchant_meta}} WHERE merchant_id=".q($merchant_id)." ")->query();				
			
		}
	}
		
}
/*end class*/