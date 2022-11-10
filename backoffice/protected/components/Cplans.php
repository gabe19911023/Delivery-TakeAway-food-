<?php
class Cplans
{
	
	public static function get($package_id='')
	{		
		$dependency = CCacheData::dependency();
		$model = AR_plans::model()->cache(Yii::app()->params->cache, $dependency)->find('package_id=:package_id', 
		array(':package_id'=>intval($package_id))); 
		if($model){
			return $model;
		}
		throw new Exception( 'Plans not found' );
	}
	
	public static function getByUUID($package_uuid='')
	{		
		$dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{cache}}');
		$model = AR_plans::model()->cache(Yii::app()->params->cache, $dependency)->find('package_uuid=:package_uuid', 
		array(':package_uuid'=>trim($package_uuid))); 
		if($model){
			return $model;
		}
		throw new Exception( 'Plans not found' );
	}
	
	public static function planDetails($package_id='', $lang='')
	{		
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select="a.package_id,a.package_uuid,a.plan_type,b.title,b.description,a.price,a.promo_price,
		a.package_period,a.ordering_enabled,a.item_limit,a.order_limit,a.trial_period,a.status
		";
		$criteria->join='LEFT JOIN {{plans_translation}} b on  a.package_id=b.package_id ';
		$criteria->condition = "a.package_id=:package_id AND b.language=:language";
		$criteria->params = array(
		  ':package_id'=>intval($package_id),
		  ':language'=>$lang,		  
		);
		
		$dependency = CCacheData::dependency();
		$model=AR_plans::model()->cache(Yii::app()->params->cache, $dependency)->find($criteria);
		if($model){
			return $model;
		}
		throw new Exception( 'Plans not found' );
	}
	
	public static function planPriceID($meta_name='', $package_id=0)
	{
		$dependency = CCacheData::dependency();
		$model = AR_admin_meta::model()->cache(Yii::app()->params->cache, $dependency)->find('meta_name=:meta_name AND meta_value1=:meta_value1 ', 
		  array(
		    ':meta_name'=>trim($meta_name),
		    ':meta_value1'=>intval($package_id)
		)); 
		if($model){
			return $model;
		}
		throw new Exception( 'Plans price not found' );
	}
	
	public static function getMechantSubcriptions($merchant_id=0,$is_live=0,$meta_name='subscription_stripe')
	{
		$meta = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name AND meta_value1=:meta_value1 ",array(
		     ':merchant_id'=>intval($merchant_id),
		     ':meta_name'=>$meta_name,	
		     ':meta_value1'=>$is_live
		   ));
	    if($meta){
	    	return $meta;
	    }
	    throw new Exception( 'Subscriber account not found' );
	}
	
	public static function getMerchantCustomerID($merchant_id=0,$is_live=0,$meta_name='stripe')
	{
		$meta = AR_merchant_meta::model()->find("merchant_id=:merchant_id AND meta_name=:meta_name AND meta_value=:meta_value ",array(
		     ':merchant_id'=>intval($merchant_id),
		     ':meta_name'=>$meta_name,	
		     ':meta_value'=>$is_live
		   ));
	    if($meta){
	    	return $meta;
	    }
	    throw new Exception( 'Subscriber account not found' );
	}
	
}
/*end class*/