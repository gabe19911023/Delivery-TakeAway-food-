<?php
/*
*/
class MerchantAR extends CComponent
{
	public static function tableName()
	{
		return '{{merchant}}';
	}
	
	public static function search($search='')
	{
		$where ="WHERE status='active'";
		if (!empty($search)){
			$where = " WHERE restaurant_name LIKE ".q("%$search%");
		}
		
		$stmt="
		SELECT merchant_id as id,
		restaurant_name as text
		FROM ".self::tableName()."
		$where
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$val['text'] = stripslashes(Yii::app()->input->stripClean($val['text']));
				$data[]=$val;
			}			
			return $data;
		}				
		throw new Exception('no results');		
	}
	
	public static function getSelected($ids=array())
	{		
		$data = array();
		if(is_array($ids) && count($ids)>=1){
			$stmt="
			SELECT merchant_id as id,
		    restaurant_name as text
	    	FROM ".self::tableName()."
	    	WHERE merchant_id IN (".implode($ids,",").")
			";					
			if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
				foreach ($res as $val) {					
					$data[$val['id']] = stripslashes($val['text']);
				}
			}
		}		
		return $data;
	}
	
	public static function RecentRegistered($limit=5)
	{
		$stmt="
		SELECT a.merchant_id,a.restaurant_name as merchant_name,a.status,a.logo,a.path,
		concat(a.street,' ',a.city,' ',a.state,' ',a.post_code,' ',b.country_name) as merchant_address,			
		
		IFNULL((
		 select title_trans from {{view_status_management}}
		 where
		 status=a.status and group_name='customer'
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),a.status) as status_title,
		 
		IFNULL((
		 select type_name_trans from {{view_merchant_type}}
		 where
		 type_id=a.merchant_type
		 and language=".q(Yii::app()->language)."
		 limit 0,1
		 ),'') as merchant_type	
		 
		FROM ".self::tableName()." a	
		LEFT JOIN {{location_countries}} b
		ON
		a.country_code = b.shortcode
			
		
		ORDER BY merchant_id DESC
		LIMIT 0,$limit
		";						
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){						
			return $res;
		}				
		return false;	
	}
	
	public static function getMerchantType()
	{
		/* $id = (integer)Yii::app()->merchant->merchant_id;		
		
		 $dependency = new CDbCacheDependency("
		 SELECT MAX(date_modified) FROM {{merchant}}
		 WHERE merchant_id = ".$id."
		 ");		 
			     	     
		 if(Yii::app()->params->db_cache_enabled){		 	
		 	$merchant = AR_merchant::model()->cache(1000, $dependency)->findByPk( $id );		
		 } else $merchant = AR_merchant::model()->findByPk( $id );			     
	     if($merchant){
	     	return $merchant->merchant_type;
	     }
	     return false;*/
				 
		 $dependency = CCacheData::dependency();			     	    		 
	     if($merchant = AR_merchant::model()->cache(Yii::app()->params->cache, $dependency)->findByPk( Yii::app()->merchant->merchant_id )){
	     	return $merchant->merchant_type;
	     }
	     return false;
	}
	
}
/*end class*/