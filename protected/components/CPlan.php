<?php
Class CPlan
{

	public static function listing($lang = KMRS_DEFAULT_LANGUAGE , $exclude_free_trial=false)
	{
		$and = '';
		if($exclude_free_trial){
			$and = " AND trial_period <=0";
		}
	    $stmt="
		SELECT a.package_id, a.package_uuid,
		b.title, b.description,
		a.price, 
		a.price as price_raw, 
		a.promo_price, 
		a.promo_price as promo_price_raw,
		a.package_period
		
		FROM {{plans}} a		
		LEFT JOIN {{plans_translation}} b
		ON
		a.package_id = b.package_id
		WHERE b.language=".q($lang)."
		AND a.status = 'publish'
		$and
		ORDER BY a.sequence ASC
		";					    
	    $dependency = CCacheData::dependency();
	    if($res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll() ){
	    	$data = array();
	    	foreach ($res as $val) {	    		
	    		$val['title'] = Yii::app()->input->xssClean($val['title']);	    		
	    		$val['price'] = Price_Formatter::formatNumber($val['price']);
	    		$val['promo_price'] = Price_Formatter::formatNumber($val['promo_price']);	    	
	    		$val['package_period'] = t($val['package_period']);
	    		$data[]=$val;
	    	}	    	
	    	return $data;
	    }
	    throw new Exception( 'no results' );
	}
	
	public static function Details()
	{
		$model = AR_admin_meta::model()->findAll("meta_name=:meta_name",array(
		  ':meta_name'=>'plan_features'
		));
		if($model){
			$data = array();
			foreach ($model as $item) {				
				$data[$item->meta_value1][] =  Yii::app()->input->xssClean($item->meta_value);
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function get($package_id=0)
	{
		$model = AR_plans::model()->find('package_id=:package_id', 
		array(':package_id'=> intval($package_id) )); 	
		if($model){
			return $model;
		}
		return false;
	}
	
}
/*end class*/